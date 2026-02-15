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
 * Unit tests for XoopsMembership and XoopsMembershipHandler.
 *
 * Tests both the data object (XoopsMembership) and the handler
 * (XoopsMembershipHandler), using mocked database connections.
 */
class XoopsMembershipTest extends KernelTestCase
{
    // =========================================================================
    // XoopsMembership data object tests
    // =========================================================================

    public function testConstructorInitializesAllVars(): void
    {
        $mship = new \XoopsMembership();
        $vars = $mship->getVars();
        $this->assertArrayHasKey('linkid', $vars);
        $this->assertArrayHasKey('groupid', $vars);
        $this->assertArrayHasKey('uid', $vars);
    }

    public function testLinkidIsIntType(): void
    {
        $mship = new \XoopsMembership();
        $this->assertSame(XOBJ_DTYPE_INT, $mship->vars['linkid']['data_type']);
    }

    public function testGroupidIsIntType(): void
    {
        $mship = new \XoopsMembership();
        $this->assertSame(XOBJ_DTYPE_INT, $mship->vars['groupid']['data_type']);
    }

    public function testUidIsIntType(): void
    {
        $mship = new \XoopsMembership();
        $this->assertSame(XOBJ_DTYPE_INT, $mship->vars['uid']['data_type']);
    }

    public function testAllVarsAreNotRequired(): void
    {
        $mship = new \XoopsMembership();
        $this->assertFalse($mship->vars['linkid']['required']);
        $this->assertFalse($mship->vars['groupid']['required']);
        $this->assertFalse($mship->vars['uid']['required']);
    }

    public function testAllVarsDefaultToNull(): void
    {
        $mship = new \XoopsMembership();
        $this->assertNull($mship->vars['linkid']['value']);
        $this->assertNull($mship->vars['groupid']['value']);
        $this->assertNull($mship->vars['uid']['value']);
    }

    public function testMembershipAssignVars(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $mship = new \XoopsMembership();
        $mship->assignVars(['linkid' => 1, 'groupid' => 2, 'uid' => 3]);
        $this->assertSame(1, $mship->getVar('linkid', 'n'));
        $this->assertSame(2, $mship->getVar('groupid', 'n'));
        $this->assertSame(3, $mship->getVar('uid', 'n'));
    }

    public function testMembershipClone(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $mship = new \XoopsMembership();
        $mship->assignVars(['linkid' => 10, 'groupid' => 5, 'uid' => 7]);

        $clone = $mship->xoopsClone();
        $this->assertInstanceOf(\XoopsMembership::class, $clone);
        $this->assertTrue($clone->isNew());
        $this->assertSame(10, $clone->getVar('linkid', 'n'));
    }

    // =========================================================================
    // XoopsMembershipHandler tests
    // =========================================================================

