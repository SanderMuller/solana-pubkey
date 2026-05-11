# Public API

This document enumerates the SemVer-governed surface of `sandermuller/solana-pubkey`. Every symbol listed here is governed by [SemVer 2.0](https://semver.org/) — renames or breaking signature changes require a MAJOR bump.

Symbols **not listed here** — and anything under `SanderMuller\SolanaPubkey\Internal\` — are `@internal` and may change in any release.

## Classes

### `SanderMuller\SolanaPubkey\PublicKey`

Immutable wrapper around a 32-byte Ed25519 / Solana public key.

| Member | Signature |
|---|---|
| `LENGTH` constant | `public const int LENGTH = 32` |
| `SIGNATURE_LENGTH` constant | `public const int SIGNATURE_LENGTH = 64` |
| `from()` | `public static function from(string $base58): self` |
| `fromBytes()` | `public static function fromBytes(string $bytes): self` |
| `toBase58()` | `public function toBase58(): string` |
| `toBinaryString()` | `public function toBinaryString(): string` |
| `equals()` | `public function equals(self $other): bool` |
| `verify()` | `public function verify(string $message, string $signature): bool` |
| `__toString()` | `public function __toString(): string` |

The constructor is `private`; instantiate via `from()` or `fromBytes()`.

### `SanderMuller\SolanaPubkey\Base58`

Stateless Base58 codec using the Bitcoin / Solana alphabet (`123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz`).

| Member | Signature |
|---|---|
| `encode()` | `public static function encode(string $binary): string` |
| `decode()` | `public static function decode(string $base58): string` |

## Exceptions

All package exceptions extend `SolanaPubkeyException` (which extends `\RuntimeException`). Downstream code can catch either the base class for broad handling or the specific subclasses for targeted recovery.

| Class | Thrown by |
|---|---|
| `SanderMuller\SolanaPubkey\Exceptions\SolanaPubkeyException` | (abstract base) |
| `SanderMuller\SolanaPubkey\Exceptions\InvalidPublicKeyException` | `PublicKey::from`, `PublicKey::fromBytes` |
| `SanderMuller\SolanaPubkey\Exceptions\InvalidBase58Exception` | `Base58::decode` |
| `SanderMuller\SolanaPubkey\Exceptions\InvalidSignatureException` | `PublicKey::verify` |

## Stability guarantees

- **PATCH** (`0.1.0` → `0.1.1`): bug fixes that preserve all signatures and observable behavior listed above.
- **MINOR** (`0.1.0` → `0.2.0`): new public symbols, new optional parameters with defaults. No removals, no renames.
- **MAJOR** (`0.x` → `1.0`, or `1.x` → `2.0`): removals, renames, or signature changes to symbols listed above.

During the `0.x` series, MINOR bumps may include breaking changes per SemVer's "initial development" clause — but the package will document any such change in release notes with a clear migration path.

## What is NOT covered

- The `Internal\` namespace (does not currently exist, reserved).
- Exception **messages** (only class names + thrown-from contracts).
- Exception **codes** (always `0` unless explicitly documented).
- Behavior on input that violates documented preconditions (e.g. passing a non-string where a string is required — PHP's type system handles this, not this contract).
- Performance characteristics. The Base58 codec is O(n²) but inputs are bounded to ~64 bytes in practice.

## Deprecations

None at this time.
