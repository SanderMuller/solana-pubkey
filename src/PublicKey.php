<?php declare(strict_types=1);

namespace SanderMuller\SolanaPubkey;

use SanderMuller\SolanaPubkey\Exceptions\InvalidBase58Exception;
use SanderMuller\SolanaPubkey\Exceptions\InvalidPublicKeyException;
use SanderMuller\SolanaPubkey\Exceptions\InvalidSignatureException;
use SodiumException;
use Stringable;

/**
 * Solana / Ed25519 public key (32 bytes).
 *
 * Immutable wrapper around raw key bytes. Construct via {@see from}
 * (base58 string) or {@see fromBytes} (raw 32-byte binary).
 */
final readonly class PublicKey implements Stringable
{
    public const int LENGTH = 32;

    public const int SIGNATURE_LENGTH = 64;

    /**
     * @param non-empty-string $bytes
     */
    private function __construct(private string $bytes) {}

    public static function from(string $base58): self
    {
        try {
            $bytes = Base58::decode($base58);
        } catch (InvalidBase58Exception $invalidBase58Exception) {
            throw new InvalidPublicKeyException("Invalid Solana public key: {$invalidBase58Exception->getMessage()}", $invalidBase58Exception->getCode(), previous: $invalidBase58Exception);
        }

        return self::fromBytes($bytes);
    }

    public static function fromBytes(string $bytes): self
    {
        if ($bytes === '') {
            throw new InvalidPublicKeyException(
                'Invalid public key length. Expected ' . self::LENGTH . ', got 0.',
            );
        }

        $length = strlen($bytes);
        if ($length !== self::LENGTH) {
            throw new InvalidPublicKeyException(
                'Invalid public key length. Expected ' . self::LENGTH . ", got {$length}.",
            );
        }

        return new self($bytes);
    }

    public function toBase58(): string
    {
        return Base58::encode($this->bytes);
    }

    public function toBinaryString(): string
    {
        return $this->bytes;
    }

    public function equals(self $other): bool
    {
        return hash_equals($this->bytes, $other->bytes);
    }

    /**
     * Verify an Ed25519 signature against a message using this public key.
     *
     * @param string $message Raw message bytes that were signed.
     * @param string $signature Binary 64-byte Ed25519 signature. Caller decodes any base58/base64/hex first.
     * @throws InvalidSignatureException When $signature is not exactly SIGNATURE_LENGTH bytes.
     */
    public function verify(string $message, string $signature): bool
    {
        $length = strlen($signature);
        if ($length !== self::SIGNATURE_LENGTH || $signature === '') {
            throw new InvalidSignatureException(
                'Invalid signature length. Expected ' . self::SIGNATURE_LENGTH . ", got {$length}.",
            );
        }

        try {
            return sodium_crypto_sign_verify_detached($signature, $message, $this->bytes);
        } catch (SodiumException) {
            return false;
        }
    }

    public function __toString(): string
    {
        return $this->toBase58();
    }
}
