#!/usr/bin/env python
"""Apply the approved FEXPAR header limit without changing PHP or request bodies."""
from __future__ import print_function
import datetime
import os
import re
import shutil
import subprocess

INCLUDE = '/etc/nginx/conf.d/xadrezsuico-header-buffers.inc'
FILES = ['/etc/nginx/conf.d/ssl.conf', '/etc/nginx/conf.d/xadrezsuico.fexpar.com.br.conf']
DIRECTIVE = 'include conf.d/xadrezsuico-header-buffers.inc;'
CONTENT = '# FEXPAR: bounded header buffers for accumulated browser cookies.\nlarge_client_header_buffers 4 16k;\n'


def main():
    backup = '/var/lib/nginx/header-buffers-backup-' + datetime.datetime.utcnow().strftime('%Y%m%dT%H%M%S%f')
    os.makedirs(backup)
    originals = {}
    for path in FILES + [INCLUDE]:
        originals[path] = open(path).read() if os.path.exists(path) else None
        if originals[path] is not None:
            shutil.copy2(path, os.path.join(backup, os.path.basename(path)))
    updated = {}
    for path in FILES:
        text = originals[path]
        if DIRECTIVE not in text:
            text, count = re.subn(r'(\s*server_name\s+[^;]+;)', r'\1\n    ' + DIRECTIVE, text, count=1)
            if count != 1:
                raise RuntimeError('Expected a server_name in ' + path)
        updated[path] = text
    updated[INCLUDE] = CONTENT
    try:
        for path, text in updated.items():
            with open(path, 'w') as handle:
                handle.write(text)
        subprocess.check_call(['sudo', '-n', '/etc/init.d/nginx', 'configtest'])
        subprocess.check_call(['sudo', '-n', '/etc/init.d/nginx', 'reload'])
    except Exception:
        for path, text in originals.items():
            if text is None:
                if os.path.exists(path):
                    os.unlink(path)
            else:
                with open(path, 'w') as handle:
                    handle.write(text)
        subprocess.call(['sudo', '-n', '/etc/init.d/nginx', 'configtest'])
        subprocess.call(['sudo', '-n', '/etc/init.d/nginx', 'reload'])
        raise
    print('Nginx header buffers applied; backup: ' + backup)


if __name__ == '__main__':
    main()
