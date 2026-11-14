#!/usr/bin/env python
"""Restricted FIDE HTML relay. Stdlib only; Python 2.7.9+/3 supported.
TLS verification upstream is performed by curl, using the OS trust store.
"""
from __future__ import print_function
import hmac
import json
import os
import re
import socket
import ssl
import subprocess
import sys
import threading
import time
try:
    from http.server import BaseHTTPRequestHandler, HTTPServer
    from socketserver import ThreadingMixIn
except ImportError:
    from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer
    from SocketServer import ThreadingMixIn


class Relay(object):
    def __init__(self, config):
        self.allowed = set(config['allowed_ips'])
        if not self.allowed or any(not re.match(r'^\d+\.\d+\.\d+\.\d+$', ip) for ip in self.allowed):
            raise ValueError('Explicit IPv4 allowlist required')
        self.token = config['token'].encode('ascii')
        if len(self.token) < 32:
            raise ValueError('Token must contain at least 32 characters')
        self.lock = threading.Lock()
        self.cache = {}
        self.last_fetch = 0

    def fetch(self, fide_id):
        # Serialize upstream calls and cache repeat requests to reduce FIDE load.
        if not self.lock.acquire(False):
            raise RuntimeError("Busy")
        try:
            now = time.time()
            cached = self.cache.get(fide_id)
            if cached and now - cached[0] < 3600:
                return cached[1]
            time.sleep(max(0, 1 - (now - self.last_fetch)))
            self.last_fetch = time.time()
            result = subprocess.Popen([
                'curl', '--silent', '--show-error', '--fail', '--proto', '=https',
                '--connect-timeout', '5', '--max-time', '20', '--max-filesize', '2097152',
                '--user-agent', 'Mozilla/5.0 (compatible; XadrezSuico/1.0)',
                'https://ratings.fide.com/profile/' + fide_id,
            ], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
            body, _ = result.communicate()
            if result.returncode or len(body) > 2097152:
                raise IOError('FIDE unavailable')
            if b'player-title' not in body and b'No record found' not in body and b'Player not found' not in body:
                raise IOError('Unexpected FIDE response')
            if len(self.cache) >= 128:
                self.cache.clear()
            self.cache[fide_id] = (time.time(), body)
            return body

        finally:
            self.lock.release()

class Handler(BaseHTTPRequestHandler):
    server_version = 'FideRelay/1.0'
    sys_version = ''

    def log_message(self, fmt, *args):
        # Do not log authorization headers or HTML/player data.
        sys.stdout.write('%s status=%s\n' % (time.strftime('%Y-%m-%dT%H:%M:%SZ', time.gmtime()), args[1] if len(args) > 1 else '-'))
        sys.stdout.flush()

    def reply(self, status, body, content_type='text/plain; charset=utf-8'):
        self.send_response(status)
        self.send_header('Content-Type', content_type)
        self.send_header('Content-Length', str(len(body)))
        self.send_header('Cache-Control', 'no-store')
        self.send_header('X-Content-Type-Options', 'nosniff')
        self.end_headers()
        self.wfile.write(body)

    def do_GET(self):
        relay = self.server.relay
        # Trust only socket peer IP. Never trust X-Forwarded-For.
        if self.client_address[0] not in relay.allowed:
            self.reply(403, b'Forbidden')
            return
        auth = self.headers.get('Authorization', '').encode('utf-8')
        if not hmac.compare_digest(auth, b'Bearer ' + relay.token):
            self.reply(401, b'Unauthorized')
            return
        if self.path == '/health':
            self.reply(200, b'{"status":"ok"}', 'application/json')
            return
        match = re.match(r'^/profile/([1-9][0-9]{0,11})$', self.path)
        if not match:
            self.reply(404, b'Not found')
            return
        try:
            self.reply(200, relay.fetch(match.group(1)), 'text/html; charset=utf-8')
        except RuntimeError:
            self.reply(503, b'Busy; retry later')
        except (IOError, OSError):
            self.reply(502, b'FIDE unavailable; retry later')


class Server(ThreadingMixIn, HTTPServer):
    daemon_threads = True
    allow_reuse_address = True

    def get_request(self):
        sock, address = self.socket.accept()
        sock.settimeout(5)
        # Reject unauthorized source before doing TLS or creating worker threads.
        if address[0] not in self.relay.allowed:
            sock.close()
            raise socket.error('Source not allowed')
        try:
            return self.context.wrap_socket(sock, server_side=True), address
        except Exception:
            sock.close()
            raise


def main():
    with open(os.environ.get('FIDE_PROXY_CONFIG', 'config.json')) as handle:
        config = json.load(handle)
    port = int(config.get('port', 8443))
    if not 1024 <= port <= 65535:
        raise ValueError('Use a nonprivileged port')
    server = Server((config['bind'], port), Handler)
    server.relay = Relay(config)
    server.context = ssl.SSLContext(ssl.PROTOCOL_TLSv1_2)
    server.context.options |= ssl.OP_NO_COMPRESSION
    server.context.load_cert_chain(config['certificate'], config['private_key'])
    server.serve_forever()


if __name__ == '__main__':
    main()
