# solana-pubkey

[![Latest Version on Packagist](https://img.shields.io/packagist/v/sandermuller/solana-pubkey.svg?style=flat-square)](https://packagist.org/packages/sandermuller/solana-pubkey)
[![run-tests](https://github.com/sandermuller/solana-pubkey/actions/workflows/run-tests.yml/badge.svg)](https://github.com/sandermuller/solana-pubkey/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/sandermuller/solana-pubkey/actions/workflows/phpstan.yml/badge.svg)](https://github.com/sandermuller/solana-pubkey/actions/workflows/phpstan.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/sandermuller/solana-pubkey.svg?style=flat-square)](https://packagist.org/packages/sandermuller/solana-pubkey)
[![License](https://img.shields.io/packagist/l/sandermuller/solana-pubkey.svg?style=flat-square)](LICENSE)

Tiny, framework-agnostic PHP library for Solana public keys and Ed25519 signature verification.

Built for [Sign-In With Solana](https://github.com/phantom/sign-in-with-solana) flows where you need to validate a wallet address and verify a detached signature — without pulling in a full Solana SDK.

## Installation

```bash
composer require sandermuller/solana-pubkey
```

Requires PHP **8.3+** and **ext-sodium**. No other runtime dependencies.

## Usage

### Verify a Solana wallet signature

```php
use SanderMuller\SolanaPubkey\Base58;
use SanderMuller\SolanaPubkey\PublicKey;

$pubkey    = PublicKey::from($walletAddressBase58);
$signature = Base58::decode($signatureBase58);

if ($pubkey->verify($message, $signature)) {
    // signature is valid for $message under $pubkey
}
```

### Construct from raw bytes

```php
use SanderMuller\SolanaPubkey\PublicKey;

$keypair = sodium_crypto_sign_keypair();
$pubkey  = PublicKey::fromBytes(sodium_crypto_sign_publickey($keypair));

echo $pubkey->toBase58();
```

### Base58 encoding

```php
use SanderMuller\SolanaPubkey\Base58;

$encoded = Base58::encode($binary);   // throws nothing
$decoded = Base58::decode($base58);   // throws InvalidBase58Exception on bad input
```

## API surface

See [PUBLIC_API.md](PUBLIC_API.md) for the SemVer-governed surface. Anything not listed there is `@internal` and may change in any release.

## Exceptions

All thrown exceptions extend `SanderMuller\SolanaPubkey\Exceptions\SolanaPubkeyException` (which extends `RuntimeException`):

| Exception                   | Thrown by                                                                      |
|-----------------------------|--------------------------------------------------------------------------------|
| `InvalidPublicKeyException` | `PublicKey::from()`, `PublicKey::fromBytes()` — wrong length or invalid base58 |
| `InvalidBase58Exception`    | `Base58::decode()` — non-alphabet character                                    |
| `InvalidSignatureException` | `PublicKey::verify()` — signature is not exactly 64 bytes                      |

`PublicKey::verify()` returns `false` (does not throw) for wrong-but-well-formed signatures and tampered messages.

## Why this exists

The [`collectiq/solana-php-sdk`](https://github.com/collectiq/solana-php-sdk) is a full Solana SDK (RPC, transactions, Borsh, programs, DID). For SIWS-style auth flows, you only need three primitives: base58 codec, Ed25519 verify, and pubkey validation. This package ships exactly those, with no framework dependencies and no proprietary-license upstream.

## Development

```bash
composer install
composer test      # Pest
composer qa        # Rector + Pint + PHPStan + lean-package-validator + tests
```

## License

MIT — see [LICENSE](LICENSE).
