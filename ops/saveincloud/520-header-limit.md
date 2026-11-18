# Correção aplicada: Cloudflare 520 no proxy FEXPAR

Diagnóstico em 18/11/2026:
- Nginx 1.22.0, nó 121745.
- Às 21:19:36 e 21:19:37 UTC: `client sent too large header field while processing HTTP/2 connection`.
- As conexões são encerradas antes de encaminhar a requisição ao Apache/Laravel.
- Configuração herdada: `large_client_header_buffers 4 2k`.
- GET público `/inscricao/787/confirmacao`: sem cookie retorna 200; cookies sintéticos de 3.000 e 6.000 bytes retornam 520.

Alteração autorizada e aplicada em 18/11/2026:
1. Copiar o fragmento `xadrezsuico-header-buffers.inc` para `/etc/nginx/conf.d/`.
2. Adicionar `include conf.d/xadrezsuico-header-buffers.inc;` dentro dos blocos `server` de `/etc/nginx/conf.d/ssl.conf` e `/etc/nginx/conf.d/xadrezsuico.fexpar.com.br.conf`.
3. Salvar backup dos dois arquivos e do fragmento, caso exista.
4. Executar `sudo -n /etc/init.d/nginx configtest`; apenas se passar, executar `sudo -n /etc/init.d/nginx reload`.
5. Repetir os GETs com cookies sintéticos de 0, 3.000 e 6.000 bytes. Esperado: HTTP 200 nos três.
6. Se houver falha de configuração ou regressão, restaurar os backups e recarregar a configuração anterior.

O ajuste amplia o limite por campo de 2 KB para 16 KB nos servidores HTTP do domínio e HTTPS compartilhado. São até quatro buffers, alocados quando necessários; o limite permanece finito. O escopo do bloco HTTPS inclui os outros domínios que usam esse mesmo bloco `server`.

Validação após aplicação:
- `configtest`: configuração válida; `reload`: OK.
- Confirmação e login: HTTP 200 com cookies sintéticos de 0, 3.000 e 6.000 bytes (seis requisições).
- Backup: `/var/lib/nginx/header-buffers-backup-20261118T220915154979`.
- Mitigação PHP/Apache preservada: progresso de upload desativado; `memory_limit=-1` administrativo; 32 MB por requisição e 16 MB por arquivo; `LimitRequestBody 33554432`.
- Nenhum cadastro, inscrição ou sessão foi alterado.

Para reaplicar após recriar o nó, executar `python - < ops/saveincloud/fix-nginx-header-buffers.py` no Nginx com as permissões administrativas de configtest/reload. O script é compatível com Python 2/3, mantém backup e restaura os arquivos se a validação ou recarga falhar.

Referências: [Nginx — buffers de cabeçalhos](https://nginx.org/en/docs/http/ngx_http_core_module.html#large_client_header_buffers), [Cloudflare — erro 520](https://developers.cloudflare.com/support/troubleshooting/http-status-codes/cloudflare-5xx-errors/error-520/).
