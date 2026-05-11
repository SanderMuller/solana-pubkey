<?php declare(strict_types=1);

use SanderMuller\SolanaPubkey\Base58;
use SanderMuller\SolanaPubkey\Exceptions\InvalidBase58Exception;

it('encodes empty input to empty string', function (): void {
    expect(Base58::encode(''))->toBe('');
});

it('decodes empty input to empty string', function (): void {
    expect(Base58::decode(''))->toBe('');
});

it('round-trips arbitrary bytes', function (): void {
    $bytes = random_bytes(32);
    expect(Base58::decode(Base58::encode($bytes)))->toBe($bytes);
});

it('preserves leading zero bytes as leading 1s', function (): void {
    $bytes = "\x00\x00\x00" . random_bytes(29);
    $encoded = Base58::encode($bytes);

    expect(substr($encoded, 0, 3))->toBe('111');
    expect(Base58::decode($encoded))->toBe($bytes);
});

it('decodes a known Solana system-program address', function (): void {
    // Solana System Program — all 32 zero bytes → 32 leading '1' chars in base58.
    $decoded = Base58::decode('11111111111111111111111111111111');

    expect($decoded)->toBe(str_repeat("\x00", 32));
});

it('encodes 32 zero bytes back to 32 ones', function (): void {
    expect(Base58::encode(str_repeat("\x00", 32)))->toBe('11111111111111111111111111111111');
});

it('throws InvalidBase58Exception on invalid characters', function (): void {
    Base58::decode('0OIl');
})->throws(InvalidBase58Exception::class);

it('round-trips a known signature-length payload', function (): void {
    $bytes = random_bytes(64);
    expect(Base58::decode(Base58::encode($bytes)))->toBe($bytes);
});
