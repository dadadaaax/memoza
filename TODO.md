# Memoza TODO

## VPN / social-source ingestion

- [ ] Start with one Proton VPN WireGuard exit for a future social-source worker.
- [ ] Keep VPN credentials and WireGuard private keys out of Git; store only in server-side `.env` files.
- [ ] Verify VPN egress IP before any source watcher uses it.
- [ ] Add source accounts only where access is allowed and rate-limited.
- [ ] Build a small worker that reads public/news/social sources through the VPN network and writes normalized candidates for moderation.
- [ ] Later: expand from one VPN to a managed pool, with health checks, rotation, and per-source limits.

## Memoza ranking/feed

- [x] Add `social_score` metadata to posts.
- [x] Sort Breaking News thumbnails by `social_score` descending.
- [ ] Use real engagement/source signals to update `social_score` over time.
