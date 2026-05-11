# Changelog

All notable changes to `sandermuller/solana-pubkey` will be documented in this file.

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
