<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/group.php';

// Language constants needed by cleanVars
if (!defined('_XOBJ_ERR_REQUIRED')) {
    define('_XOBJ_ERR_REQUIRED', '%s is required');
}
if (!defined('_XOBJ_ERR_SHORTERTHAN')) {
    define('_XOBJ_ERR_SHORTERTHAN', '%s must be shorter than %d characters.');
}

/**
 * Comprehensive unit tests for XoopsGroup and XoopsGroupHandler.
 *
 * Tests both the data object (XoopsGroup) and the handler (XoopsGroupHandler),
 * using mocked database connections for all handler operations.
 */
class XoopsGroupTest extends KernelTestCase
{
    /** @var \XoopsGroupHandler */
    private $handler;

    /** @var \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    /**
     * Set up the handler with a mock database for handler tests.
     */
    private function setUpHandler(): void
    {
        $this->db = $this->createMockDatabase();
        $this->handler = $this->createHandler(\XoopsGroupHandler::class, $this->db);
        $this->setProtectedProperty($this->handler, 'table', 'xoops_groups');
    }

    // =========================================================================
    // XoopsGroup data object tests
    // =========================================================================

    public function testConstructorInitializesAllVars(): void
    {
        $group = new \XoopsGroup();
        $vars = $group->getVars();
        $this->assertArrayHasKey('groupid', $vars);
        $this->assertArrayHasKey('name', $vars);
        $this->assertArrayHasKey('description', $vars);
        $this->assertArrayHasKey('group_type', $vars);
    }

    public function testGroupidIsIntType(): void
    {
        $group = new \XoopsGroup();
        $this->assertSame(XOBJ_DTYPE_INT, $group->vars['groupid']['data_type']);
    }

