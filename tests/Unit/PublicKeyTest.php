<?php declare(strict_types=1);

use SanderMuller\SolanaPubkey\Base58;
use SanderMuller\SolanaPubkey\Exceptions\InvalidPublicKeyException;
use SanderMuller\SolanaPubkey\Exceptions\InvalidSignatureException;
use SanderMuller\SolanaPubkey\PublicKey;

/**
 * @return array{publicKey: string, secretKey: string}
 */
function freshKeypair(): array
{
    $kp = sodium_crypto_sign_keypair();

    return [
        'publicKey' => sodium_crypto_sign_publickey($kp),
        'secretKey' => sodium_crypto_sign_secretkey($kp),
    ];
}

it('round-trips bytes through base58', function (): void {
    $kp = freshKeypair();
    $pk = PublicKey::fromBytes($kp['publicKey']);

    expect(PublicKey::from($pk->toBase58())->toBinaryString())->toBe($kp['publicKey']);
});

it('rejects wrong-length raw bytes', function (): void {
    PublicKey::fromBytes(str_repeat("\x00", 31));
})->throws(InvalidPublicKeyException::class);

it('rejects base58 that decodes to wrong length', function (): void {
    PublicKey::from('abc');
})->throws(InvalidPublicKeyException::class);

it('rejects base58 with invalid characters', function (): void {
    PublicKey::from('not-base58!!!');
})->throws(InvalidPublicKeyException::class);

it('verifies a valid Ed25519 signature', function (): void {
    $kp = freshKeypair();
    $message = 'sign in with solana';
    $signature = sodium_crypto_sign_detached($message, $kp['secretKey']);

    $pk = PublicKey::fromBytes($kp['publicKey']);

    expect($pk->verify($message, $signature))->toBeTrue();
});

it('rejects a tampered message', function (): void {
    $kp = freshKeypair();
    $message = 'sign in with solana';
    $signature = sodium_crypto_sign_detached($message, $kp['secretKey']);

    $pk = PublicKey::fromBytes($kp['publicKey']);

    expect($pk->verify('sign in with anything else', $signature))->toBeFalse();
});

it('rejects signature of wrong length', function (): void {
    $kp = freshKeypair();
    PublicKey::fromBytes($kp['publicKey'])->verify('msg', str_repeat("\x00", 63));
})->throws(InvalidSignatureException::class);

it('equals is true for same key, false for different keys', function (): void {
    $kp1 = freshKeypair();
    $kp2 = freshKeypair();
    $a = PublicKey::fromBytes($kp1['publicKey']);
    $b = PublicKey::fromBytes($kp1['publicKey']);
    $c = PublicKey::fromBytes($kp2['publicKey']);

    expect($a->equals($b))->toBeTrue();
    expect($a->equals($c))->toBeFalse();
});

it('stringifies to base58', function (): void {
    $kp = freshKeypair();
    $pk = PublicKey::fromBytes($kp['publicKey']);

    expect((string) $pk)->toBe($pk->toBase58());
});

it('produces base58 matching Base58::encode of raw bytes', function (): void {
    $kp = freshKeypair();
    $pk = PublicKey::fromBytes($kp['publicKey']);

    expect($pk->toBase58())->toBe(Base58::encode($kp['publicKey']));
});
