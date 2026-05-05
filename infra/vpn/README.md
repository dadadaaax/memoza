# Memoza Proton VPN scaffold

This starts with **one** Proton VPN WireGuard exit for future social-source reading.

## What WireGuard is

WireGuard is a modern VPN protocol. Proton gives us a small config containing a private key, an internal tunnel address, and server selection. Docker/Gluetun uses that to create an encrypted network tunnel. Any container sharing `network_mode: service:memoza_vpn` sends outbound traffic through the VPN instead of the VPS public IP.

## Proton VPN Plus fit

Proton VPN Plus is suitable for the first version:

- paid Proton VPN plans include access to Proton servers and WireGuard configs;
- VPN Plus allows up to 10 simultaneous VPN connections, so one worker is well within the limit;
- use one dedicated WireGuard profile/config for Memoza.

Important: Proton does not provide a stable public “give me fresh VPNs” automation API for normal accounts. For now, treat Proton configs as operator-provisioned secrets. Later we can automate rotation around a set of pre-created configs if Proton allows it in the account UI/CLI.

## Setup

On the server:

```sh
cd /opt/memoza/html/infra/vpn
cp .env.example .env
chmod 600 .env
# Fill .env with Proton WireGuard values from the Proton dashboard.
docker compose up -d
./scripts/check-vpn.sh
```

Expected check: returned IP/country should be Proton, not the VPS host.

## Rules for future source readers

- Respect each source platform’s terms/rate limits.
- Do not use VPN rotation to evade bans, abuse detection, paywalls, or access controls.
- Prefer official APIs/RSS/public pages where available.
- Store source account credentials outside Git, ideally in a secrets file or secret manager.
