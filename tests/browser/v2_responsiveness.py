"""Local v2 layout regression scenarios using real templates/assets and synthetic data.

Start an isolated Chrome with --headless --remote-debugging-port=9223 first.
Run: python3 tests/browser/v2_responsiveness.py
Requires the Python websockets package. No application or production DB is used.
This checks shared layout behavior; it does not render every business page with live data.
"""
import asyncio
import json
import re
import tempfile
import urllib.request
from pathlib import Path
import websockets

ROOT = Path(__file__).resolve().parents[2]
OUT = Path(tempfile.gettempdir()) / 'xadrez-v2-responsive-results'
OUT.mkdir(exist_ok=True)

def read(path):
    return (ROOT / path).read_text()

def fixture(name):
    long = 'EventoMuitoLongoSemEspacos' * 8
    topbar = read('resources/views/layouts/v2/partials/topbar.blade.php')
    topbar = topbar.replace('@auth', '').replace('@endauth', '').replace('@csrf', '')
    topbar = re.sub(r'\{\{.*?Auth::user\(\)->name.*?\}\}', 'Nome do usuário ' * 12, topbar)
    topbar = re.sub(r'\{\{.*?\}\}', '#', topbar)
    sidebar = '<aside id="v2-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform bg-brand-sidebar text-white transition-transform duration-200 lg:translate-x-0"><div class="flex h-16 items-center px-4">Sistema</div><nav class="overflow-y-auto px-3 py-4">' + ''.join('<p><a href="#">Item do menu %s</a></p>' % i for i in range(24)) + '</nav></aside>'
    button = '<a href="#" role="button" class="inline-flex items-center rounded-lg px-3 py-2 bg-brand text-white">' + long + '</a>'
    tabs = '<nav class="v2-event-shell overflow-x-auto"><ul class="flex min-w-max">' + ''.join('<li class="whitespace-nowrap px-3 py-2">Configurações do evento %s</li>' % i for i in range(12)) + '</ul></nav>'
    table = '<table id="review-table" class="table-responsive table" style="width:100%"><thead><tr>' + ''.join('<th>Coluna %s</th>' % i for i in range(12)) + '</tr></thead><tbody>'
    table += ''.join('<tr>' + ''.join('<td style="white-space:nowrap">Conteúdo extenso %s</td>' % i for i in range(12)) + '</tr>' for _ in range(4)) + '</tbody></table>'
    columns = ''.join('<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3"><label>Campo %s</label><input class="form-control" value="%s"><a class="btn btn-success">%s</a></div>' % (i, long, long) for i in range(4))
    cases = {
        'native': '<div class="v2-event-shell flex flex-wrap gap-3"><div><h1>' + long + '</h1></div><div>' + button + '</div></div>' + tabs + '<div class="v2-event-native"><div class="v2-action-grid v2-action-grid--3">' + ''.join('<a class="v2-action-card"><span class="v2-action-card__label">'+long+'</span></a>' for _ in range(6)) + '</div></div>',
        'forms': '<div class="v2-legacy-content"><div class="row">'+columns+'</div><div class="box"><div class="box-body">'+table+'</div></div></div>',
        'report': '<div class="v2-legacy-content">'+table+'</div>',
        'modal': '<div class="v2-legacy-content"><div class="modal in" role="dialog"><div class="modal-dialog"><div class="modal-content"><div class="modal-body"><h2>'+long+'</h2><textarea class="form-control">'+long+'</textarea>'+button+'</div></div></div></div></div>',
    }
    assets = ['css/app-v2.css', 'css/v2/event-legacy-scoped.css', 'css/v2/event-v2-native.css', 'vendor/datatables/datatables.min.css', 'vendor/datatables/Responsive-2.2.2/css/responsive.dataTables.min.css', 'css/v2/responsive.css']
    head = '<script>window.reviewErrors=[];window.addEventListener("error",function(e){if(e.message)window.reviewErrors.push(e.message);});</script><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">' + ''.join('<link rel="stylesheet" href="'+(ROOT/'public'/a).as_uri()+'">' for a in assets)
    scripts = ''.join('<script src="'+(ROOT/'public'/a).as_uri()+'"></script>' for a in ['vendor/adminlte/vendor/jquery/dist/jquery.min.js', 'vendor/datatables/datatables.min.js', 'vendor/datatables/DataTables-1.10.18/js/dataTables.bootstrap.min.js', 'vendor/datatables/Responsive-2.2.2/js/dataTables.responsive.min.js', 'js/v2/responsive.js'])
    if name == 'report': scripts += '<script>jQuery("#review-table").DataTable({responsive:true});</script>'
    if name == 'login':
        login = read('resources/views/auth/login.blade.php')
        login = login.split("@section('content')", 1)[1].rsplit('@endsection', 1)[0]
        login = re.sub(r'@php\b.*?@endphp', '', login, flags=re.S)
        login = re.sub(r'@if\(\$companyHtml\).*?@endif', '', login, flags=re.S)
        login = re.sub(r'@if \(\$errors.*?@endif', '', login, flags=re.S)
        login = re.sub(r'@component\([^\n]+\)', '<div class="overflow-hidden rounded-xl border border-purple-100 bg-white shadow-sm"><div class="p-4 sm:p-6">', login).replace('@endcomponent', '</div></div>').replace('@csrf', '')
        login = re.sub(r"\{\{.*?\}\}", 'SistemaMuitoLongoSemEspacos' * 4, login)
        guest = read('resources/views/layouts/v2/guest.blade.php').replace("@include('layouts.v2.partials.head')", head).replace("@yield('content')", login)
        guest = re.sub(r'@stack\([^\n]+\)|@yield\([^\n]+\)', '', guest)
        guest = re.sub(r'\{\{.*?\}\}', '', guest)
        path = OUT / (name + '.html'); path.write_text(guest); return path
    html = read('resources/views/layouts/v2/app.blade.php')
    html = re.sub(r'\{\{--.*?--\}\}', '', html, flags=re.S)
    html = html.replace("@include('layouts.v2.partials.head')", head).replace("@include('layouts.v2.partials.sidebar')", sidebar).replace("@include('layouts.v2.partials.topbar')", topbar).replace("@include('layouts.v2.partials.scripts')", scripts)
    html = re.sub(r"@hasSection\('page-header'\).*?@endif", '', html, flags=re.S)
    html = html.replace("@include('layouts.v2.partials.alerts')", '').replace("@yield('content')", cases[name])
    html = re.sub(r'\{\{.*?\}\}', '', html)
    path = OUT / (name + '.html')
    path.write_text(html)
    return path

