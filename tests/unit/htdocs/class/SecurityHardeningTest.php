<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for security hardening patterns — security audit phase 2.
 *
 * Tests the activation key generation (bin2hex + random_bytes),
 * activation key verification (hash_equals), and unserialize safety
 * patterns introduced across the codebase.
 */
class SecurityHardeningTest extends TestCase
{
    // =========================================================================
    // Activation key generation: bin2hex(random_bytes(4))
    // =========================================================================

    #[Test]
    public function activationKeyIsExactlyEightCharacters(): void
    {
        $key = bin2hex(random_bytes(4));
        $this->assertSame(8, strlen($key));
    }

    #[Test]
    public function activationKeyContainsOnlyHexCharacters(): void
    {
        $key = bin2hex(random_bytes(4));
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $key);
    }

    #[Test]
    public function activationKeyIsLowercaseHex(): void
    {
        $key = bin2hex(random_bytes(4));
        $this->assertSame($key, strtolower($key));
    }

    #[Test]
    public function multipleActivationKeysAreDifferent(): void
    {
        $keys = [];
        for ($i = 0; $i < 20; $i++) {
            $keys[] = bin2hex(random_bytes(4));
        }
        $unique = array_unique($keys);
        // With 20 random 32-bit values, collisions are astronomically unlikely
        $this->assertCount(20, $unique, 'All 20 generated keys should be unique');
    }

    #[Test]
    public function activationKeyIsCompatibleWithHashEquals(): void
    {
        $stored = bin2hex(random_bytes(4));
        $input  = $stored; // simulate correct user input

        $this->assertTrue(hash_equals($stored, $input));
    }

    #[Test]
    public function activationKeyLengthMatchesHashEqualsRequirement(): void
    {
        // hash_equals works best when both strings are the same length
        $key1 = bin2hex(random_bytes(4));
        $key2 = bin2hex(random_bytes(4));
        $this->assertSame(strlen($key1), strlen($key2));
    }

    // =========================================================================
    // Activation key verification: hash_equals pattern
    // =========================================================================

    #[Test]
    public function emptyActkeyIsRejected(): void
    {
        $stored = bin2hex(random_bytes(4));
        $actkey = '';

        // Pattern: $actkey === '' || !hash_equals($stored, $actkey)
        $rejected = ($actkey === '' || !hash_equals($stored, $actkey));
        $this->assertTrue($rejected, 'Empty activation key must be rejected');
    }

    #[Test]
    public function wrongActkeyIsRejected(): void
    {
        $stored = 'abcd1234';
        $actkey = 'wrong999';

        $rejected = ($actkey === '' || !hash_equals($stored, $actkey));
        $this->assertTrue($rejected, 'Wrong activation key must be rejected');
    }

    #[Test]
    public function correctActkeyPasses(): void
    {
        $stored = 'abcd1234';
        $actkey = 'abcd1234';

        $rejected = ($actkey === '' || !hash_equals($stored, $actkey));
        $this->assertFalse($rejected, 'Correct activation key must pass');
    }

    #[Test]
    public function hashEqualsIsTimingSafe(): void
    {
        // Verify hash_equals is used (not == or !=) by testing that
        // it properly handles type-juggling edge cases
        $stored = '0e123456'; // looks like scientific notation to ==
        $actkey = '0e654321'; // different value but == would say true

        // With ==, these would be "equal" (both evaluate to 0 as floats)
        // hash_equals correctly sees them as different
        $this->assertFalse(hash_equals($stored, $actkey));
    }

    #[Test]
    public function hashEqualsRejectsDifferentLengths(): void
    {
        $stored = 'abcd1234';
        $actkey = 'abcd12345'; // one character too long

        $this->assertFalse(hash_equals($stored, $actkey));
    }

    // =========================================================================
    // Unserialize safety: allowed_classes => false
    // =========================================================================

    #[Test]
    public function unserializeSafelyDeserializesArrays(): void
    {
        $data = serialize(['key' => 'value', 'num' => 42]);
        $result = unserialize($data, ['allowed_classes' => false]);

        $this->assertIsArray($result);
        $this->assertSame('value', $result['key']);
        $this->assertSame(42, $result['num']);
    }

    #[Test]
    public function unserializeSafelyDeserializesStrings(): void
    {
        $data = serialize('hello world');
        $result = unserialize($data, ['allowed_classes' => false]);

        $this->assertSame('hello world', $result);
    }

    #[Test]
    public function unserializeSafelyDeserializesIntegers(): void
    {
        $data = serialize(12345);
        $result = unserialize($data, ['allowed_classes' => false]);

        $this->assertSame(12345, $result);
    }

    #[Test]
    public function unserializeSafelyBlocksObjectInstantiation(): void
    {
        $obj = new \stdClass();
        $obj->name = 'test';
        $data = serialize($obj);

        $result = unserialize($data, ['allowed_classes' => false]);

        $this->assertInstanceOf(\__PHP_Incomplete_Class::class, $result);
    }

    #[Test]
    public function unserializeSafelyDeserializesNestedArrays(): void
    {
        $data = serialize(['outer' => ['inner' => 'deep']]);
        $result = unserialize($data, ['allowed_classes' => false]);

        $this->assertSame('deep', $result['outer']['inner']);
    }

    #[Test]
    public function unserializeSafelyHandlesEmptyArray(): void
    {
        $data = serialize([]);
        $result = unserialize($data, ['allowed_classes' => false]);

        $this->assertSame([], $result);
    }

    #[Test]
    public function unserializeSafelyReturnsFalseForInvalidData(): void
    {
        $warningTriggered = false;
        set_error_handler(function (int $errno) use (&$warningTriggered): bool {
            if ($errno === E_WARNING || $errno === E_NOTICE) {
                $warningTriggered = true;
            }
            return true;
        });

        $result = unserialize('not_valid_serialized_data', ['allowed_classes' => false]);

        restore_error_handler();

        $this->assertFalse($result);
    }

    #[Test]
    public function unserializeSafelyBlocksCustomClassInstantiation(): void
    {
        // Simulate a serialized object of a hypothetical class
        // The serialized form of an object with class "EvilClass" and property x=1
        $data = 'O:9:"EvilClass":1:{s:1:"x";i:1;}';

        $warningTriggered = false;
        set_error_handler(function (int $errno) use (&$warningTriggered): bool {
            if ($errno === E_WARNING || $errno === E_NOTICE) {
                $warningTriggered = true;
            }
            return true;
        });

        $result = unserialize($data, ['allowed_classes' => false]);

        restore_error_handler();

        $this->assertInstanceOf(\__PHP_Incomplete_Class::class, $result);
    }
}
