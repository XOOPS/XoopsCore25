<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionMethod;
use XoopsMySQLDatabase;

require_once XOOPS_ROOT_PATH . '/kernel/session80.php';

/**
 * Unit tests for XoopsSessionHandler (session80.php).
 *
 * Uses reflection to bypass the constructor (which depends on globals
 * and session_set_cookie_params) and injects a mock database.
 */
class XoopsSessionHandlerTest extends KernelTestCase
{
    /** @var XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    /** @var \XoopsSessionHandler */
    private $handler;

    /** @var string|null saved REMOTE_ADDR to restore in tearDown */
    private ?string $savedRemoteAddr = null;

    /** @var bool whether REMOTE_ADDR was originally set */
    private bool $hadRemoteAddr = false;

    protected function setUp(): void
    {
        // Save original REMOTE_ADDR
        $this->hadRemoteAddr = isset($_SERVER['REMOTE_ADDR']);
        $this->savedRemoteAddr = $_SERVER['REMOTE_ADDR'] ?? null;

        // Set a default REMOTE_ADDR for tests
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';

        $this->db = $this->createMockDatabase();

        // Create handler without constructor (avoids globals, session_set_cookie_params)
        $ref = new ReflectionClass(\XoopsSessionHandler::class);
        $this->handler = $ref->newInstanceWithoutConstructor();
        $this->setProtectedProperty($this->handler, 'db', $this->db);

        // Ensure default properties are set (they have defaults in class definition,
        // but newInstanceWithoutConstructor should respect property defaults)
        // securityLevel defaults to 3, enableRegenerateId defaults to true
    }

    protected function tearDown(): void
    {
        // Restore REMOTE_ADDR
        if ($this->hadRemoteAddr) {
            $_SERVER['REMOTE_ADDR'] = $this->savedRemoteAddr;
        } else {
            unset($_SERVER['REMOTE_ADDR']);
        }
    }

    /**
     * Helper: invoke a private/protected method via reflection.
     *
     * @param object $object
     * @param string $methodName
     * @param array  $args
     * @return mixed
     */
    private function invokePrivateMethod(object $object, string $methodName, array $args = [])
    {
        $method = new ReflectionMethod($object, $methodName);
        $method->setAccessible(true);
        return $method->invoke($object, ...$args);
    }

    // =========================================================================
    // open()
    // =========================================================================

    public function testOpenAlwaysReturnsTrue(): void
    {
        $this->assertTrue($this->handler->open('/tmp', 'PHPSESSID'));
    }

    public function testOpenWithEmptyArgumentsReturnsTrue(): void
    {
        $this->assertTrue($this->handler->open('', ''));
    }

    // =========================================================================
    // close()
    // =========================================================================

    public function testCloseReturnsTrue(): void
    {
        // close() calls gc_force() internally, which may or may not call gc()
        // depending on random_int. We just verify it returns true without error.
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getAffectedRows')->willReturn(0);

        $this->assertTrue($this->handler->close());
    }

    // =========================================================================
    // read() -- query fails
    // =========================================================================

    public function testReadReturnsfalseWhenQueryFails(): void
    {
        $this->db->method('queryF')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->read('abc123');
        $this->assertFalse($result);
    }

    // =========================================================================
    // read() -- no row found
    // =========================================================================

    public function testReadReturnsEmptyStringWhenNoRowFound(): void
    {
        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(false);

        $result = $this->handler->read('abc123');
        $this->assertSame('', $result);
    }

    // =========================================================================
    // read() -- valid row with IP match (same subnet)
    // =========================================================================

    public function testReadReturnsDataWhenIpMatchesSameSubnet(): void
    {
        // REMOTE_ADDR is 192.168.1.100
        // Stored IP is 192.168.1.50 -- same /24 subnet
        $sessionData = 'serialized_session_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '192.168.1.50']);

        $result = $this->handler->read('abc123');
        $this->assertSame($sessionData, $result);
    }

    // =========================================================================
    // read() -- valid row with IP mismatch (different subnet)
    // =========================================================================

    public function testReadReturnsEmptyStringWhenIpMismatch(): void
    {
        // REMOTE_ADDR is 192.168.1.100
        // Stored IP is 10.0.0.1 -- different /24 subnet at security level 3
        $sessionData = 'serialized_session_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '10.0.0.1']);

        $result = $this->handler->read('abc123');
        $this->assertSame('', $result);
    }

