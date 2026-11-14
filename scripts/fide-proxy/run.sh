#!/bin/sh
set -eu
cd "$(dirname "$0")"
umask 077
# One process only; cron can call this every minute and at boot.
exec 9>proxy.lock
flock -n 9 || exit 0
echo $$ >proxy.pid
export FIDE_PROXY_CONFIG="$PWD/config.json"
exec python -u proxy.py >>proxy.log 2>&1
