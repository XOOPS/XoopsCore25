<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/privmessage.php';

// Language constants needed by cleanVars
if (!defined('_XOBJ_ERR_REQUIRED')) {
    define('_XOBJ_ERR_REQUIRED', '%s is required');
}
if (!defined('_XOBJ_ERR_SHORTERTHAN')) {
    define('_XOBJ_ERR_SHORTERTHAN', '%s must be shorter than %d characters.');
}

/**
 * Unit tests for XoopsPrivmessage and XoopsPrivmessageHandler.
 */
class XoopsPrivmessageTest extends KernelTestCase
{
    /** @var \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    /** @var \XoopsPrivmessageHandler */
    private $handler;

    private function setUpHandler(): void
    {
        $this->db = $this->createMockDatabase();
        $this->handler = $this->createHandler(\XoopsPrivmessageHandler::class, $this->db);
    }

    // =========================================================================
    // XoopsPrivmessage -- object tests
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $pm = new \XoopsPrivmessage();
        $this->assertInstanceOf(\XoopsPrivmessage::class, $pm);
        $this->assertInstanceOf(\XoopsObject::class, $pm);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $pm = new \XoopsPrivmessage();
        $vars = $pm->getVars();

        $expectedVars = [
            'msg_id', 'msg_image', 'subject', 'from_userid',
            'to_userid', 'msg_time', 'msg_text', 'read_msg',
        ];

        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testMsgIdIsIntType(): void
    {
        $pm = new \XoopsPrivmessage();
        $this->assertSame(XOBJ_DTYPE_INT, $pm->vars['msg_id']['data_type']);
        $this->assertFalse($pm->vars['msg_id']['required']);
    }

    public function testSubjectIsRequired(): void
    {
        $pm = new \XoopsPrivmessage();
        $this->assertTrue($pm->vars['subject']['required']);
        $this->assertSame(255, $pm->vars['subject']['maxlength']);
    }

    public function testFromUseridIsRequired(): void
    {
        $pm = new \XoopsPrivmessage();
        $this->assertTrue($pm->vars['from_userid']['required']);
    }

    public function testToUseridIsRequired(): void
    {
        $pm = new \XoopsPrivmessage();
        $this->assertTrue($pm->vars['to_userid']['required']);
    }

    public function testMsgTextIsRequired(): void
    {
        $pm = new \XoopsPrivmessage();
        $this->assertTrue($pm->vars['msg_text']['required']);
    }

    public function testReadMsgDefaultsToZero(): void
    {
        $pm = new \XoopsPrivmessage();
        $this->assertEquals(0, $pm->getVar('read_msg'));
    }

    public function testIdAccessorReturnsMsgId(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_id', 42);
        $this->assertSame(42, $pm->id());
    }