    // =========================================================================
    // read() -- securityLevel <= 1 bypasses IP check
    // =========================================================================

    public function testReadBypassesIpCheckWhenSecurityLevelIsOne(): void
    {
        $this->handler->securityLevel = 1;

        $sessionData = 'my_session_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '10.0.0.1']);

        $result = $this->handler->read('abc123');
        $this->assertSame($sessionData, $result);
    }

    public function testReadBypassesIpCheckWhenSecurityLevelIsZero(): void
    {
        $this->handler->securityLevel = 0;

        $sessionData = 'another_session_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '172.16.0.1']);

        $result = $this->handler->read('abc123');
        $this->assertSame($sessionData, $result);
    }

    // =========================================================================
    // read() -- security level 2 (broader subnet /16 for v4)
    // =========================================================================

    public function testReadWithSecurityLevel2MatchesBroaderSubnet(): void
    {
        $this->handler->securityLevel = 2;

        // REMOTE_ADDR is 192.168.1.100
        // Stored is 192.168.200.5 -- same /16 subnet
        $sessionData = 'level2_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '192.168.200.5']);

        $result = $this->handler->read('abc123');
        $this->assertSame($sessionData, $result);
    }

    public function testReadWithSecurityLevel2RejectsOutsideSubnet(): void
    {
        $this->handler->securityLevel = 2;

        // REMOTE_ADDR is 192.168.1.100
        // Stored is 192.169.1.100 -- different /16 subnet
        $sessionData = 'level2_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '192.169.1.100']);

        $result = $this->handler->read('abc123');
        $this->assertSame('', $result);
    }

    // =========================================================================
    // read() -- security level 4 (exact match /32 for v4)
    // =========================================================================

    public function testReadWithSecurityLevel4RequiresExactIpMatch(): void
    {
        $this->handler->securityLevel = 4;

        $sessionData = 'level4_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '192.168.1.100']);

        $result = $this->handler->read('abc123');
        $this->assertSame($sessionData, $result);
    }

    public function testReadWithSecurityLevel4RejectsDifferentIp(): void
    {
        $this->handler->securityLevel = 4;

        $sessionData = 'level4_data';

        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([$sessionData, '192.168.1.101']);

        $result = $this->handler->read('abc123');
        $this->assertSame('', $result);
    }

    // =========================================================================
    // write() -- success
    // =========================================================================

    public function testWriteReturnsTrueOnExecSuccess(): void
    {
        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->write('sess_abc', 'some_data');
        $this->assertTrue($result);
    }

    // =========================================================================
    // write() -- failure
    // =========================================================================

    public function testWriteReturnsFalseOnExecFailure(): void
    {
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->write('sess_abc', 'some_data');
        $this->assertFalse($result);
    }

    // =========================================================================
    // write() -- SQL contains correct table and data
    // =========================================================================

    public function testWriteBuildsCorrectSql(): void
    {
        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function (string $sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return true;
            });

        $this->handler->write('test_session_id', 'test_data_payload');

