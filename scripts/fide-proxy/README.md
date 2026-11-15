# Proxy FIDE

Relay HTTPS exclusivo para `GET /profile/{codigo_fide}`. Não aceita URL arbitrária,
redirecionamentos nem encaminha cookies. TLS verificado também no acesso à FIDE.
A lista de IPs usa a origem real do socket, nunca `X-Forwarded-For` ou os IPs do
Cloudflare. `/health` exige a mesma origem e token dos perfis.

## Instalação

Copie `proxy.py` e `run.sh` para um diretório privado no servidor de saída.
Requer Python com SSLContext e hmac.compare_digest (Python 3 recomendado;
compatível com o Python 2.7 mantido pelo fornecedor do ambiente atual), curl,
flock, OpenSSL e cron. Não instala dependências Python.

Crie `config.json` a partir do exemplo, com permissão 600, um token aleatório,
IP privado de escuta e IP real da aplicação. Gere um certificado TLS com SAN
para o endereço de conexão; mantenha a chave privada apenas no proxy e copie
somente o certificado público para a aplicação. Configure:

```
FIDE_PROXY_ENABLED=true
FIDE_PROXY_HOST=10.100.42.79
FIDE_PROXY_PORT=8443
FIDE_PROXY_TOKEN=<mesmo token privado>
FIDE_PROXY_CA=/var/www/html/storage/app/fide-proxy-ca.crt
FIDE_HTTP_TIMEOUT=25
FIDE_HTTP_MAX_DURATION=25
```

`FIDE_PROXY_CA` deve apontar para o caminho físico do certificado, acessível pelo
usuário do processo web. No servidor da aplicação atual, `/root/xadrezsuico` é
um atalho para `/var/www/html`: não use o caminho sob `/root`, pois esse diretório
não permite acesso ao usuário `www-data`. O certificado público pode ter modo
644; mantenha a chave privada exclusivamente no servidor do proxy.

`FIDE_PROXY_HOST` aceita domínio ou IPv4, sem esquema/caminho. O endereço deve
resolver para o proxy e constar no certificado. Não use o domínio da aplicação
como destino: ele resolve via Cloudflare para a aplicação, não para o proxy.
Se criar um domínio para o proxy, mantenha a validação TLS e a restrição de origem.
Desativar `FIDE_PROXY_ENABLED` retorna à consulta direta; falhas do proxy não
provocam fallback silencioso nem apagam os ratings existentes.

No ambiente SaveInCloud atual, a porta também deve estar liberada em
`/etc/sysconfig/iptables-custom`, exclusivamente da origem privada da aplicação:

```
*filter
-I INPUT -s 10.100.40.165/32 -d 10.100.42.79/32 -p tcp --dport 8443 -j ACCEPT
COMMIT
```

Preserve as regras preexistentes; aplique com `sudo /usr/bin/jem firewall fwstart`.
O serviço escuta somente no IP privado, sem exposição da porta no IP público.
O certificado instalado é válido por um ano e precisa ser renovado antes de vencer;
atualize a cópia pública na aplicação ao renovar.

Registre no crontab do usuário do proxy (ajuste o caminho):

```
@reboot /bin/sh /var/lib/nginx/fide-proxy/run.sh
* * * * * /bin/sh /var/lib/nginx/fide-proxy/run.sh
```

O flock evita processos duplicados; cron restaura em até um minuto após falha.
Para manutenção, remova as duas entradas antes de encerrar o processo. Rotacione
`proxy.log` periodicamente. Não coloque configuração, chave ou token no Git.

## Limites e verificação

Apenas uma consulta upstream simultânea, intervalo mínimo de 1 segundo, conexão
até 5 segundos, execução até 20 segundos, HTML até 2 MiB. Cache em memória por
1 hora, até 128 perfis; respostas inválidas e erros não entram no cache.

Da aplicação, teste `/health`, um perfil real e o parser em modo sem gravação.
Use `php artisan fide:check` para verificar conexão e HTML sem gravar dados (saída 0 em sucesso, 1 em falha).
Valide também como o usuário web, por exemplo
`runuser -u www-data -- php artisan fide:check`, a partir do diretório físico
da aplicação, e pela rota HTTP de atualização. Um teste executado somente como
root não verifica as permissões do processo web. A rota de atualização grava
ratings; o comando `fide:check` não grava.
Teste token incorreto (401), rota arbitrária (404) e outra origem (conexão
recusada/encerrada). Valide novamente após reinício do processo. `/health` mede
o serviço local; uma consulta de perfil é necessária para verificar a FIDE.

Testes locais: `python3 -m unittest discover -s scripts/fide-proxy -p 'test_*.py'`.
