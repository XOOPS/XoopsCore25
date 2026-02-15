<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/online.php';

/**
 * Unit tests for XoopsOnlineHandler.
 *
 * XoopsOnlineHandler is NOT an XoopsObjectHandler -- it has its own structure
 * with a direct $db property and $table property set in the constructor.
 */
class XoopsOnlineHandlerTest extends KernelTestCase
{
    /** @var \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    /** @var \XoopsOnlineHandler */
    private $handler;

    protected function setUp(): void
    {
        $this->db = $this->createMockDatabase();
        $this->handler = new \XoopsOnlineHandler($this->db);
    }

    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorSetsDatabaseAndTable(): void
    {
        $this->assertSame($this->db, $this->handler->db);
        $this->assertSame('xoops_online', $this->handler->table);
    }

    // =========================================================================
    // write() -- new registered user inserts
    // =========================================================================

    public function testWriteNewUserInserts(): void
    {
        $uid = 5;
        $uname = 'testuser';
        $time = 1000000;
        $module = 1;
        $ip = '192.168.1.1';

        // queryF for the COUNT(*) check -- user not found
        $countResult = 'count_result';
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn($countResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn([0]);

        // exec called twice: first for DELETE anonymous row, then for INSERT
        $this->db->expects($this->exactly(2))
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->write($uid, $uname, $time, $module, $ip);
        $this->assertTrue($result);
    }

    public function testWriteExistingUserUpdates(): void
    {
        $uid = 5;
        $uname = 'testuser';
        $time = 1000000;
        $module = 2;
        $ip = '192.168.1.1';

        // queryF returns count > 0 -- user already online
        $countResult = 'count_result';
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn($countResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn([1]);

        // exec called once for UPDATE
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->write($uid, $uname, $time, $module, $ip);
        $this->assertTrue($result);
    }

    public function testWriteAnonymousNewInserts(): void
    {
        $uid = 0;
        $uname = '';
        $time = 1000000;
        $module = 1;
        $ip = '10.0.0.1';

        // queryF for COUNT(*) with uid=0 AND ip -- not found
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn('count_result');
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn([0]);

        // exec called once for INSERT (no DELETE for anonymous uid==0)
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->write($uid, $uname, $time, $module, $ip);
        $this->assertTrue($result);
    }

    public function testWriteAnonymousExistingUpdates(): void
    {
        $uid = 0;
        $uname = '';
        $time = 2000000;
        $module = 1;
        $ip = '10.0.0.1';

        // queryF for COUNT(*) with uid=0 AND ip -- found
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn('count_result');
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn([1]);

        // exec called once for UPDATE with uid=0 AND ip
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->write($uid, $uname, $time, $module, $ip);
        $this->assertTrue($result);
    }

    public function testWriteNewUserCleansUpAnonymousRow(): void
    {
        $uid = 10;
        $uname = 'newuser';
        $time = 1000000;
        $module = 1;
        $ip = '192.168.1.50';

        // queryF for COUNT(*) -- user not found (new sign-in)
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn('count_result');
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn([0]);

        // exec called twice: DELETE anonymous row, then INSERT new row
        $execCalls = [];
        $this->db->expects($this->exactly(2))
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$execCalls) {
                     $execCalls[] = $sql;
                     return true;
                 });

        $result = $this->handler->write($uid, $uname, $time, $module, $ip);
        $this->assertTrue($result);

        // First exec should be the DELETE for anonymous row
        $this->assertStringContainsString('DELETE', $execCalls[0]);
        $this->assertStringContainsString('online_uid = 0', $execCalls[0]);
        // Second exec should be the INSERT
        $this->assertStringContainsString('INSERT', $execCalls[1]);
    }

    public function testWriteDbFailOnQueryFThrowsException(): void
    {
        // queryF returns non-result-set -- throws RuntimeException
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn(false);
        $this->db->method('isResultSet')
                 ->willReturn(false);
        $this->db->method('error')
                 ->willReturn('connection error');

        $this->expectException(\RuntimeException::class);

        $this->handler->write(1, 'user', 100, 1, '1.2.3.4');
    }

    public function testWriteDbFailOnExecReturnsFalse(): void
    {
        // queryF succeeds, count=0, but INSERT exec fails
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn('count_result');
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn([1]);

        // UPDATE exec fails
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(false);

        $result = $this->handler->write(5, 'user', 100, 1, '1.2.3.4');
        $this->assertFalse($result);
    }

    // =========================================================================
    // destroy()
    // =========================================================================

    public function testDestroySuccess(): void
    {
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) {
                     $this->assertStringContainsString('DELETE', $sql);
                     $this->assertStringContainsString('online_uid', $sql);
                     return true;
                 });

        $result = $this->handler->destroy(42);
        $this->assertTrue($result);
    }

    public function testDestroyDbFailReturnsFalse(): void
    {
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(false);

        $result = $this->handler->destroy(42);
        $this->assertFalse($result);
    }

    // =========================================================================
    // gc()
    // =========================================================================

    public function testGcDeletesExpiredEntries(): void
    {
        $expire = 300;

        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) {
                     $this->assertStringContainsString('DELETE', $sql);
                     $this->assertStringContainsString('online_updated', $sql);
                     return true;
                 });

        $this->handler->gc($expire);
    }

    // =========================================================================
    // getAll()
    // =========================================================================

    public function testGetAllReturnsArrayOfRows(): void
    {
        $rows = [
            ['online_uid' => 1, 'online_uname' => 'admin', 'online_updated' => 100, 'online_ip' => '1.1.1.1', 'online_module' => 1],
            ['online_uid' => 2, 'online_uname' => 'user2', 'online_updated' => 200, 'online_ip' => '2.2.2.2', 'online_module' => 1],
        ];

        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')
                 ->willReturnOnConsecutiveCalls($rows[0], $rows[1], false);

        $result = $this->handler->getAll();
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]['online_uid']);
        $this->assertSame('user2', $result[1]['online_uname']);
    }

    public function testGetAllWithCriteria(): void
    {
        $criteria = new \Criteria('online_uid', 0, '>');

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getAll($criteria);
        $this->assertIsArray($result);
        $this->assertSame([], $result);
        $this->assertStringContainsString('online_uid', $sqlCaptured);
    }

    public function testGetAllEmptyResult(): void
    {
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getAll();
        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function testGetAllReturnsEmptyArrayOnQueryFailure(): void
    {
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getAll();
        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    // =========================================================================
    // getCount()
    // =========================================================================

    public function testGetCountReturnsInteger(): void
    {
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([15]);

        $result = $this->handler->getCount();
        $this->assertSame(15, $result);
    }

    public function testGetCountWithCriteria(): void
    {
        $criteria = new \Criteria('online_module', 3);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([5]);

        $result = $this->handler->getCount($criteria);
        $this->assertSame(5, $result);
        $this->assertStringContainsString('online_module', $sqlCaptured);
    }

    public function testGetCountReturnsZeroOnQueryFailure(): void
    {
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getCount();
        $this->assertSame(0, $result);
    }
}
