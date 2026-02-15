<?php

declare(strict_types=1);

namespace kernel;

use XoopsNotification;
use XoopsNotificationHandler;
use XoopsObject;
use Criteria;
use CriteriaCompo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

// Notification constants normally loaded from include/notification_constants.php.
// Define them inline since that file has an XOOPS_ROOT_PATH guard using $GLOBALS['xoops'].
if (!defined('XOOPS_NOTIFICATION_MODE_SENDALWAYS')) {
    define('XOOPS_NOTIFICATION_MODE_SENDALWAYS', 0);
}
if (!defined('XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE')) {
    define('XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE', 1);
}
if (!defined('XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT')) {
    define('XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT', 2);
}
if (!defined('XOOPS_NOTIFICATION_MODE_WAITFORLOGIN')) {
    define('XOOPS_NOTIFICATION_MODE_WAITFORLOGIN', 3);
}
if (!defined('XOOPS_NOTIFICATION_METHOD_DISABLE')) {
    define('XOOPS_NOTIFICATION_METHOD_DISABLE', 0);
}
if (!defined('XOOPS_NOTIFICATION_METHOD_PM')) {
    define('XOOPS_NOTIFICATION_METHOD_PM', 1);
}
if (!defined('XOOPS_NOTIFICATION_METHOD_EMAIL')) {
    define('XOOPS_NOTIFICATION_METHOD_EMAIL', 2);
}
if (!defined('XOOPS_NOTIFICATION_DISABLE')) {
    define('XOOPS_NOTIFICATION_DISABLE', 0);
}
if (!defined('XOOPS_NOTIFICATION_ENABLEBLOCK')) {
    define('XOOPS_NOTIFICATION_ENABLEBLOCK', 1);
}
if (!defined('XOOPS_NOTIFICATION_ENABLEINLINE')) {
    define('XOOPS_NOTIFICATION_ENABLEINLINE', 2);
}
if (!defined('XOOPS_NOTIFICATION_ENABLEBOTH')) {
    define('XOOPS_NOTIFICATION_ENABLEBOTH', 3);
}

// Language constants needed by cleanVars
if (!defined('_XOBJ_ERR_REQUIRED')) {
    define('_XOBJ_ERR_REQUIRED', '%s is required');
}
if (!defined('_XOBJ_ERR_SHORTERTHAN')) {
    define('_XOBJ_ERR_SHORTERTHAN', '%s must be shorter than %d characters.');
}

require_once XOOPS_ROOT_PATH . '/kernel/notification.php';

/**
 * Unit tests for XoopsNotification and XoopsNotificationHandler.
 */
#[CoversClass(XoopsNotification::class)]
#[CoversClass(XoopsNotificationHandler::class)]
class XoopsNotificationTest extends KernelTestCase
{
    /** @var \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    /** @var XoopsNotificationHandler */
    private $handler;

    private function setUpHandler(): void
    {
        $this->db = $this->createMockDatabase();
        $this->handler = $this->createHandler(XoopsNotificationHandler::class, $this->db);
    }

