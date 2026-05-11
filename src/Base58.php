<?php declare(strict_types=1);

namespace SanderMuller\SolanaPubkey;

use SanderMuller\SolanaPubkey\Exceptions\InvalidBase58Exception;

/**
 * Base58 codec using the Bitcoin / Solana alphabet.
 *
 * Pure-PHP implementation — no GMP / BCMath dependency. Input is bounded
 * (32 byte pubkeys, 64 byte signatures), so the O(n^2) byte-wise long
 * division is negligible in practice.
 */
final class Base58
{
    private const string ALPHABET = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

    /**
     * Char → alphabet index. Lazily built once per process.
     *
     * @var array<int|string, int>|null
     */
    private static ?array $decodeMap = null;

    public static function encode(string $binary): string
    {
        if ($binary === '') {
            return '';
        }

        $len = strlen($binary);

        $zeros = 0;
        while ($zeros < $len && $binary[$zeros] === "\x00") {
            ++$zeros;
        }

        // log(256) / log(58) ≈ 1.3655 → +1 for ceiling safety
        $size = intdiv(($len - $zeros) * 138, 100) + 1;
        $b58 = array_fill(0, $size, 0);

        for ($i = $zeros; $i < $len; ++$i) {
            $carry = ord($binary[$i]);
            for ($j = $size - 1; $j >= 0; --$j) {
                $carry += $b58[$j] << 8;
                $b58[$j] = $carry % 58;
                $carry = intdiv($carry, 58);
            }
        }

        $it = 0;
        while ($it < $size && $b58[$it] === 0) {
            ++$it;
        }

        $result = str_repeat(self::ALPHABET[0], $zeros);
        for (; $it < $size; ++$it) {
            $result .= self::ALPHABET[$b58[$it]];
        }

        return $result;
    }

    public static function decode(string $base58): string
    {
        if ($base58 === '') {
            return '';
        }

        $map = self::$decodeMap ??= self::buildDecodeMap();
        $len = strlen($base58);

        $zeros = 0;
        while ($zeros < $len && $base58[$zeros] === self::ALPHABET[0]) {
            ++$zeros;
        }

        // log(58) / log(256) ≈ 0.7325 → +1 for ceiling safety
        $size = intdiv($len * 733, 1000) + 1;
        $b256 = array_fill(0, $size, 0);

        for ($i = $zeros; $i < $len; ++$i) {
            $char = $base58[$i];
            if (! isset($map[$char])) {
                throw new InvalidBase58Exception("Invalid base58 character '{$char}' at offset {$i}.");
            }

            $carry = $map[$char];
            for ($j = $size - 1; $j >= 0; --$j) {
                $carry += 58 * $b256[$j];
                $b256[$j] = $carry & 0xFF;
                $carry >>= 8;
            }
        }

        $it = 0;
        while ($it < $size && $b256[$it] === 0) {
            ++$it;
        }

        $result = str_repeat("\x00", $zeros);
        for (; $it < $size; ++$it) {
            $result .= chr($b256[$it]);
        }

        return $result;
    }

    /**
     * @return array<int|string, int>
     */
    private static function buildDecodeMap(): array
    {
        $map = [];
        for ($i = 0, $n = strlen(self::ALPHABET); $i < $n; ++$i) {
            $map[self::ALPHABET[$i]] = $i;
        }

        return $map;
    }
}