        $this->assertStringContainsString('xoops_session', $sqlCaptured);
        $this->assertStringContainsString('INSERT INTO', $sqlCaptured);
        $this->assertStringContainsString('ON DUPLICATE KEY UPDATE', $sqlCaptured);
        $this->assertStringContainsString('test_session_id', $sqlCaptured);
        $this->assertStringContainsString('test_data_payload', $sqlCaptured);
    }

    // =========================================================================
    // destroy() -- success
    // =========================================================================

    public function testDestroyReturnsTrueOnSuccess(): void
    {
        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function (string $sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return true;
            });

        $result = $this->handler->destroy('sess_to_delete');
        $this->assertTrue($result);
        $this->assertStringContainsString('DELETE', $sqlCaptured);
        $this->assertStringContainsString('xoops_session', $sqlCaptured);
        $this->assertStringContainsString('sess_to_delete', $sqlCaptured);
    }

    // =========================================================================
    // destroy() -- failure
    // =========================================================================

    public function testDestroyReturnsFalseOnExecFailure(): void
    {
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->destroy('sess_to_delete');
        $this->assertFalse($result);
    }

    // =========================================================================
    // gc() -- max_lifetime <= 0
    // =========================================================================

    public function testGcReturnsZeroWhenMaxLifetimeIsZero(): void
    {
        $result = $this->handler->gc(0);
        $this->assertSame(0, $result);
    }

    public function testGcReturnsZeroWhenMaxLifetimeIsNegative(): void
    {
        $result = $this->handler->gc(-100);
        $this->assertSame(0, $result);
    }

    // =========================================================================
    // gc() -- exec succeeds, returns affected rows
    // =========================================================================

    public function testGcReturnsAffectedRowsOnSuccess(): void
    {
        $this->db->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function (string $sql) {
                $this->assertStringContainsString('DELETE', $sql);
                $this->assertStringContainsString('xoops_session', $sql);
                $this->assertStringContainsString('sess_updated', $sql);
                return true;
            });
        $this->db->method('getAffectedRows')->willReturn(5);

        $result = $this->handler->gc(3600);
        $this->assertSame(5, $result);
    }

    // =========================================================================
    // gc() -- exec fails, returns false
    // =========================================================================

    public function testGcReturnsFalseOnExecFailure(): void
    {
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->gc(3600);
        $this->assertFalse($result);
    }

    // =========================================================================
    // gc() -- affected rows zero is valid
    // =========================================================================

    public function testGcReturnsZeroAffectedRows(): void
    {
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getAffectedRows')->willReturn(0);

        $result = $this->handler->gc(3600);
        $this->assertSame(0, $result);
    }

    // =========================================================================
    // validateId() -- no row found
    // =========================================================================

    public function testValidateIdReturnsFalseWhenNoRowFound(): void
    {
        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(false);

        $result = $this->handler->validateId('nonexistent_session');
        $this->assertFalse($result);
    }

    // =========================================================================
    // validateId() -- query fails
    // =========================================================================

    public function testValidateIdReturnsFalseWhenQueryFails(): void
    {
        $this->db->method('queryF')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->validateId('some_session');
        $this->assertFalse($result);
    }

    // =========================================================================
    // validateId() -- row found with matching IP
    // =========================================================================

    public function testValidateIdReturnsTrueWhenIpMatches(): void
    {
        // REMOTE_ADDR = 192.168.1.100, stored = 192.168.1.50 (same /24)
        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(['192.168.1.50']);

        $result = $this->handler->validateId('valid_session');
        $this->assertTrue($result);
    }

    // =========================================================================
    // validateId() -- row found with mismatching IP
    // =========================================================================

    public function testValidateIdReturnsFalseWhenIpMismatches(): void
    {
        // REMOTE_ADDR = 192.168.1.100, stored = 10.0.0.1 (different subnet)
        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(['10.0.0.1']);

        $result = $this->handler->validateId('session_with_bad_ip');
        $this->assertFalse($result);
    }

    // =========================================================================
    // validateId() -- row with null stored IP
    // =========================================================================

    public function testValidateIdReturnsTrueWhenStoredIpIsNull(): void
    {
        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([null]);

        $result = $this->handler->validateId('session_null_ip');
        $this->assertTrue($result);
    }

    // =========================================================================
    // validateId() -- low security level bypasses check
    // =========================================================================

    public function testValidateIdReturnsTrueWithLowSecurityLevel(): void
    {
        $this->handler->securityLevel = 1;

        // Different IP but low security so should pass
        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(['10.0.0.1']);

        $result = $this->handler->validateId('any_session');
        $this->assertTrue($result);
    }

    // =========================================================================
    // updateTimestamp() -- success
    // =========================================================================

    public function testUpdateTimestampReturnsTrueOnSuccess(): void
    {
        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function (string $sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return true;
            });

        $result = $this->handler->updateTimestamp('sess_123', 'data');
        $this->assertTrue($result);
        $this->assertStringContainsString('UPDATE', $sqlCaptured);
        $this->assertStringContainsString('xoops_session', $sqlCaptured);
        $this->assertStringContainsString('sess_updated', $sqlCaptured);
        $this->assertStringContainsString('sess_123', $sqlCaptured);
    }

    // =========================================================================
    // updateTimestamp() -- failure
    // =========================================================================

    public function testUpdateTimestampReturnsFalseOnFailure(): void
    {
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->updateTimestamp('sess_123', 'data');
        $this->assertFalse($result);
    }

    // =========================================================================
    // validateSessionIp() -- private method via reflection
    // =========================================================================

    public function testValidateSessionIpReturnsTrueForNullStoredIp(): void
    {
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', [null]);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpReturnsTrueForEmptyStoredIp(): void
    {
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpReturnsTrueWhenSecurityLevelIsOne(): void
    {
        $this->handler->securityLevel = 1;
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['10.0.0.1']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpReturnsTrueWhenSecurityLevelIsZero(): void
    {
        $this->handler->securityLevel = 0;
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['10.0.0.1']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpReturnsTrueForSameSubnet(): void
    {
        // REMOTE_ADDR = 192.168.1.100, stored = 192.168.1.50, security level 3 => /24 mask
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['192.168.1.50']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpReturnsFalseForDifferentSubnet(): void
    {
        // REMOTE_ADDR = 192.168.1.100, stored = 10.0.0.1, security level 3 => /24 mask
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['10.0.0.1']);
        $this->assertFalse($result);
    }

    public function testValidateSessionIpReturnsTrueForMixedIpFamilies(): void
    {
        // REMOTE_ADDR = 192.168.1.100 (IPv4), stored = ::1 (IPv6)
        // Mixed families => returns true (does not enforce)
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['::1']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpReturnsTrueWhenRemoteAddrNotSet(): void
    {
        unset($_SERVER['REMOTE_ADDR']);
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['192.168.1.1']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpWithIpv6SameSubnet(): void
    {
        $_SERVER['REMOTE_ADDR'] = '2001:db8:85a3::8a2e:370:7334';
        // Security level 3 => /56 mask for v6
        // 2001:db8:85a3:00XX:: both share the first 56 bits
        $result = $this->invokePrivateMethod(
            $this->handler,
            'validateSessionIp',
            ['2001:db8:85a3::1']
        );
        $this->assertTrue($result);
    }

    public function testValidateSessionIpWithIpv6DifferentSubnet(): void
    {
        $_SERVER['REMOTE_ADDR'] = '2001:db8:85a3::8a2e:370:7334';
        // Security level 3 => /56 mask for v6
        // 2001:db8:FFFF:: has completely different bits at position 32+
        $result = $this->invokePrivateMethod(
            $this->handler,
            'validateSessionIp',
            ['2001:db8:ffff::1']
        );
        $this->assertFalse($result);
    }

    public function testValidateSessionIpReturnsTrueForInvalidSecurityLevel(): void
    {
        // Security level 99 not in bitMasks => returns true
        $this->handler->securityLevel = 99;
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['10.0.0.1']);
        $this->assertTrue($result);
    }

    // =========================================================================
    // applyIpMask() -- private method via reflection
    // =========================================================================

    public function testApplyIpMaskZeroBitsZerosEverything(): void
    {
        $ipBin = inet_pton('192.168.1.100');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 0]);
        $this->assertSame(str_repeat(chr(0), 4), $result);
    }

    public function testApplyIpMask32BitsPreservesAllForIpv4(): void
    {
        $ipBin = inet_pton('192.168.1.100');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 32]);
        $this->assertSame($ipBin, $result);
    }

    public function testApplyIpMask24BitsForIpv4(): void
    {
        $ipBin = inet_pton('192.168.1.100');
        $expected = inet_pton('192.168.1.0');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 24]);
        $this->assertSame($expected, $result);
    }

    public function testApplyIpMask16BitsForIpv4(): void
    {
        $ipBin = inet_pton('192.168.1.100');
        $expected = inet_pton('192.168.0.0');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 16]);
        $this->assertSame($expected, $result);
    }

    public function testApplyIpMask8BitsForIpv4(): void
    {
        $ipBin = inet_pton('192.168.1.100');
        $expected = inet_pton('192.0.0.0');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 8]);
        $this->assertSame($expected, $result);
    }

    public function testApplyIpMaskPartialByteForIpv4(): void
    {
        // 192.168.1.100 with /25 mask
        // 192.168.1.100 => last byte 100 = 0b01100100
        // /25 means first 3 bytes kept, plus top 1 bit of 4th byte
        // 0b01100100 & 0b10000000 = 0b00000000 => 192.168.1.0
        $ipBin = inet_pton('192.168.1.100');
        $expected = inet_pton('192.168.1.0');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 25]);
        $this->assertSame($expected, $result);
    }

    public function testApplyIpMaskPartialByteHighBit(): void
    {
        // 192.168.1.200 with /25 mask
        // 200 = 0b11001000, top bit = 1 => 0b10000000 = 128
        // Result: 192.168.1.128
        $ipBin = inet_pton('192.168.1.200');
        $expected = inet_pton('192.168.1.128');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 25]);
        $this->assertSame($expected, $result);
    }

    public function testApplyIpMask128BitsPreservesAllForIpv6(): void
    {
        $ipBin = inet_pton('2001:db8:85a3::8a2e:370:7334');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 128]);
        $this->assertSame($ipBin, $result);
    }

    public function testApplyIpMask64BitsForIpv6(): void
    {
        $ipBin = inet_pton('2001:db8:85a3::8a2e:370:7334');
        $expected = inet_pton('2001:db8:85a3::');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 64]);
        $this->assertSame($expected, $result);
    }

    public function testApplyIpMask56BitsForIpv6(): void
    {
        // /56 means first 7 full bytes kept
        // 2001:0db8:85a3:0000:... => first 7 bytes are: 20 01 0d b8 85 a3 00
        // 8th byte (index 7) gets 0 remaining bits => zeroed
        $ipBin = inet_pton('2001:db8:85a3:00ff:1234:5678:9abc:def0');
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, 56]);
        $expected = inet_pton('2001:db8:85a3::');
        $this->assertSame($expected, $result);
    }

    // =========================================================================
    // gc_force() -- should not throw
    // =========================================================================

    public function testGcForceDoesNotThrow(): void
    {
        // gc_force calls random_int and may or may not call gc()
        // We just verify it completes without exception
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getAffectedRows')->willReturn(0);

        // Call multiple times to exercise both branches (gc triggered and not)
        for ($i = 0; $i < 20; $i++) {
            $this->handler->gc_force();
        }
        // If we get here without exceptions, the test passes
        $this->assertTrue(true);
    }

    // =========================================================================
    // update_cookie() -- no-op on PHP 8.0+
    // =========================================================================

    public function testUpdateCookieIsNoOp(): void
    {
        // update_cookie() is a no-op, should return void and not throw
        $this->handler->update_cookie();
        $this->handler->update_cookie('custom_sess_id');
        $this->handler->update_cookie(null, 3600);
        $this->handler->update_cookie('sess_id', 0);

        // If we get here without errors, the test passes
        $this->assertTrue(true);
    }

    // =========================================================================
    // Interface implementation checks
    // =========================================================================

    public function testImplementsSessionHandlerInterface(): void
    {
        $this->assertInstanceOf(\SessionHandlerInterface::class, $this->handler);
    }

    public function testImplementsSessionUpdateTimestampHandlerInterface(): void
    {
        $this->assertInstanceOf(\SessionUpdateTimestampHandlerInterface::class, $this->handler);
    }

    // =========================================================================
    // Default property values
    // =========================================================================

    public function testDefaultSecurityLevel(): void
    {
        $this->assertSame(3, $this->handler->securityLevel);
    }

    public function testDefaultEnableRegenerateId(): void
    {
        $this->assertTrue($this->handler->enableRegenerateId);
    }

    public function testBitMasksContainExpectedLevels(): void
    {
        $bitMasks = $this->getProtectedProperty($this->handler, 'bitMasks');

        $this->assertArrayHasKey(2, $bitMasks);
        $this->assertArrayHasKey(3, $bitMasks);
        $this->assertArrayHasKey(4, $bitMasks);

        $this->assertSame(['v4' => 16, 'v6' => 64], $bitMasks[2]);
        $this->assertSame(['v4' => 24, 'v6' => 56], $bitMasks[3]);
        $this->assertSame(['v4' => 32, 'v6' => 128], $bitMasks[4]);
    }

    // =========================================================================
    // read() verifies SQL construction
    // =========================================================================

    public function testReadBuildsCorrectSql(): void
    {
        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('queryF')
            ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return 'mock_result';
            });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(false);

        $this->handler->read('my_session_id');

        $this->assertStringContainsString('SELECT sess_data, sess_ip', $sqlCaptured);
        $this->assertStringContainsString('xoops_session', $sqlCaptured);
        $this->assertStringContainsString('my_session_id', $sqlCaptured);
    }

    // =========================================================================
    // validateId() verifies SQL construction
    // =========================================================================

    public function testValidateIdBuildsCorrectSql(): void
    {
        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('queryF')
            ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return 'mock_result';
            });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(['192.168.1.100']);

        $this->handler->validateId('check_this_session');

        $this->assertStringContainsString('SELECT sess_ip', $sqlCaptured);
        $this->assertStringContainsString('xoops_session', $sqlCaptured);
        $this->assertStringContainsString('check_this_session', $sqlCaptured);
    }

    // =========================================================================
    // validateSessionIp with security level 2 (broader subnet)
    // =========================================================================

    public function testValidateSessionIpLevel2SameSubnet(): void
    {
        $this->handler->securityLevel = 2;
        // REMOTE_ADDR = 192.168.1.100, stored = 192.168.200.5
        // Level 2 => /16 mask => same 192.168.x.x subnet
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['192.168.200.5']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpLevel2DifferentSubnet(): void
    {
        $this->handler->securityLevel = 2;
        // REMOTE_ADDR = 192.168.1.100, stored = 192.169.1.100
        // Level 2 => /16 mask => different subnet
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['192.169.1.100']);
        $this->assertFalse($result);
    }

    // =========================================================================
    // validateSessionIp with security level 4 (exact match)
    // =========================================================================

    public function testValidateSessionIpLevel4ExactMatch(): void
    {
        $this->handler->securityLevel = 4;
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['192.168.1.100']);
        $this->assertTrue($result);
    }

    public function testValidateSessionIpLevel4DiffersByOneBit(): void
    {
        $this->handler->securityLevel = 4;
        $result = $this->invokePrivateMethod($this->handler, 'validateSessionIp', ['192.168.1.101']);
        $this->assertFalse($result);
    }

    // =========================================================================
    // Data provider tests for applyIpMask
    // =========================================================================

    /**
     * @return array<string, array{string, int, string}>
     */
    public static function ipv4MaskProvider(): array
    {
        return [
            'slash-0'  => ['255.255.255.255', 0, '0.0.0.0'],
            'slash-8'  => ['172.16.254.1', 8, '172.0.0.0'],
            'slash-12' => ['172.16.254.1', 12, '172.16.0.0'],
            'slash-16' => ['172.16.254.1', 16, '172.16.0.0'],
            'slash-24' => ['172.16.254.1', 24, '172.16.254.0'],
            'slash-32' => ['172.16.254.1', 32, '172.16.254.1'],
        ];
    }

    #[DataProvider('ipv4MaskProvider')]
    public function testApplyIpMaskIpv4WithDataProvider(string $ip, int $bits, string $expected): void
    {
        $ipBin = inet_pton($ip);
        $expectedBin = inet_pton($expected);
        $result = $this->invokePrivateMethod($this->handler, 'applyIpMask', [$ipBin, $bits]);
        $this->assertSame($expectedBin, $result);
    }

    // =========================================================================
    // Edge case: read with empty session data stored
    // =========================================================================

    public function testReadReturnsEmptyStringWhenStoredDataIsEmpty(): void
    {
        $this->db->method('queryF')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn(['', '192.168.1.100']);

        $result = $this->handler->read('sess_with_empty_data');
        $this->assertSame('', $result);
    }

    // =========================================================================
    // Edge case: destroy verifies session ID in SQL
    // =========================================================================

    public function testDestroyIncludesSessionIdInSql(): void
    {
        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function (string $sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return true;
            });

        $this->handler->destroy('specific_session_id_12345');
        $this->assertStringContainsString('specific_session_id_12345', $sqlCaptured);
    }

    // =========================================================================
    // gc() SQL contains correct threshold
    // =========================================================================

    public function testGcSqlContainsCorrectTable(): void
    {
        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function (string $sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return true;
            });
        $this->db->method('getAffectedRows')->willReturn(0);

        $this->handler->gc(1800);
        $this->assertStringContainsString('xoops_session', $sqlCaptured);
    }

    // =========================================================================
    // Write includes IP address from REMOTE_ADDR
    // =========================================================================

    public function testWriteIncludesRemoteAddr(): void
    {
        $_SERVER['REMOTE_ADDR'] = '10.20.30.40';

        $sqlCaptured = null;
        $this->db->expects($this->once())
            ->method('exec')
            ->willReturnCallback(function (string $sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return true;
            });

        $this->handler->write('sess_ip_test', 'data');
        $this->assertStringContainsString('10.20.30.40', $sqlCaptured);
    }
}