    public function testMsgIdAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_id', 7);
        $this->assertSame(7, $pm->msg_id());
    }

    public function testSubjectAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('subject', 'Hello World');
        $this->assertSame('Hello World', $pm->subject('n'));
    }

    public function testFromUseridAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('from_userid', 5);
        $this->assertSame(5, $pm->from_userid());
    }

    public function testToUseridAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('to_userid', 10);
        $this->assertSame(10, $pm->to_userid());
    }

    public function testMsgTimeAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_time', 1234567890);
        $this->assertEquals(1234567890, $pm->msg_time());
    }

    public function testMsgTextAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_text', 'This is the body');
        $this->assertSame('This is the body', $pm->msg_text('n'));
    }

    public function testReadMsgAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('read_msg', 1);
        $this->assertSame(1, $pm->read_msg());
    }

    // =========================================================================
    // XoopsPrivmessageHandler -- create()
    // =========================================================================

    public function testCreateReturnsNewPrivmessage(): void
    {
        $this->setUpHandler();
        $pm = $this->handler->create();
        $this->assertInstanceOf(\XoopsPrivmessage::class, $pm);
        $this->assertTrue($pm->isNew());
    }

    public function testCreateWithFalseReturnsNotNew(): void
    {
        $this->setUpHandler();
        $pm = $this->handler->create(false);
        $this->assertInstanceOf(\XoopsPrivmessage::class, $pm);
        $this->assertFalse($pm->isNew());
    }

    // =========================================================================
    // XoopsPrivmessageHandler -- get()
    // =========================================================================

    public function testGetReturnsPrivmessageOnValidId(): void
    {
        $this->setUpHandler();
        $row = [
            'msg_id' => 1, 'msg_image' => 'icon.png', 'subject' => 'Test',
            'from_userid' => 2, 'to_userid' => 3, 'msg_time' => 100,
            'msg_text' => 'Body', 'read_msg' => 0,
        ];
        $this->stubSingleRowResult($this->db, $row);

        $pm = $this->handler->get(1);
        $this->assertInstanceOf(\XoopsPrivmessage::class, $pm);
    }

    public function testGetReturnsFalseForZeroId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->get(0);
        $this->assertFalse($result);
    }

    public function testGetReturnsFalseForNegativeId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->get(-5);
        $this->assertFalse($result);
    }

    public function testGetReturnsFalseWhenQueryFails(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->get(99);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsPrivmessageHandler -- insert()
    // =========================================================================

    public function testInsertReturnsFalseForNonPrivmessageObject(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->insert($obj);
        $this->assertFalse($result);
    }

    public function testInsertReturnsTrueWhenNotDirty(): void
    {
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        // Not dirty -- short-circuit
        $result = $this->handler->insert($pm);
        $this->assertTrue($result);
    }

    public function testInsertNewPrivmessageSuccess(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->setNew();
        $pm->setVar('subject', 'New message');
        $pm->setVar('from_userid', 1);
        $pm->setVar('to_userid', 2);
        $pm->setVar('msg_text', 'Hello');

        $this->db->method('genId')->willReturn(0);
        // Uses query (not queryF) since force is false by default
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('getInsertId')->willReturn(50);

        $result = $this->handler->insert($pm);
        $this->assertTrue($result);
        $this->assertEquals(50, $pm->getVar('msg_id'));
    }

    public function testInsertUpdateExistingPrivmessage(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_id', 10);
        $pm->setVar('subject', 'Updated subject');
        $pm->setVar('from_userid', 1);
        $pm->setVar('to_userid', 2);
        $pm->setVar('msg_text', 'Updated body');

        $this->db->method('query')->willReturn('mock_result');

        $result = $this->handler->insert($pm);
        $this->assertTrue($result);
    }

    public function testInsertWithForceUsesQueryF(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->setNew();
        $pm->setVar('subject', 'Forced message');
        $pm->setVar('from_userid', 1);
        $pm->setVar('to_userid', 2);
        $pm->setVar('msg_text', 'Force body');

        $this->db->method('genId')->willReturn(0);
        $this->db->expects($this->once())
                 ->method('queryF')
                 ->willReturn('mock_result');
        $this->db->method('getInsertId')->willReturn(55);

        $result = $this->handler->insert($pm, true);
        $this->assertTrue($result);
    }

    public function testInsertReturnsFalseWhenQueryFails(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->setNew();
        $pm->setVar('subject', 'Failing message');
        $pm->setVar('from_userid', 1);
        $pm->setVar('to_userid', 2);
        $pm->setVar('msg_text', 'Fail body');

        $this->db->method('genId')->willReturn(0);
        $this->db->method('query')->willReturn(false);

        $result = $this->handler->insert($pm);
        $this->assertFalse($result);
    }

    public function testInsertReturnsFalseWhenCleanVarsFails(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->setNew();
        // subject is required but missing
        $pm->setVar('from_userid', 1);
        $pm->setVar('to_userid', 2);
        $pm->setVar('msg_text', 'Body');

        $result = $this->handler->insert($pm);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsPrivmessageHandler -- delete()
    // =========================================================================

    public function testDeleteReturnsFalseForNonPrivmessageObject(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->delete($obj);
        $this->assertFalse($result);
    }

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_id', 10);

        // delete uses query() not exec()
        $this->db->method('query')->willReturn('mock_result');

        $result = $this->handler->delete($pm);
        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseOnQueryFailure(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_id', 10);

        $this->db->method('query')->willReturn(false);

        $result = $this->handler->delete($pm);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsPrivmessageHandler -- getObjects()
    // =========================================================================

    public function testGetObjectsReturnsEmptyArrayWhenNoResults(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getObjects();
        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function testGetObjectsReturnsArrayOfPrivmessages(): void
    {
        $this->setUpHandler();
        $rows = [
            ['msg_id' => 1, 'msg_image' => '', 'subject' => 'A', 'from_userid' => 1, 'to_userid' => 2, 'msg_time' => 100, 'msg_text' => 'Body A', 'read_msg' => 0],
            ['msg_id' => 2, 'msg_image' => '', 'subject' => 'B', 'from_userid' => 2, 'to_userid' => 1, 'msg_time' => 200, 'msg_text' => 'Body B', 'read_msg' => 1],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\XoopsPrivmessage::class, $result[0]);
    }

    public function testGetObjectsWithSortValidation(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('to_userid', 5);
        $criteria->setSort('msg_time');

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getObjects($criteria);
        $this->assertStringContainsString('ORDER BY msg_time', $sqlCaptured);
    }

    public function testGetObjectsInvalidSortDefaultsToMsgId(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('to_userid', 5);
        $criteria->setSort('invalid_column');

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getObjects($criteria);
        $this->assertStringContainsString('ORDER BY msg_id', $sqlCaptured);
    }

    public function testGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getObjects();
        $this->assertSame([], $result);
    }

    // =========================================================================
    // XoopsPrivmessageHandler -- getCount()
    // =========================================================================

    public function testGetCountReturnsInteger(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([7]);

        $result = $this->handler->getCount();
        $this->assertSame(7, $result);
    }

    public function testGetCountWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('to_userid', 5);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([3]);

        $result = $this->handler->getCount($criteria);
        $this->assertSame(3, $result);
        $this->assertStringContainsString('to_userid', $sqlCaptured);
    }

    public function testGetCountReturnsZeroOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getCount();
        $this->assertSame(0, $result);
    }

    // =========================================================================
    // XoopsPrivmessageHandler -- setRead()
    // =========================================================================

    public function testSetReadSuccess(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_id', 10);

        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) {
                     $this->assertStringContainsString('read_msg = 1', $sql);
                     $this->assertStringContainsString('msg_id', $sql);
                     return true;
                 });

        $result = $this->handler->setRead($pm);
        $this->assertTrue($result);
    }

    public function testSetReadReturnsFalseOnExecFailure(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $pm = new \XoopsPrivmessage();
        $pm->assignVar('msg_id', 10);

        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->setRead($pm);
        $this->assertFalse($result);
    }
}