    // =========================================================================
    // XoopsNotification -- constructor / initVar
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $notification = new XoopsNotification();
        $this->assertInstanceOf(XoopsNotification::class, $notification);
        $this->assertInstanceOf(XoopsObject::class, $notification);
    }

    public function testConstructorSetsIsNewToFalse(): void
    {
        $notification = new XoopsNotification();
        $this->assertFalse($notification->isNew());
    }

    public function testConstructorInitializesAllVars(): void
    {
        $notification = new XoopsNotification();
        $vars = $notification->getVars();

        $expectedVars = [
            'not_id', 'not_modid', 'not_category',
            'not_itemid', 'not_event', 'not_uid', 'not_mode',
        ];

        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testConstructorVarCount(): void
    {
        $notification = new XoopsNotification();
        $vars = $notification->getVars();
        $this->assertCount(7, $vars);
    }

    // =========================================================================
    // XoopsNotification -- data types and properties
    // =========================================================================

    public function testNotIdIsIntType(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(XOBJ_DTYPE_INT, $notification->vars['not_id']['data_type']);
    }

    public function testNotIdIsNotRequired(): void
    {
        $notification = new XoopsNotification();
        $this->assertFalse($notification->vars['not_id']['required']);
    }

    public function testNotModidIsIntType(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(XOBJ_DTYPE_INT, $notification->vars['not_modid']['data_type']);
    }

    public function testNotModidIsNotRequired(): void
    {
        $notification = new XoopsNotification();
        $this->assertFalse($notification->vars['not_modid']['required']);
    }

    public function testNotCategoryIsTxtboxType(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $notification->vars['not_category']['data_type']);
    }

    public function testNotCategoryMaxLength(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(30, $notification->vars['not_category']['maxlength']);
    }

    public function testNotCategoryIsNotRequired(): void
    {
        $notification = new XoopsNotification();
        $this->assertFalse($notification->vars['not_category']['required']);
    }

    public function testNotItemidIsIntType(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(XOBJ_DTYPE_INT, $notification->vars['not_itemid']['data_type']);
    }

    public function testNotItemidDefaultValue(): void
    {
        $notification = new XoopsNotification();
        $this->assertEquals(0, $notification->getVar('not_itemid'));
    }

    public function testNotEventIsTxtboxType(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $notification->vars['not_event']['data_type']);
    }

    public function testNotEventMaxLength(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(30, $notification->vars['not_event']['maxlength']);
    }

    public function testNotEventIsNotRequired(): void
    {
        $notification = new XoopsNotification();
        $this->assertFalse($notification->vars['not_event']['required']);
    }

    public function testNotUidIsIntType(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(XOBJ_DTYPE_INT, $notification->vars['not_uid']['data_type']);
    }

    public function testNotUidIsRequired(): void
    {
        $notification = new XoopsNotification();
        $this->assertTrue($notification->vars['not_uid']['required']);
    }

    public function testNotUidDefaultValue(): void
    {
        $notification = new XoopsNotification();
        $this->assertEquals(0, $notification->getVar('not_uid'));
    }

    public function testNotModeIsIntType(): void
    {
        $notification = new XoopsNotification();
        $this->assertSame(XOBJ_DTYPE_INT, $notification->vars['not_mode']['data_type']);
    }

    public function testNotModeIsNotRequired(): void
    {
        $notification = new XoopsNotification();
        $this->assertFalse($notification->vars['not_mode']['required']);
    }

    public function testNotModeDefaultValue(): void
    {
        $notification = new XoopsNotification();
        $this->assertEquals(0, $notification->getVar('not_mode'));
    }

    // =========================================================================
    // XoopsNotification -- accessor methods
    // =========================================================================

    public function testIdAccessorReturnsNotId(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 42);
        $this->assertEquals(42, $notification->id());
    }

    public function testIdAccessorDefaultFormatIsN(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 99);
        // id() defaults to 'N' format
        $this->assertSame(99, $notification->id('N'));
    }

    public function testNotIdAccessor(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 7);
        $this->assertEquals(7, $notification->not_id());
    }

    public function testNotModidAccessor(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_modid', 3);
        $this->assertEquals(3, $notification->not_modid());
    }

    public function testNotCategoryAccessor(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_category', 'thread');
        $this->assertEquals('thread', $notification->not_category());
    }

    public function testNotItemidAccessor(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_itemid', 55);
        $this->assertEquals(55, $notification->not_itemid());
    }

    public function testNotEventAccessor(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_event', 'new_post');
        $this->assertEquals('new_post', $notification->not_event());
    }

    public function testNotUidAccessor(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_uid', 10);
        $this->assertEquals(10, $notification->not_uid());
    }

    public function testNotModeAccessor(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_mode', 2);
        $this->assertEquals(2, $notification->not_mode());
    }

    // =========================================================================
    // XoopsNotification -- setVar / getVar
    // =========================================================================

    public function testSetVarAndGetVarForNotId(): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_id', 100);
        $this->assertEquals(100, $notification->getVar('not_id'));
    }

    public function testSetVarAndGetVarForNotModid(): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_modid', 5);
        $this->assertEquals(5, $notification->getVar('not_modid'));
    }

    public function testSetVarAndGetVarForNotCategory(): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_category', 'article');
        $this->assertEquals('article', $notification->getVar('not_category'));
    }

    public function testSetVarAndGetVarForNotItemid(): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_itemid', 200);
        $this->assertEquals(200, $notification->getVar('not_itemid'));
    }

    public function testSetVarAndGetVarForNotEvent(): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_event', 'approve');
        $this->assertEquals('approve', $notification->getVar('not_event'));
    }

    public function testSetVarAndGetVarForNotUid(): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_uid', 15);
        $this->assertEquals(15, $notification->getVar('not_uid'));
    }

    public function testSetVarAndGetVarForNotMode(): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT);
        $this->assertEquals(XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT, $notification->getVar('not_mode'));
    }

    // =========================================================================
    // XoopsNotification -- assignVars
    // =========================================================================

    public function testAssignVarsSetsNotificationData(): void
    {
        $notification = new XoopsNotification();
        $data = [
            'not_id'       => 50,
            'not_modid'    => 2,
            'not_category' => 'forum',
            'not_itemid'   => 10,
            'not_event'    => 'reply',
            'not_uid'      => 7,
            'not_mode'     => 1,
        ];
        $notification->assignVars($data);

        $this->assertEquals(50, $notification->getVar('not_id'));
        $this->assertEquals(2, $notification->getVar('not_modid'));
        $this->assertEquals('forum', $notification->getVar('not_category'));
        $this->assertEquals(10, $notification->getVar('not_itemid'));
        $this->assertEquals('reply', $notification->getVar('not_event'));
        $this->assertEquals(7, $notification->getVar('not_uid'));
        $this->assertEquals(1, $notification->getVar('not_mode'));
    }

    // =========================================================================
    // XoopsNotification -- public properties (PHP 8.2 compat)
    // =========================================================================

    public function testPublicPropertiesAreAccessible(): void
    {
        $notification = new XoopsNotification();

        $notification->not_id       = 1;
        $notification->not_modid    = 2;
        $notification->not_category = 'test';
        $notification->not_itemid   = 3;
        $notification->not_event    = 'event';
        $notification->not_uid      = 4;
        $notification->not_mode     = 5;

        $this->assertEquals(1, $notification->not_id);
        $this->assertEquals(2, $notification->not_modid);
        $this->assertEquals('test', $notification->not_category);
        $this->assertEquals(3, $notification->not_itemid);
        $this->assertEquals('event', $notification->not_event);
        $this->assertEquals(4, $notification->not_uid);
        $this->assertEquals(5, $notification->not_mode);
    }

    // =========================================================================
    // XoopsNotification -- setVar marks dirty
    // =========================================================================

    public function testSetVarMarksDirty(): void
    {
        $notification = new XoopsNotification();
        $this->assertFalse($notification->isDirty());

        $notification->setVar('not_uid', 1);
        $this->assertTrue($notification->isDirty());
    }

    public function testAssignVarDoesNotMarkDirty(): void
    {
        $notification = new XoopsNotification();
        $notification->assignVar('not_uid', 1);
        $this->assertFalse($notification->isDirty());
    }

    // =========================================================================
    // XoopsNotificationHandler -- create()
    // =========================================================================

    public function testCreateReturnsNewNotification(): void
    {
        $this->setUpHandler();
        $notification = $this->handler->create();
        $this->assertInstanceOf(XoopsNotification::class, $notification);
        $this->assertTrue($notification->isNew());
    }

    public function testCreateWithTrueReturnsNewNotification(): void
    {
        $this->setUpHandler();
        $notification = $this->handler->create(true);
        $this->assertInstanceOf(XoopsNotification::class, $notification);
        $this->assertTrue($notification->isNew());
    }

    public function testCreateWithFalseReturnsNotNew(): void
    {
        $this->setUpHandler();
        $notification = $this->handler->create(false);
        $this->assertInstanceOf(XoopsNotification::class, $notification);
        $this->assertFalse($notification->isNew());
    }

    public function testCreateReturnsObjectWithDefaultVars(): void
    {
        $this->setUpHandler();
        $notification = $this->handler->create();
        $this->assertEquals(0, $notification->getVar('not_itemid'));
        $this->assertEquals(0, $notification->getVar('not_uid'));
        $this->assertEquals(0, $notification->getVar('not_mode'));
    }

    // =========================================================================
    // XoopsNotificationHandler -- get()
    // =========================================================================

    public function testGetReturnsNotificationOnValidId(): void
    {
        $this->setUpHandler();
        $row = [
            'not_id'       => 1,
            'not_modid'    => 2,
            'not_category' => 'thread',
            'not_itemid'   => 10,
            'not_event'    => 'new_post',
            'not_uid'      => 5,
            'not_mode'     => 0,
        ];
        $this->stubSingleRowResult($this->db, $row);

        $notification = $this->handler->get(1);
        $this->assertInstanceOf(XoopsNotification::class, $notification);
        $this->assertEquals(1, $notification->getVar('not_id'));
        $this->assertEquals('thread', $notification->getVar('not_category'));
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

    public function testGetReturnsFalseWhenNoRowsFound(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(0);

        $result = $this->handler->get(999);
        $this->assertFalse($result);
    }

    public function testGetReturnsFalseWhenMultipleRowsFound(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(2);

        $result = $this->handler->get(1);
        $this->assertFalse($result);
    }

    public function testGetCastsIdToInt(): void
    {
        $this->setUpHandler();
        $row = [
            'not_id'       => 5,
            'not_modid'    => 1,
            'not_category' => 'article',
            'not_itemid'   => 1,
            'not_event'    => 'new',
            'not_uid'      => 3,
            'not_mode'     => 0,
        ];
        $this->stubSingleRowResult($this->db, $row);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });

        // Passing a string-like int to verify it gets cast
        $this->handler->get(5);
        $this->assertStringContainsString('not_id=5', $sqlCaptured);
    }

    // =========================================================================
    // XoopsNotificationHandler -- insert() -- new notification
    // =========================================================================

    public function testInsertNewNotificationSuccess(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();

        $notification = new XoopsNotification();
        $notification->setNew();
        $notification->setVar('not_modid', 1);
        $notification->setVar('not_category', 'forum');
        $notification->setVar('not_itemid', 10);
        $notification->setVar('not_event', 'reply');
        $notification->setVar('not_uid', 5);
        $notification->setVar('not_mode', 0);

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getInsertId')->willReturn(42);

        $result = $this->handler->insert($notification);
        $this->assertTrue($result);
        $this->assertEquals(42, $notification->getVar('not_id'));
    }

    public function testInsertNewNotificationWithGenIdReturnsId(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();

        $notification = new XoopsNotification();
        $notification->setNew();
        $notification->setVar('not_modid', 1);
        $notification->setVar('not_category', 'topic');
        $notification->setVar('not_itemid', 5);
        $notification->setVar('not_event', 'new');
        $notification->setVar('not_uid', 3);
        $notification->setVar('not_mode', 0);

        $this->db->method('genId')->willReturn(77);
        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->insert($notification);
        $this->assertTrue($result);
        $this->assertEquals(77, $notification->getVar('not_id'));
    }

    public function testInsertNewNotificationGeneratesInsertSql(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();

        $notification = new XoopsNotification();
        $notification->setNew();
        $notification->setVar('not_modid', 2);
        $notification->setVar('not_category', 'thread');
        $notification->setVar('not_itemid', 20);
        $notification->setVar('not_event', 'new_post');
        $notification->setVar('not_uid', 8);
        $notification->setVar('not_mode', 1);

        $this->db->method('genId')->willReturn(0);
        $this->db->method('getInsertId')->willReturn(10);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->insert($notification);
        $this->assertStringContainsString('INSERT INTO', $sqlCaptured);
        $this->assertStringContainsString('xoops_xoopsnotifications', $sqlCaptured);
    }

    // =========================================================================
    // XoopsNotificationHandler -- insert() -- update existing
    // =========================================================================

    public function testInsertUpdateExistingNotification(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();

        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 10);
        $notification->setVar('not_modid', 1);
        $notification->setVar('not_category', 'forum');
        $notification->setVar('not_itemid', 5);
        $notification->setVar('not_event', 'reply');
        $notification->setVar('not_uid', 3);
        $notification->setVar('not_mode', 2);

        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->insert($notification);
        $this->assertTrue($result);
    }

    public function testInsertUpdateGeneratesUpdateSql(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();

        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 25);
        $notification->setVar('not_modid', 3);
        $notification->setVar('not_category', 'article');
        $notification->setVar('not_itemid', 15);
        $notification->setVar('not_event', 'approve');
        $notification->setVar('not_uid', 6);
        $notification->setVar('not_mode', 0);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->insert($notification);
        $this->assertStringContainsString('UPDATE', $sqlCaptured);
        $this->assertStringContainsString('WHERE not_id', $sqlCaptured);
    }

    // =========================================================================
    // XoopsNotificationHandler -- insert() -- error cases
    // =========================================================================

    public function testInsertReturnsFalseForNonNotificationObject(): void
    {
        $this->setUpHandler();
        $obj = new XoopsObject();
        $result = $this->handler->insert($obj);
        $this->assertFalse($result);
    }

    public function testInsertReturnsTrueWhenNotDirty(): void
    {
        $this->setUpHandler();
        $notification = new XoopsNotification();
        // Not dirty -- short-circuit returns true
        $result = $this->handler->insert($notification);
        $this->assertTrue($result);
    }

    public function testInsertReturnsFalseWhenExecFails(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();

        $notification = new XoopsNotification();
        $notification->setNew();
        $notification->setVar('not_modid', 1);
        $notification->setVar('not_category', 'test');
        $notification->setVar('not_itemid', 1);
        $notification->setVar('not_event', 'test');
        $notification->setVar('not_uid', 1);
        $notification->setVar('not_mode', 0);

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->insert($notification);
        $this->assertFalse($result);
    }

    public function testInsertReturnsFalseWhenCleanVarsFails(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();

        $notification = new XoopsNotification();
        $notification->setNew();
        // not_uid is required but leave it at null/default without explicitly setting it
        // We need to make it dirty without satisfying the required field.
        // Actually not_uid has a default of 0, so it won't fail on required.
        // Instead, set not_category to a string longer than maxlength (30)
        $notification->setVar('not_category', str_repeat('x', 50));
        $notification->setVar('not_uid', 1);

        $result = $this->handler->insert($notification);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsNotificationHandler -- delete()
    // =========================================================================

    public function testDeleteReturnsFalseForNonNotificationObject(): void
    {
        $this->setUpHandler();
        $obj = new XoopsObject();
        $result = $this->handler->delete($obj);
        $this->assertFalse($result);
    }

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        $this->setUpHandler();
        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 10);

        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->delete($notification);
        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseOnExecFailure(): void
    {
        $this->setUpHandler();
        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 10);

        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->delete($notification);
        $this->assertFalse($result);
    }

    public function testDeleteUsesCorrectSql(): void
    {
        $this->setUpHandler();
        $notification = new XoopsNotification();
        $notification->assignVar('not_id', 33);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->delete($notification);
        $this->assertStringContainsString('DELETE FROM', $sqlCaptured);
        $this->assertStringContainsString('xoops_xoopsnotifications', $sqlCaptured);
        $this->assertStringContainsString('not_id = 33', $sqlCaptured);
    }

    // =========================================================================
    // XoopsNotificationHandler -- getObjects()
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

    public function testGetObjectsReturnsArrayOfNotifications(): void
    {
        $this->setUpHandler();
        $rows = [
            ['not_id' => 1, 'not_modid' => 2, 'not_category' => 'thread', 'not_itemid' => 10, 'not_event' => 'new_post', 'not_uid' => 5, 'not_mode' => 0],
            ['not_id' => 2, 'not_modid' => 2, 'not_category' => 'thread', 'not_itemid' => 11, 'not_event' => 'reply', 'not_uid' => 6, 'not_mode' => 1],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();
        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsNotification::class, $result[0]);
        $this->assertInstanceOf(XoopsNotification::class, $result[1]);
    }

    public function testGetObjectsWithIdAsKey(): void
    {
        $this->setUpHandler();
        $rows = [
            ['not_id' => 10, 'not_modid' => 1, 'not_category' => 'forum', 'not_itemid' => 5, 'not_event' => 'new', 'not_uid' => 3, 'not_mode' => 0],
            ['not_id' => 20, 'not_modid' => 1, 'not_category' => 'forum', 'not_itemid' => 6, 'not_event' => 'reply', 'not_uid' => 4, 'not_mode' => 0],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects(null, true);
        $this->assertArrayHasKey(10, $result);
        $this->assertArrayHasKey(20, $result);
        $this->assertInstanceOf(XoopsNotification::class, $result[10]);
        $this->assertInstanceOf(XoopsNotification::class, $result[20]);
    }

    public function testGetObjectsWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new Criteria('not_uid', 5);

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
        $this->assertStringContainsString('not_uid', $sqlCaptured);
    }

    public function testGetObjectsWithSortCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new Criteria('not_modid', 1);
        $criteria->setSort('not_uid');

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
        $this->assertStringContainsString('ORDER BY not_uid', $sqlCaptured);
    }

    public function testGetObjectsDefaultSortIsNotId(): void
    {
        $this->setUpHandler();
        $criteria = new Criteria('not_modid', 1);
        // Do not set sort, should default to not_id

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
        $this->assertStringContainsString('ORDER BY not_id', $sqlCaptured);
    }

    public function testGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getObjects();
        $this->assertSame([], $result);
    }

    public function testGetObjectsWithNullCriteria(): void
    {
        $this->setUpHandler();

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getObjects(null);
        // Without criteria, no WHERE or ORDER BY clause
        $this->assertStringNotContainsString('WHERE', $sqlCaptured);
        $this->assertStringNotContainsString('ORDER BY', $sqlCaptured);
    }

    // =========================================================================
    // XoopsNotificationHandler -- getCount()
    // =========================================================================

    public function testGetCountReturnsInteger(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 12);

        $result = $this->handler->getCount();
        $this->assertSame(12, $result);
    }

    public function testGetCountWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new Criteria('not_uid', 5);

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
        $this->assertStringContainsString('not_uid', $sqlCaptured);
    }

    public function testGetCountReturnsZeroOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getCount();
        $this->assertSame(0, $result);
    }

    public function testGetCountWithNullCriteria(): void
    {
        $this->setUpHandler();

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([25]);

        $result = $this->handler->getCount(null);
        $this->assertSame(25, $result);
        $this->assertStringContainsString('SELECT COUNT(*)', $sqlCaptured);
        $this->assertStringNotContainsString('WHERE', $sqlCaptured);
    }

    public function testGetCountWithCriteriaCompo(): void
    {
        $this->setUpHandler();
        $criteria = new CriteriaCompo(new Criteria('not_modid', 2));
        $criteria->add(new Criteria('not_uid', 5));

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([2]);

        $result = $this->handler->getCount($criteria);
        $this->assertSame(2, $result);
        $this->assertStringContainsString('not_modid', $sqlCaptured);
        $this->assertStringContainsString('not_uid', $sqlCaptured);
    }

    // =========================================================================
    // XoopsNotificationHandler -- deleteAll()
    // =========================================================================

    public function testDeleteAllReturnsTrueOnSuccess(): void
    {
        $this->setUpHandler();
        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->deleteAll();
        $this->assertTrue($result);
    }

    public function testDeleteAllReturnsFalseOnFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->deleteAll();
        $this->assertFalse($result);
    }

    public function testDeleteAllWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new Criteria('not_modid', 5);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->deleteAll($criteria);
        $this->assertTrue($result);
        $this->assertStringContainsString('DELETE FROM', $sqlCaptured);
        $this->assertStringContainsString('not_modid', $sqlCaptured);
    }

    public function testDeleteAllWithNullCriteriaDeletesAll(): void
    {
        $this->setUpHandler();

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteAll(null);
        $this->assertStringContainsString('DELETE FROM', $sqlCaptured);
        $this->assertStringNotContainsString('WHERE', $sqlCaptured);
    }

    public function testDeleteAllWithCriteriaCompo(): void
    {
        $this->setUpHandler();
        $criteria = new CriteriaCompo(new Criteria('not_modid', 2));
        $criteria->add(new Criteria('not_category', 'forum'));

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->deleteAll($criteria);
        $this->assertTrue($result);
        $this->assertStringContainsString('not_modid', $sqlCaptured);
        $this->assertStringContainsString('not_category', $sqlCaptured);
    }

    // =========================================================================
    // XoopsNotificationHandler -- notification mode constants
    // =========================================================================

    public function testNotificationModeConstants(): void
    {
        $this->assertSame(0, XOOPS_NOTIFICATION_MODE_SENDALWAYS);
        $this->assertSame(1, XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);
        $this->assertSame(2, XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT);
        $this->assertSame(3, XOOPS_NOTIFICATION_MODE_WAITFORLOGIN);
    }

    public function testNotificationMethodConstants(): void
    {
        $this->assertSame(0, XOOPS_NOTIFICATION_METHOD_DISABLE);
        $this->assertSame(1, XOOPS_NOTIFICATION_METHOD_PM);
        $this->assertSame(2, XOOPS_NOTIFICATION_METHOD_EMAIL);
    }

    // =========================================================================
    // Data provider tests
    // =========================================================================

    /**
     * @return array<string, array{string, mixed}>
     */
    public static function intFieldProvider(): array
    {
        return [
            'not_id'     => ['not_id', 100],
            'not_modid'  => ['not_modid', 5],
            'not_itemid' => ['not_itemid', 42],
            'not_uid'    => ['not_uid', 7],
            'not_mode'   => ['not_mode', 2],
        ];
    }

    #[DataProvider('intFieldProvider')]
    public function testSetAndGetIntFields(string $field, int $value): void
    {
        $notification = new XoopsNotification();
        $notification->setVar($field, $value);
        $this->assertEquals($value, $notification->getVar($field));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function txtboxFieldProvider(): array
    {
        return [
            'not_category' => ['not_category', 'forum'],
            'not_event'    => ['not_event', 'new_post'],
        ];
    }

    #[DataProvider('txtboxFieldProvider')]
    public function testSetAndGetTxtboxFields(string $field, string $value): void
    {
        $notification = new XoopsNotification();
        $notification->setVar($field, $value);
        $this->assertEquals($value, $notification->getVar($field));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function accessorMethodProvider(): array
    {
        return [
            'not_id'       => ['not_id'],
            'not_modid'    => ['not_modid'],
            'not_category' => ['not_category'],
            'not_itemid'   => ['not_itemid'],
            'not_event'    => ['not_event'],
            'not_uid'      => ['not_uid'],
            'not_mode'     => ['not_mode'],
        ];
    }

    #[DataProvider('accessorMethodProvider')]
    public function testAccessorMethodsExist(string $methodName): void
    {
        $notification = new XoopsNotification();
        $this->assertTrue(method_exists($notification, $methodName));
    }

    /**
     * @return array<string, array{int}>
     */
    public static function notificationModeProvider(): array
    {
        return [
            'send_always'            => [XOOPS_NOTIFICATION_MODE_SENDALWAYS],
            'send_once_then_delete'  => [XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE],
            'send_once_then_wait'    => [XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT],
            'wait_for_login'         => [XOOPS_NOTIFICATION_MODE_WAITFORLOGIN],
        ];
    }

    #[DataProvider('notificationModeProvider')]
    public function testSetAndGetNotificationMode(int $mode): void
    {
        $notification = new XoopsNotification();
        $notification->setVar('not_mode', $mode);
        $this->assertEquals($mode, $notification->getVar('not_mode'));
    }
}
