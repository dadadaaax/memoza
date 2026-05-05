#!/usr/bin/env sh
set -eu

# Run from infra/vpn on the server after `.env` is filled and compose is up.
# It checks egress through the worker namespace, not the host.

docker compose exec -T social_worker sh -c "apk add --no-cache curl >/dev/null 2>&1 || true; curl -fsS https://ipinfo.io/json"