    public function testNameIsTxtboxTypeRequired(): void
    {
        $group = new \XoopsGroup();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $group->vars['name']['data_type']);
        $this->assertTrue($group->vars['name']['required']);
        $this->assertSame(100, $group->vars['name']['maxlength']);
    }

    public function testDescriptionIsTxtareaType(): void
    {
        $group = new \XoopsGroup();
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $group->vars['description']['data_type']);
    }

    public function testGroupTypeIsOtherType(): void
    {
        $group = new \XoopsGroup();
        $this->assertSame(XOBJ_DTYPE_OTHER, $group->vars['group_type']['data_type']);
    }

    public function testIdMethodReturnsGroupid(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->assignVar('groupid', 5);
        $this->assertSame(5, $group->id());
    }

    public function testGroupidMethodReturnsGroupid(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->assignVar('groupid', 3);
        $this->assertSame(3, $group->groupid());
    }

    public function testNameMethodReturnsName(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->assignVar('name', 'Webmasters');
        $this->assertSame('Webmasters', $group->name());
    }

    public function testDescriptionMethodReturnsDescription(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->assignVar('description', 'The admin group');
        $result = $group->description('n');
        $this->assertSame('The admin group', $result);
    }

    public function testGroupTypeMethodReturnsGroupType(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->assignVar('group_type', 'Admin');
        $this->assertSame('Admin', $group->group_type('n'));
    }

    public function testGroupIsNotNewByDefault(): void
    {
        $group = new \XoopsGroup();
        $this->assertFalse($group->isNew());
    }

    public function testGroupClonePreservesVars(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->assignVar('groupid', 10);
        $group->assignVar('name', 'Original');

        $clone = $group->xoopsClone();
        $this->assertInstanceOf(\XoopsGroup::class, $clone);
        $this->assertTrue($clone->isNew());
        $this->assertSame(10, $clone->getVar('groupid', 'n'));
        $this->assertSame('Original', $clone->getVar('name', 'n'));
    }

    // =========================================================================
    // XoopsGroupHandler — create()
    // =========================================================================

    public function testCreateReturnsNewGroup(): void
    {
        $this->setUpHandler();
        $group = $this->handler->create();
        $this->assertInstanceOf(\XoopsGroup::class, $group);
        $this->assertTrue($group->isNew());
    }

    public function testCreateNotNew(): void
    {
        $this->setUpHandler();
        $group = $this->handler->create(false);
        $this->assertInstanceOf(\XoopsGroup::class, $group);
        $this->assertFalse($group->isNew());
    }

    // =========================================================================
    // XoopsGroupHandler — get()
    // =========================================================================

    public function testGetReturnsGroupOnSuccess(): void
    {
        $this->setUpHandler();
        $row = ['groupid' => 1, 'name' => 'Webmasters', 'description' => 'Admin group', 'group_type' => 'Admin'];
        $this->stubSingleRowResult($this->db, $row);

        $group = $this->handler->get(1);
        $this->assertInstanceOf(\XoopsGroup::class, $group);
    }

    public function testGetReturnsFalseOnNotFound(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(0);

        $result = $this->handler->get(999);
        $this->assertFalse($result);
    }

    public function testGetReturnsFalseOnZeroId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->get(0);
        $this->assertFalse($result);
    }

    public function testGetReturnsFalseOnNegativeId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->get(-1);
        $this->assertFalse($result);
    }

    public function testGetReturnsFalseOnDbFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->get(99);
        $this->assertFalse($result);
    }

    public function testGetCastsIdToInt(): void
    {
        $this->setUpHandler();
        $row = ['groupid' => 5, 'name' => 'Test', 'description' => '', 'group_type' => 'User'];
        $this->stubSingleRowResult($this->db, $row);

        $group = $this->handler->get('5');
        $this->assertInstanceOf(\XoopsGroup::class, $group);
    }

    public function testGetAssignsVarsToGroup(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $row = ['groupid' => 2, 'name' => 'Users', 'description' => 'Regular users', 'group_type' => 'User'];
        $this->stubSingleRowResult($this->db, $row);

        $group = $this->handler->get(2);
        $this->assertSame(2, $group->getVar('groupid', 'n'));
        $this->assertSame('Users', $group->getVar('name', 'n'));
    }

    // =========================================================================
    // XoopsGroupHandler — insert()
    // =========================================================================

    public function testInsertNewGroup(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->setNew();
        $group->setVar('name', 'New Group');
        $group->setVar('group_type', 'User');

        $this->db->expects($this->once())
                 ->method('genId')
                 ->willReturn(0);
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);
        $this->db->method('getInsertId')
                 ->willReturn(10);

        $result = $this->handler->insert($group);
        $this->assertTrue($result);
    }

    public function testInsertExistingGroup(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->assignVar('groupid', 5);
        $group->setVar('name', 'Updated Name');
        $group->setVar('group_type', 'Admin');

        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->insert($group);
        $this->assertTrue($result);
    }

    public function testInsertNonDirtyReturnsTrueWithoutQuery(): void
    {
        $this->setUpHandler();
        $group = new \XoopsGroup();
        // Not dirty, so insert should short-circuit to true
        $result = $this->handler->insert($group);
        $this->assertTrue($result);
    }

    public function testInsertWrongClassReturnsFalse(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->insert($obj);
        $this->assertFalse($result);
    }

    public function testInsertCleanVarsFailReturnsFalse(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->setNew();
        // 'name' is required but empty -> cleanVars will fail
        $group->setVar('name', '');
        $group->setVar('group_type', 'User');

        $result = $this->handler->insert($group);
        $this->assertFalse($result);
    }

    public function testInsertDbFailReturnsFalse(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->setNew();
        $group->setVar('name', 'Fail Group');
        $group->setVar('group_type', 'User');

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->insert($group);
        $this->assertFalse($result);
    }

    public function testInsertAssignsGroupidAfterInsert(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->setNew();
        $group->setVar('name', 'New Group');
        $group->setVar('group_type', 'User');

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getInsertId')->willReturn(42);

        $this->handler->insert($group);
        $this->assertSame(42, $group->getVar('groupid', 'n'));
    }

    public function testInsertWithGenIdReturningNonZero(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->setNew();
        $group->setVar('name', 'SeqGroup');
        $group->setVar('group_type', 'User');

        $this->db->method('genId')->willReturn(99);
        $this->db->method('exec')->willReturn(true);

        $this->handler->insert($group);
        $this->assertSame(99, $group->getVar('groupid', 'n'));
    }

    // =========================================================================
    // XoopsGroupHandler — delete()
    // =========================================================================

    public function testDeleteSuccess(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->assignVar('groupid', 5);

        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->delete($group);
        $this->assertTrue($result);
    }

    public function testDeleteWrongClassReturnsFalse(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->delete($obj);
        $this->assertFalse($result);
    }

    public function testDeleteDbFailReturnsFalse(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $group = new \XoopsGroup();
        $group->assignVar('groupid', 5);

        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->delete($group);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsGroupHandler — getObjects()
    // =========================================================================

    public function testGetObjectsReturnsArray(): void
    {
        $this->setUpHandler();
        $rows = [
            ['groupid' => 1, 'name' => 'Admin', 'description' => '', 'group_type' => 'Admin'],
            ['groupid' => 2, 'name' => 'Users', 'description' => '', 'group_type' => 'User'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\XoopsGroup::class, $result[0]);
        $this->assertInstanceOf(\XoopsGroup::class, $result[1]);
    }

    public function testGetObjectsWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('group_type', 'Admin');

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
        $this->assertStringContainsString('group_type', $sqlCaptured);
    }

    public function testGetObjectsWithIdAsKey(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $rows = [
            ['groupid' => 1, 'name' => 'Admin', 'description' => '', 'group_type' => 'Admin'],
            ['groupid' => 2, 'name' => 'Users', 'description' => '', 'group_type' => 'User'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects(null, true);
        $this->assertArrayHasKey(1, $result);
        $this->assertArrayHasKey(2, $result);
    }

    public function testGetObjectsEmptyResult(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getObjects();
        $this->assertSame([], $result);
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
    // Edge cases
    // =========================================================================

    public function testGroupNameMaxLengthEnforced(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->setVar('name', str_repeat('A', 101));
        $result = $group->cleanVars();
        $this->assertFalse($result);
        $this->assertNotEmpty($group->getErrors());
    }

    public function testGroupNameWithinMaxLengthPasses(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $group = new \XoopsGroup();
        $group->setVar('name', str_repeat('A', 100));
        $group->setVar('group_type', 'User');
        $result = $group->cleanVars();
        $this->assertTrue($result);
    }
}
