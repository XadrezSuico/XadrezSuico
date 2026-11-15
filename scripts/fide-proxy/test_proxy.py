import importlib.util
import json
import os
import ssl
import subprocess
import tempfile
import threading
import unittest
import urllib.error
import urllib.request
from unittest.mock import patch, MagicMock

spec = importlib.util.spec_from_file_location('fide_proxy', os.path.join(os.path.dirname(__file__), 'proxy.py'))
proxy = importlib.util.module_from_spec(spec)
spec.loader.exec_module(proxy)


class RelayTest(unittest.TestCase):
    def test_caches_valid_html_and_uses_fixed_upstream_with_timeouts(self):
        relay = proxy.Relay({'allowed_ips': ['127.0.0.1'], 'token': 'a' * 40})
        process = MagicMock(returncode=0)
        process.communicate.return_value = (b'<h1 class="player-title">Test</h1>', b'')
        with patch.object(proxy.subprocess, 'Popen', return_value=process) as popen:
            self.assertEqual(relay.fetch('123'), relay.fetch('123'))
            self.assertEqual(1, popen.call_count)
            args = popen.call_args[0][0]
            self.assertEqual('https://ratings.fide.com/profile/123', args[-1])
            self.assertIn('--max-time', args)
            self.assertNotIn('--insecure', args)

    def test_failed_upstream_is_not_cached(self):
        relay = proxy.Relay({'allowed_ips': ['127.0.0.1'], 'token': 'a' * 40})
        process = MagicMock(returncode=28)
        process.communicate.return_value = (b'partial', b'timeout')
        with patch.object(proxy.subprocess, 'Popen', return_value=process):
            with self.assertRaises(IOError):
                relay.fetch('123')
        self.assertEqual({}, relay.cache)

    def test_busy_request_is_rejected(self):
        relay = proxy.Relay({'allowed_ips': ['127.0.0.1'], 'token': 'a' * 40})
        relay.LOCK_WAIT_SECONDS = 0.05
        relay.lock.acquire()
        with self.assertRaises(RuntimeError):
            relay.fetch('123')
        relay.lock.release()

    def test_concurrent_same_profile_waits_and_reuses_one_upstream_response(self):
        relay = proxy.Relay({'allowed_ips': ['127.0.0.1'], 'token': 'a' * 40})
        started = threading.Event()
        body = b'<h1 class="player-title">Test</h1>'
        def communicate():
            started.set()
            proxy.time.sleep(0.15)
            return body, b''
        process = MagicMock(returncode=0)
        process.communicate.side_effect = communicate
        results = []
        with patch.object(proxy.subprocess, 'Popen', return_value=process) as popen:
            worker = threading.Thread(target=lambda: results.append(relay.fetch('123')))
            worker.start()
            self.assertTrue(started.wait(1))
            results.append(relay.fetch('123'))
            worker.join(2)
            self.assertFalse(worker.is_alive())
            self.assertEqual([body, body], results)
            self.assertEqual(1, popen.call_count)


class HttpsTest(unittest.TestCase):
    @classmethod
    def setUpClass(cls):
        cls.tmp = tempfile.TemporaryDirectory()
        cls.cert = cls.tmp.name + '/server.crt'
        key = cls.tmp.name + '/server.key'
        subprocess.run(['openssl', 'req', '-x509', '-newkey', 'rsa:2048', '-nodes',
                        '-days', '1', '-subj', '/CN=localhost', '-addext',
                        'subjectAltName=DNS:localhost', '-keyout', key, '-out', cls.cert],
                       check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        cls.server = proxy.Server(('127.0.0.1', 0), proxy.Handler)
        cls.server.relay = proxy.Relay({'allowed_ips': ['127.0.0.1'], 'token': 'a' * 40})
        cls.server.context = ssl.SSLContext(ssl.PROTOCOL_TLS_SERVER)
        cls.server.context.load_cert_chain(cls.cert, key)
        cls.thread = threading.Thread(target=cls.server.serve_forever, daemon=True)
        cls.thread.start()
        cls.url = 'https://localhost:%s' % cls.server.server_port
        cls.context = ssl.create_default_context(cafile=cls.cert)

    @classmethod
    def tearDownClass(cls):
        cls.server.shutdown()
        cls.server.server_close()
        cls.tmp.cleanup()

    def request(self, path, token='a' * 40):
        request = urllib.request.Request(self.url + path, headers={'Authorization': 'Bearer ' + token})
        return urllib.request.urlopen(request, context=self.context, timeout=3)

    def test_health_requires_token(self):
        self.assertEqual({'status': 'ok'}, json.load(self.request('/health')))
        with self.assertRaises(urllib.error.HTTPError) as error:
            self.request('/health', 'wrong')
        self.assertEqual(401, error.exception.code)
        error.exception.close()

    def test_rejects_arbitrary_urls_and_queries(self):
        for path in ['/https://example.org', '/profile/123?url=http://example.org', '/profile/../health']:
            with self.assertRaises(urllib.error.HTTPError) as error:
                self.request(path)
            self.assertEqual(404, error.exception.code)
            error.exception.close()

    def test_forwards_html_and_returns_gateway_error_on_failure(self):
        with patch.object(self.server.relay, 'fetch', return_value=b'<h1>Test</h1>'):
            self.assertEqual(b'<h1>Test</h1>', self.request('/profile/123').read())
        with patch.object(self.server.relay, 'fetch', side_effect=IOError):
            with self.assertRaises(urllib.error.HTTPError) as error:
                self.request('/profile/123')
            self.assertEqual(502, error.exception.code)
            error.exception.close()

    def test_rejects_source_even_with_forged_forwarded_header(self):
        import http.client
        import socket
        conn = http.client.HTTPSConnection('localhost', self.server.server_port,
                                          context=self.context, source_address=('127.0.0.2', 0), timeout=3)
        with self.assertRaises((OSError, ssl.SSLError, http.client.RemoteDisconnected)):
            conn.request('GET', '/health', headers={'Authorization': 'Bearer ' + 'a' * 40,
                                                   'X-Forwarded-For': '127.0.0.1'})
            conn.getresponse()
        conn.close()