    /** @var \XoopsMembershipHandler */
    private $handler;
    /** @var \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    private function setUpHandler(): void
    {
        $this->db = $this->createMockDatabase();
        $this->handler = $this->createHandler(\XoopsMembershipHandler::class, $this->db);
        $this->setProtectedProperty($this->handler, 'table', 'xoops_groups_users_link');
    }

    // -------------------------------------------------------------------------
    // create()
    // -------------------------------------------------------------------------

    public function testCreateReturnsNewMembership(): void
    {
        $this->setUpHandler();
        $mship = $this->handler->create();
        $this->assertInstanceOf(\XoopsMembership::class, $mship);
        $this->assertTrue($mship->isNew());
    }

    public function testCreateWithFalseReturnsNotNewMembership(): void
    {
        $this->setUpHandler();
        $mship = $this->handler->create(false);
        $this->assertInstanceOf(\XoopsMembership::class, $mship);
        $this->assertFalse($mship->isNew());
    }

    // -------------------------------------------------------------------------
    // get()
    // -------------------------------------------------------------------------

    public function testGetReturnsMembershipOnValidId(): void
    {
        $this->setUpHandler();
        $row = ['linkid' => 1, 'groupid' => 2, 'uid' => 3];
        $this->stubSingleRowResult($this->db, $row);

        $mship = $this->handler->get(1);
        $this->assertInstanceOf(\XoopsMembership::class, $mship);
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

        $result = $this->handler->get(1);
        $this->assertFalse($result);
    }

    public function testGetReturnsFalseWhenNoRows(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(0);

        $result = $this->handler->get(999);
        $this->assertFalse($result);
    }

    public function testGetAssignsVarsCorrectly(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $row = ['linkid' => 10, 'groupid' => 3, 'uid' => 7];
        $this->stubSingleRowResult($this->db, $row);

        $mship = $this->handler->get(10);
        $this->assertSame(10, $mship->getVar('linkid', 'n'));
        $this->assertSame(3, $mship->getVar('groupid', 'n'));
        $this->assertSame(7, $mship->getVar('uid', 'n'));
    }

    // -------------------------------------------------------------------------
    // insert()
    // -------------------------------------------------------------------------

    public function testInsertReturnsFalseForNonMembershipObject(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->insert($obj);
        $this->assertFalse($result);
    }

    public function testInsertReturnsTrueWhenNotDirty(): void
    {
        $this->setUpHandler();
        $mship = new \XoopsMembership();
        $result = $this->handler->insert($mship);
        $this->assertTrue($result);
    }

    public function testInsertNewMembershipCallsExec(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $mship = new \XoopsMembership();
        $mship->setNew();
        $mship->setVar('groupid', 2);
        $mship->setVar('uid', 5);

        $this->db->method('genId')->willReturn(0);
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);
        $this->db->method('getInsertId')->willReturn(100);

        $result = $this->handler->insert($mship);
        $this->assertTrue($result);
    }

    public function testInsertAssignsLinkidAfterInsert(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $mship = new \XoopsMembership();
        $mship->setNew();
        $mship->setVar('groupid', 1);
        $mship->setVar('uid', 10);

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getInsertId')->willReturn(55);

        $this->handler->insert($mship);
        $this->assertSame(55, $mship->getVar('linkid', 'n'));
    }

    public function testInsertExistingMembershipCallsUpdate(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $mship = new \XoopsMembership();
        $mship->assignVar('linkid', 10);
        $mship->setVar('groupid', 3);
        $mship->setVar('uid', 8);

        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->insert($mship);
        $this->assertTrue($result);
    }

    public function testInsertReturnsFalseWhenExecFails(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $mship = new \XoopsMembership();
        $mship->setNew();
        $mship->setVar('groupid', 1);
        $mship->setVar('uid', 1);

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->insert($mship);
        $this->assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // delete()
    // -------------------------------------------------------------------------

    public function testDeleteReturnsFalseForNonMembershipObject(): void
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
        $mship = new \XoopsMembership();
        $mship->assignVar('linkid', 5);

        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->delete($mship);
        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseOnExecFailure(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $mship = new \XoopsMembership();
        $mship->assignVar('linkid', 5);

        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->delete($mship);
        $this->assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // getObjects()
    // -------------------------------------------------------------------------

    public function testGetObjectsReturnsEmptyArrayWhenNoResults(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getObjects();
        $this->assertSame([], $result);
    }

    public function testGetObjectsReturnsArrayOfMemberships(): void
    {
        $this->setUpHandler();
        $rows = [
            ['linkid' => 1, 'groupid' => 1, 'uid' => 10],
            ['linkid' => 2, 'groupid' => 1, 'uid' => 20],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\XoopsMembership::class, $result[0]);
    }

    public function testGetObjectsWithIdAsKey(): void
    {
        $this->setUpHandler();
        $rows = [
            ['linkid' => 5, 'groupid' => 1, 'uid' => 10],
            ['linkid' => 6, 'groupid' => 2, 'uid' => 10],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects(null, true);
        $this->assertArrayHasKey(5, $result);
        $this->assertArrayHasKey(6, $result);
    }

    public function testGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getObjects();
        $this->assertSame([], $result);
    }

    public function testGetObjectsWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('uid', 10);

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
        $this->assertStringContainsString('uid', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // getCount()
    // -------------------------------------------------------------------------

    public function testGetCountReturnsZeroOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);

        $result = $this->handler->getCount();
        $this->assertSame(0, $result);
    }

    public function testGetCountReturnsCorrectCount(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 5);

        $result = $this->handler->getCount();
        $this->assertSame(5, $result);
    }

    public function testGetCountWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('groupid', 2);

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
        $this->assertStringContainsString('groupid', $sqlCaptured);
    }

    public function testGetCountReturnsZeroWithNullResult(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(null);

        $result = $this->handler->getCount();
        $this->assertSame(0, $result);
    }

    // -------------------------------------------------------------------------
    // deleteAll()
    // -------------------------------------------------------------------------

    public function testDeleteAllReturnsTrueOnSuccess(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(true);

        $result = $this->handler->deleteAll();
        $this->assertTrue($result);
    }

    public function testDeleteAllReturnsFalseOnFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);

        $result = $this->handler->deleteAll();
        $this->assertFalse($result);
    }

    public function testDeleteAllWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('groupid', 5);

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteAll($criteria);
        $this->assertStringContainsString('DELETE', $sqlCaptured);
        $this->assertStringContainsString('groupid', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // getGroupsByUser()
    // -------------------------------------------------------------------------

    public function testGetGroupsByUserReturnsGroupIds(): void
    {
        $this->setUpHandler();
        $rows = [
            ['groupid' => 1],
            ['groupid' => 2],
            ['groupid' => 3],
        ];
        $returns = $rows;
        $returns[] = false;

        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')
                 ->willReturnOnConsecutiveCalls(...$returns);

        $result = $this->handler->getGroupsByUser(10);
        $this->assertSame([1, 2, 3], $result);
    }

    public function testGetGroupsByUserReturnsEmptyOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getGroupsByUser(10);
        $this->assertSame([], $result);
    }

    public function testGetGroupsByUserCastsUidToInt(): void
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

        $this->handler->getGroupsByUser('5');
        $this->assertStringContainsString('uid=5', $sqlCaptured);
    }

    public function testGetGroupsByUserReturnsEmptyForUserWithNoGroups(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getGroupsByUser(999);
        $this->assertSame([], $result);
    }

    // -------------------------------------------------------------------------
    // getUsersByGroup()
    // -------------------------------------------------------------------------

    public function testGetUsersByGroupReturnsUserIds(): void
    {
        $this->setUpHandler();
        $rows = [
            ['uid' => 10],
            ['uid' => 20],
            ['uid' => 30],
        ];
        $returns = $rows;
        $returns[] = false;

        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')
                 ->willReturnOnConsecutiveCalls(...$returns);

        $result = $this->handler->getUsersByGroup(1);
        $this->assertSame([10, 20, 30], $result);
    }

    public function testGetUsersByGroupReturnsEmptyOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getUsersByGroup(1);
        $this->assertSame([], $result);
    }

    public function testGetUsersByGroupCastsGroupIdToInt(): void
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

        $this->handler->getUsersByGroup('3');
        $this->assertStringContainsString('groupid=3', $sqlCaptured);
    }

    public function testGetUsersByGroupWithLimitAndStart(): void
    {
        $this->setUpHandler();
        $this->db->expects($this->once())
                 ->method('query')
                 ->with(
                     $this->anything(),
                     $this->equalTo(10),
                     $this->equalTo(5)
                 )
                 ->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getUsersByGroup(1, 10, 5);
    }

    public function testGetUsersByGroupReturnsEmptyForGroupWithNoMembers(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getUsersByGroup(999);
        $this->assertSame([], $result);
    }

    // =========================================================================
    // Edge cases and type safety
    // =========================================================================

    public function testInsertWithGenIdReturningNonZero(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $mship = new \XoopsMembership();
        $mship->setNew();
        $mship->setVar('groupid', 1);
        $mship->setVar('uid', 5);

        $this->db->method('genId')->willReturn(77);
        $this->db->method('exec')->willReturn(true);

        $this->handler->insert($mship);
        $this->assertSame(77, $mship->getVar('linkid', 'n'));
    }

    public function testGetObjectsReturnsMembershipInstancesNotGroups(): void
    {
        $this->setUpHandler();
        $rows = [
            ['linkid' => 1, 'groupid' => 1, 'uid' => 10],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();
        $this->assertNotInstanceOf(\XoopsGroup::class, $result[0]);
        $this->assertInstanceOf(\XoopsMembership::class, $result[0]);
    }

    public function testDeleteAllWithoutCriteriaDeletesAll(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteAll();
        $this->assertStringContainsString('DELETE FROM', $sqlCaptured);
        // Without criteria, no WHERE clause should be appended
        $this->assertStringNotContainsString('WHERE', $sqlCaptured);
    }
}
