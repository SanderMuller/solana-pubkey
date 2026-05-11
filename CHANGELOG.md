# Changelog

All notable changes to `sandermuller/solana-pubkey` will be documented in this file.

## v0.1.1 - 2026-05-11

### Fixed

- **CI / packaging:** `update-changelog` workflow now pins checkout + commit-back to `main`. Previously, releases cut with `gh release create --target $SHA` would set `target_commitish` to a raw SHA, breaking the commit-back step. Future releases auto-prepend their notes to `CHANGELOG.md`.

### Added

- `CHANGELOG.md` — seeded with v0.1.0 entry so the changelog-updater action has a file to prepend to.

No source-level changes. Library API and runtime behavior identical to v0.1.0.

## v0.1.0 - 2026-05-11

Initial release.

Tiny, framework-agnostic PHP library for Solana public keys and Ed25519 signature verification. Built for Sign-In With Solana flows where you need to validate a wallet address and verify a detached signature without pulling in a full Solana SDK.

### What's included

- `SanderMuller\SolanaPubkey\PublicKey` — immutable 32-byte wrapper. Construct via `from()` (base58) or `fromBytes()` (raw). Verify Ed25519 signatures via `verify($message, $signature)`.
- `SanderMuller\SolanaPubkey\Base58` — pure-PHP codec using the Bitcoin/Solana alphabet. No GMP/BCMath.
- Typed exception hierarchy: `SolanaPubkeyException` (abstract base) → `InvalidPublicKeyException`, `InvalidBase58Exception`, `InvalidSignatureException`.

### Requirements

- PHP **8.3+**
- `ext-sodium`
- No other runtime dependencies.

### Public API

See [PUBLIC_API.md](PUBLIC_API.md) for the SemVer-governed surface.

**Full Changelog**: https://github.com/SanderMuller/solana-pubkey/commits/v0.1.0