class CDP:
    def __init__(self, ws): self.ws, self.seq = ws, 0
    async def call(self, method, params=None):
        self.seq += 1
        await self.ws.send(json.dumps({'id':self.seq,'method':method,'params':params or {}}))
        while True:
            msg = json.loads(await self.ws.recv())
            if msg.get('id') == self.seq:
                if 'error' in msg: raise RuntimeError(msg['error'])
                return msg.get('result', {})
    async def js(self, code):
        result = await self.call('Runtime.evaluate', {'expression':code,'returnByValue':True,'awaitPromise':True})
        if 'exceptionDetails' in result: raise RuntimeError(result['exceptionDetails'])
        return result.get('result', {}).get('value')

async def main():
    tabs = json.load(urllib.request.urlopen('http://127.0.0.1:9223/json'))
    target = next(t for t in tabs if t['type'] == 'page')
    checks = []
    async with websockets.connect(target['webSocketDebuggerUrl'], max_size=8_000_000) as ws:
        c = CDP(ws)
        await c.call('Page.enable')
        for name in ['native','forms','report','modal','login']:
            path = fixture(name)
            for width, height in [(320,844),(375,844),(640,900),(768,1024),(992,900),(1024,768),(1280,900),(1920,1080),(844,390)]:
                await c.call('Emulation.setDeviceMetricsOverride', {'width':width,'height':height,'deviceScaleFactor':1,'mobile':False})
                await c.call('Page.navigate', {'url':path.as_uri()})
                for _ in range(50):
                    if await c.js("document.readyState === 'complete' && (!!document.getElementById('v2-app') || document.body.classList.contains('v2-guest'))"): break
                    await asyncio.sleep(.1)
                await asyncio.sleep(.2)
                result = await c.js("""(() => ({width:innerWidth,documentWidth:document.documentElement.scrollWidth,errors:window.reviewErrors||[],tableWrappers:document.querySelectorAll('.v2-table-scroll').length,columnWidth:document.querySelector('.col-sm-6')?.getBoundingClientRect().width,modalWidth:document.querySelector('.modal-dialog')?.getBoundingClientRect().width,sidebarHidden:document.getElementById('v2-sidebar')?.inert}))()""")
                assert not result['errors'], (name,width,result['errors'])
                assert result['documentWidth'] <= width + 1, (name,width,result)
                if name == 'modal': assert result['modalWidth'] <= width, result
                if name == 'forms':
                    available = await c.js("document.querySelector('.v2-legacy-content .row').getBoundingClientRect().width")
                    fraction = 1 if width < 768 else .5 if width < 992 else 1/3 if width < 1200 else .25
                    assert abs(result['columnWidth'] - available*fraction) < 2, (name,width,result,available)
                if width < 1024 and name != 'login':
                    assert result['sidebarHidden'], result
                    await c.js("document.getElementById('v2-sidebar-toggle').click()")
                    assert await c.js("document.body.classList.contains('v2-sidebar-open') && document.getElementById('v2-sidebar-toggle').getAttribute('aria-expanded') === 'true'")
                    await c.js("document.dispatchEvent(new KeyboardEvent('keydown',{key:'Escape'}))")
                    assert await c.js("document.getElementById('v2-sidebar').inert && !document.body.classList.contains('v2-sidebar-open')")
                checks.append({'scenario':name,**result})
                if width == 375:
                    import base64
                    shot = await c.call('Page.captureScreenshot', {'format':'png'})
                    (OUT/(name+'-375.png')).write_bytes(base64.b64decode(shot['data']))
        await c.call('Page.navigate', {'url':fixture('forms').as_uri()})
        await asyncio.sleep(.4)
        # A resize must clear the mobile overlay and restore desktop navigation.
        await c.call('Emulation.setDeviceMetricsOverride', {'width':375,'height':844,'deviceScaleFactor':1,'mobile':False})
        await c.js("document.getElementById('v2-sidebar-toggle').click()")
        await c.call('Emulation.setDeviceMetricsOverride', {'width':1280,'height':900,'deviceScaleFactor':1,'mobile':False})
        await asyncio.sleep(.2)
        assert await c.js("!document.getElementById('v2-sidebar').inert && !document.body.classList.contains('v2-sidebar-open')")
        # Dynamically inserted tables must get one scroll wrapper, without mutation loops.
        await c.js("document.querySelector('main').insertAdjacentHTML('beforeend','<table id=dynamic-review><tr><td>Dinâmico</td></tr></table>')")
        await asyncio.sleep(.2)
        assert await c.js("document.getElementById('dynamic-review').parentElement.className === 'v2-table-scroll' && document.querySelectorAll('#dynamic-review').length === 1")
    (OUT/'results.json').write_text(json.dumps(checks,indent=2))
    print('PASS:',len(checks),'browser scenarios; sidebar resize and dynamic tables checked. Results:',OUT)

if __name__ == '__main__': asyncio.run(main())
