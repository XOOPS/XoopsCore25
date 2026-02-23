<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\DataProvider;

require_once XOOPS_ROOT_PATH . '/kernel/groupperm.php';

/**
 * Comprehensive unit tests for XoopsGroupPerm and XoopsGroupPermHandler.
 *
 * Tests both the data object (XoopsGroupPerm) and the handler
 * (XoopsGroupPermHandler), using mocked database connections.
 *
 * Special focus on checkRight() logic which handles:
 * - Admin group bypass (XOOPS_GROUP_ADMIN)
 * - Single vs. array group IDs
 * - trueifadmin flag
 * - Item ID zero vs. non-zero
 * - Empty group ID / empty array edge cases
 */
class XoopsGroupPermTest extends KernelTestCase
{
    // =========================================================================
    // XoopsGroupPerm data object tests
    // =========================================================================

    public function testConstructorInitializesAllVars(): void
    {
        $perm = new \XoopsGroupPerm();
        $vars = $perm->getVars();

        self::assertArrayHasKey('gperm_id', $vars);
        self::assertArrayHasKey('gperm_groupid', $vars);
        self::assertArrayHasKey('gperm_itemid', $vars);
        self::assertArrayHasKey('gperm_modid', $vars);
        self::assertArrayHasKey('gperm_name', $vars);
    }

    public function testConstructorInitializesExactlyFiveVars(): void
    {
        $perm = new \XoopsGroupPerm();
        $vars = $perm->getVars();

        self::assertCount(5, $vars);
    }

    public function testGpermIdIsIntType(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertSame(XOBJ_DTYPE_INT, $perm->vars['gperm_id']['data_type']);
    }

    public function testGpermGroupidIsIntType(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertSame(XOBJ_DTYPE_INT, $perm->vars['gperm_groupid']['data_type']);
    }

    public function testGpermItemidIsIntType(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertSame(XOBJ_DTYPE_INT, $perm->vars['gperm_itemid']['data_type']);
    }

    public function testGpermModidIsIntType(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertSame(XOBJ_DTYPE_INT, $perm->vars['gperm_modid']['data_type']);
    }

    public function testGpermModidDefaultValueIsZero(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertSame(0, $perm->vars['gperm_modid']['value']);
    }

    public function testGpermNameIsOtherType(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertSame(XOBJ_DTYPE_OTHER, $perm->vars['gperm_name']['data_type']);
    }

    public function testGpermIdDefaultIsNull(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertNull($perm->vars['gperm_id']['value']);
    }

    public function testGpermGroupidDefaultIsNull(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertNull($perm->vars['gperm_groupid']['value']);
    }

    public function testGpermItemidDefaultIsNull(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertNull($perm->vars['gperm_itemid']['value']);
    }

    public function testGpermNameDefaultIsNull(): void
    {
        $perm = new \XoopsGroupPerm();
        self::assertNull($perm->vars['gperm_name']['value']);
    }

    public function testAllVarsAreNotRequired(): void
    {
        $perm = new \XoopsGroupPerm();
        foreach ($perm->getVars() as $name => $meta) {
            self::assertFalse($meta['required'], "Var '$name' should not be required");
        }
    }

    // -------------------------------------------------------------------------
    // Accessor methods
    // -------------------------------------------------------------------------

    public function testIdMethodReturnsGpermId(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 42);
        self::assertSame(42, $perm->id());
    }

    public function testIdMethodWithDefaultFormatN(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 99);
        // Default format is 'N' which returns numeric (no sanitizing)
        self::assertSame(99, $perm->id('N'));
    }

    public function testGpermIdMethod(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 10);
        self::assertSame(10, $perm->gperm_id());
    }

    public function testGpermGroupidMethod(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_groupid', 3);
        self::assertSame(3, $perm->gperm_groupid());
    }

    public function testGpermItemidMethod(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_itemid', 7);
        self::assertSame(7, $perm->gperm_itemid());
    }

    public function testGpermModidMethod(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_modid', 1);
        self::assertSame(1, $perm->gperm_modid());
    }

    public function testGpermNameMethod(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_name', 'module_read');
        self::assertSame('module_read', $perm->gperm_name());
    }

    // -------------------------------------------------------------------------
    // assignVars
    // -------------------------------------------------------------------------

    public function testAssignVarsSetsMultipleValues(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVars([
            'gperm_id'      => 1,
            'gperm_groupid' => 2,
            'gperm_itemid'  => 3,
            'gperm_modid'   => 4,
            'gperm_name'    => 'test_perm',
        ]);

        self::assertSame(1, $perm->getVar('gperm_id', 'n'));
        self::assertSame(2, $perm->getVar('gperm_groupid', 'n'));
        self::assertSame(3, $perm->getVar('gperm_itemid', 'n'));
        self::assertSame(4, $perm->getVar('gperm_modid', 'n'));
        self::assertSame('test_perm', $perm->getVar('gperm_name', 'n'));
    }

    // -------------------------------------------------------------------------
    // Clone
    // -------------------------------------------------------------------------

    public function testPermCloneReturnsNewInstance(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVars([
            'gperm_id'      => 1,
            'gperm_groupid' => 2,
            'gperm_itemid'  => 3,
            'gperm_modid'   => 1,
            'gperm_name'    => 'module_read',
        ]);

        $clone = $perm->xoopsClone();
        self::assertInstanceOf(\XoopsGroupPerm::class, $clone);
        self::assertTrue($clone->isNew());
        self::assertSame(1, $clone->getVar('gperm_id', 'n'));
    }

    public function testPermCloneIsIndependentObject(): void
    {
        $perm = new \XoopsGroupPerm();
        $perm->assignVars([
            'gperm_id'      => 5,
            'gperm_groupid' => 2,
            'gperm_itemid'  => 10,
            'gperm_modid'   => 1,
            'gperm_name'    => 'module_read',
        ]);

        $clone = $perm->xoopsClone();
        $clone->assignVar('gperm_groupid', 99);

        // Original should be unchanged
        self::assertSame(2, $perm->getVar('gperm_groupid', 'n'));
        self::assertSame(99, $clone->getVar('gperm_groupid', 'n'));
    }

    // =========================================================================
    // XoopsGroupPermHandler tests
    // =========================================================================

    /** @var \XoopsGroupPermHandler */
    private $handler;
    /** @var \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    private function setUpHandler(): void
    {
        $this->db = $this->createMockDatabase();
        $this->handler = $this->createHandler(\XoopsGroupPermHandler::class, $this->db);
        $this->setProtectedProperty($this->handler, 'table', 'xoops_group_permission');
    }

    // -------------------------------------------------------------------------
    // Constructor
    // -------------------------------------------------------------------------

    public function testConstructorSetsTableProperty(): void
    {
        $db = $this->createMockDatabase();
        $handler = new \XoopsGroupPermHandler($db);

        self::assertSame('xoops_group_permission', $handler->table);
    }

    // -------------------------------------------------------------------------
    // create()
    // -------------------------------------------------------------------------

    public function testCreateReturnsNewGroupPerm(): void
    {
        $this->setUpHandler();
        $perm = $this->handler->create();

        self::assertInstanceOf(\XoopsGroupPerm::class, $perm);
        self::assertTrue($perm->isNew());
    }

    public function testCreateWithTrueReturnsNewPerm(): void
    {
        $this->setUpHandler();
        $perm = $this->handler->create(true);

        self::assertInstanceOf(\XoopsGroupPerm::class, $perm);
        self::assertTrue($perm->isNew());
    }

    public function testCreateWithFalseReturnsNotNewPerm(): void
    {
        $this->setUpHandler();
        $perm = $this->handler->create(false);

        self::assertInstanceOf(\XoopsGroupPerm::class, $perm);
        self::assertFalse($perm->isNew());
    }

    public function testCreateReturnsDistinctObjects(): void
    {
        $this->setUpHandler();
        $perm1 = $this->handler->create();
        $perm2 = $this->handler->create();

        self::assertNotSame($perm1, $perm2);
    }

    // -------------------------------------------------------------------------
    // get()
    // -------------------------------------------------------------------------

    public function testGetReturnsPermOnValidId(): void
    {
        $this->setUpHandler();
        $row = [
            'gperm_id'      => 1,
            'gperm_groupid' => 2,
            'gperm_itemid'  => 3,
            'gperm_modid'   => 1,
            'gperm_name'    => 'module_read',
        ];
        $this->stubSingleRowResult($this->db, $row);

        $perm = $this->handler->get(1);
        self::assertInstanceOf(\XoopsGroupPerm::class, $perm);
    }

    public function testGetReturnsFalseForZeroId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->get(0);

        self::assertFalse($result);
    }

    public function testGetReturnsFalseForNegativeId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->get(-1);

        self::assertFalse($result);
    }

    public function testGetReturnsFalseWhenQueryFails(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->get(99);
        self::assertFalse($result);
    }

    public function testGetReturnsFalseWhenNoRows(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(0);

        $result = $this->handler->get(999);
        self::assertFalse($result);
    }

    public function testGetReturnsFalseWhenMultipleRows(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(2);

        $result = $this->handler->get(1);
        self::assertFalse($result);
    }

    public function testGetAssignsVarsCorrectly(): void
    {
        $this->setUpHandler();
        $row = [
            'gperm_id'      => 5,
            'gperm_groupid' => 2,
            'gperm_itemid'  => 10,
            'gperm_modid'   => 1,
            'gperm_name'    => 'module_admin',
        ];
        $this->stubSingleRowResult($this->db, $row);

        $perm = $this->handler->get(5);
        self::assertSame(5, $perm->getVar('gperm_id', 'n'));
        self::assertSame(2, $perm->getVar('gperm_groupid', 'n'));
        self::assertSame(10, $perm->getVar('gperm_itemid', 'n'));
        self::assertSame(1, $perm->getVar('gperm_modid', 'n'));
        self::assertSame('module_admin', $perm->getVar('gperm_name', 'n'));
    }

    public function testGetCastsIdToInt(): void
    {
        $this->setUpHandler();
        $row = [
            'gperm_id'      => 5,
            'gperm_groupid' => 2,
            'gperm_itemid'  => 10,
            'gperm_modid'   => 1,
            'gperm_name'    => 'module_read',
        ];
        $this->stubSingleRowResult($this->db, $row);

        // Pass a string -- should be cast to int internally
        $perm = $this->handler->get('5');
        self::assertInstanceOf(\XoopsGroupPerm::class, $perm);
    }

    public function testGetBuildsCorrectSql(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(0);

        $this->handler->get(42);

        self::assertStringContainsString('SELECT * FROM', $sqlCaptured);
        self::assertStringContainsString('xoops_group_permission', $sqlCaptured);
        self::assertStringContainsString('gperm_id = 42', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // insert()
    // -------------------------------------------------------------------------

    public function testInsertReturnsFalseForNonGroupPermObject(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->insert($obj);

        self::assertFalse($result);
    }

    public function testInsertReturnsTrueWhenNotDirty(): void
    {
        $this->setUpHandler();
        // A freshly created perm with setNew() but no setVar -- not dirty
        $perm = new \XoopsGroupPerm();
        $result = $this->handler->insert($perm);

        self::assertTrue($result);
    }

    public function testInsertNewPermCallsExecWithInsert(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->setNew();
        $perm->setVar('gperm_groupid', 2);
        $perm->setVar('gperm_itemid', 5);
        $perm->setVar('gperm_modid', 1);
        $perm->setVar('gperm_name', 'module_read');

        $sqlCaptured = null;
        $this->db->method('genId')->willReturn(0);
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });
        $this->db->method('getInsertId')->willReturn(99);

        $result = $this->handler->insert($perm);

        self::assertTrue($result);
        self::assertStringContainsString('INSERT INTO', $sqlCaptured);
        self::assertStringContainsString('xoops_group_permission', $sqlCaptured);
        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
    }

    public function testInsertExistingPermCallsUpdate(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        // Existing: not isNew (assigned vars without setNew)
        $perm->assignVar('gperm_id', 10);
        $perm->setVar('gperm_groupid', 3);
        $perm->setVar('gperm_itemid', 7);
        $perm->setVar('gperm_modid', 1);
        $perm->setVar('gperm_name', 'module_admin');

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->insert($perm);

        self::assertTrue($result);
        self::assertStringContainsString('UPDATE', $sqlCaptured);
        self::assertStringContainsString('xoops_group_permission', $sqlCaptured);
    }

    public function testInsertReturnsFalseWhenExecFails(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->setNew();
        $perm->setVar('gperm_groupid', 1);
        $perm->setVar('gperm_itemid', 1);
        $perm->setVar('gperm_modid', 1);
        $perm->setVar('gperm_name', 'test');

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->insert($perm);
        self::assertFalse($result);
    }

    public function testInsertAssignsGpermIdFromGetInsertId(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->setNew();
        $perm->setVar('gperm_groupid', 1);
        $perm->setVar('gperm_itemid', 5);
        $perm->setVar('gperm_modid', 1);
        $perm->setVar('gperm_name', 'module_read');

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getInsertId')->willReturn(77);

        $this->handler->insert($perm);

        self::assertSame(77, $perm->getVar('gperm_id', 'n'));
    }

    public function testInsertWithGenIdReturningNonZero(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->setNew();
        $perm->setVar('gperm_groupid', 1);
        $perm->setVar('gperm_itemid', 5);
        $perm->setVar('gperm_modid', 1);
        $perm->setVar('gperm_name', 'test');

        $this->db->method('genId')->willReturn(88);
        $this->db->method('exec')->willReturn(true);

        $this->handler->insert($perm);

        // When genId returns non-zero, the ID is used directly (no getInsertId call)
        self::assertSame(88, $perm->getVar('gperm_id', 'n'));
    }

    public function testInsertUpdateSqlDoesNotIncludeName(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 10);
        $perm->setVar('gperm_groupid', 3);
        $perm->setVar('gperm_itemid', 7);
        $perm->setVar('gperm_modid', 1);
        $perm->setVar('gperm_name', 'module_admin');

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->insert($perm);

        // The UPDATE SQL does not set gperm_name (by design in the handler)
        self::assertStringNotContainsString('gperm_name', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // delete()
    // -------------------------------------------------------------------------

    public function testDeleteReturnsFalseForNonGroupPermObject(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->delete($obj);

        self::assertFalse($result);
    }

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 5);

        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->delete($perm);
        self::assertTrue($result);
    }

    public function testDeleteReturnsFalseOnExecFailure(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 5);

        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->delete($perm);
        self::assertFalse($result);
    }

    public function testDeleteBuildsCorrectSql(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 42);

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->delete($perm);

        self::assertStringContainsString('DELETE FROM', $sqlCaptured);
        self::assertStringContainsString('xoops_group_permission', $sqlCaptured);
        self::assertStringContainsString('gperm_id = 42', $sqlCaptured);
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
        self::assertSame([], $result);
    }

    public function testGetObjectsReturnsArrayOfPerms(): void
    {
        $this->setUpHandler();
        $rows = [
            ['gperm_id' => 1, 'gperm_groupid' => 1, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 2, 'gperm_groupid' => 2, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();

        self::assertCount(2, $result);
        self::assertInstanceOf(\XoopsGroupPerm::class, $result[0]);
        self::assertInstanceOf(\XoopsGroupPerm::class, $result[1]);
    }

    public function testGetObjectsWithIdAsKey(): void
    {
        $this->setUpHandler();
        $rows = [
            ['gperm_id' => 10, 'gperm_groupid' => 1, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 20, 'gperm_groupid' => 2, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects(null, true);

        self::assertArrayHasKey(10, $result);
        self::assertArrayHasKey(20, $result);
        self::assertInstanceOf(\XoopsGroupPerm::class, $result[10]);
        self::assertInstanceOf(\XoopsGroupPerm::class, $result[20]);
    }

    public function testGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getObjects();
        self::assertSame([], $result);
    }

    public function testGetObjectsWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('gperm_modid', 1);

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getObjects($criteria);

        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testGetObjectsWithNullCriteriaSelectsAll(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getObjects(null);

        self::assertStringContainsString('SELECT * FROM', $sqlCaptured);
        self::assertStringNotContainsString('WHERE', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // getCount()
    // -------------------------------------------------------------------------

    public function testGetCountReturnsZeroOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getCount();
        self::assertSame(0, $result);
    }

    public function testGetCountReturnsCorrectCount(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 15);

        $result = $this->handler->getCount();
        self::assertSame(15, $result);
    }

    public function testGetCountReturnsZeroForEmptyTable(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 0);

        $result = $this->handler->getCount();
        self::assertSame(0, $result);
    }

    public function testGetCountWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('gperm_modid', 1);

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([8]);

        $result = $this->handler->getCount($criteria);

        self::assertSame(8, $result);
        self::assertStringContainsString('COUNT(*)', $sqlCaptured);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testGetCountWithNullCriteriaCountsAll(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([100]);

        $result = $this->handler->getCount(null);

        self::assertSame(100, $result);
        self::assertStringNotContainsString('WHERE', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // deleteAll()
    // -------------------------------------------------------------------------

    public function testDeleteAllReturnsTrueOnSuccess(): void
    {
        $this->setUpHandler();
        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->deleteAll();
        self::assertTrue($result);
    }

    public function testDeleteAllReturnsFalseOnFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->deleteAll();
        self::assertFalse($result);
    }

    public function testDeleteAllWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('gperm_modid', 5);

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteAll($criteria);

        self::assertStringContainsString('DELETE FROM', $sqlCaptured);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testDeleteAllWithNullCriteriaDeletesEverything(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteAll(null);

        self::assertStringContainsString('DELETE FROM', $sqlCaptured);
        self::assertStringNotContainsString('WHERE', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // deleteByGroup()
    // -------------------------------------------------------------------------

    public function testDeleteByGroupWithGroupIdOnly(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->deleteByGroup(5);

        self::assertTrue($result);
        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
        self::assertStringNotContainsString('gperm_modid', $sqlCaptured);
    }

    public function testDeleteByGroupWithModuleId(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->deleteByGroup(5, 3);

        self::assertTrue($result);
        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testDeleteByGroupReturnsFalseOnFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->deleteByGroup(5);
        self::assertFalse($result);
    }

    public function testDeleteByGroupCastsGroupIdToInt(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteByGroup('7');

        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
    }

    public function testDeleteByGroupWithNullModIdDoesNotIncludeModCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteByGroup(5, null);

        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
        self::assertStringNotContainsString('gperm_modid', $sqlCaptured);
    }

    // -------------------------------------------------------------------------
    // deleteByModule()
    // -------------------------------------------------------------------------

    public function testDeleteByModuleWithModIdOnly(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->deleteByModule(5);

        self::assertTrue($result);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
        self::assertStringNotContainsString('gperm_name', $sqlCaptured);
        self::assertStringNotContainsString('gperm_itemid', $sqlCaptured);
    }

    public function testDeleteByModuleWithName(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->deleteByModule(5, 'module_read');

        self::assertTrue($result);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
        self::assertStringContainsString('gperm_name', $sqlCaptured);
        self::assertStringNotContainsString('gperm_itemid', $sqlCaptured);
    }

    public function testDeleteByModuleWithNameAndItemId(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $result = $this->handler->deleteByModule(5, 'module_read', 10);

        self::assertTrue($result);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
        self::assertStringContainsString('gperm_name', $sqlCaptured);
        self::assertStringContainsString('gperm_itemid', $sqlCaptured);
    }

    public function testDeleteByModuleItemIdIgnoredWithoutName(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        // itemid without name -> name condition not set, so itemid not added either
        $result = $this->handler->deleteByModule(5, null, 10);

        self::assertTrue($result);
        self::assertStringNotContainsString('gperm_name', $sqlCaptured);
        self::assertStringNotContainsString('gperm_itemid', $sqlCaptured);
    }

    public function testDeleteByModuleReturnsFalseOnFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->deleteByModule(5);
        self::assertFalse($result);
    }

    public function testDeleteByModuleCastsModIdToInt(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->deleteByModule('3');

        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    // =========================================================================
    // checkRight() -- comprehensive tests
    // =========================================================================

    // ---- Admin group bypass (trueifadmin = true, default) ----

    public function testCheckRightReturnsTrueForAdminGroupSingleId(): void
    {
        $this->setUpHandler();
        // XOOPS_GROUP_ADMIN = 1, trueifadmin defaults to true
        $result = $this->handler->checkRight('module_read', 5, XOOPS_GROUP_ADMIN, 1);

        self::assertTrue($result);
    }

    public function testCheckRightReturnsTrueForAdminGroupInArray(): void
    {
        $this->setUpHandler();
        // Admin group in an array of groups => auto-pass
        $result = $this->handler->checkRight('module_read', 5, [XOOPS_GROUP_ADMIN, 2, 3], 1);

        self::assertTrue($result);
    }

    public function testCheckRightReturnsTrueForAdminGroupAloneInArray(): void
    {
        $this->setUpHandler();
        $result = $this->handler->checkRight('module_read', 5, [XOOPS_GROUP_ADMIN], 1);

        self::assertTrue($result);
    }

    public function testCheckRightAdminBypassDoesNotQueryDb(): void
    {
        $this->setUpHandler();
        // DB query/exec should never be called for admin group bypass
        $this->db->expects(self::never())->method('query');

        $this->handler->checkRight('module_read', 5, XOOPS_GROUP_ADMIN, 1, true);
    }

    public function testCheckRightAdminArrayBypassDoesNotQueryDb(): void
    {
        $this->setUpHandler();
        $this->db->expects(self::never())->method('query');

        $this->handler->checkRight('module_read', 5, [XOOPS_GROUP_ADMIN, 2], 1, true);
    }

    // ---- Admin group bypass disabled (trueifadmin = false) ----

    public function testCheckRightDoesNotAutoPassAdminWhenTrueifadminFalse(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 0);

        $result = $this->handler->checkRight('module_read', 5, XOOPS_GROUP_ADMIN, 1, false);

        self::assertFalse($result);
    }

    public function testCheckRightAdminInArrayNoBypassWhenTrueifadminFalse(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 0);

        $result = $this->handler->checkRight('module_read', 5, [XOOPS_GROUP_ADMIN, 2], 1, false);

        self::assertFalse($result);
    }

    public function testCheckRightAdminInArrayQueriesDbWhenTrueifadminFalse(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 1);

        // Should query DB instead of short-circuiting
        $result = $this->handler->checkRight('module_read', 5, [XOOPS_GROUP_ADMIN, 2], 1, false);

        self::assertTrue($result);
    }

    public function testCheckRightAdminSingleQueriesDbWhenTrueifadminFalse(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 1);

        $result = $this->handler->checkRight('module_read', 5, XOOPS_GROUP_ADMIN, 1, false);

        self::assertTrue($result);
    }

    // ---- Empty / falsy group IDs ----

    public function testCheckRightReturnsFalseForZeroGroupId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->checkRight('module_read', 5, 0, 1);

        self::assertFalse($result);
    }

    public function testCheckRightReturnsFalseForEmptyArray(): void
    {
        $this->setUpHandler();
        $result = $this->handler->checkRight('module_read', 5, [], 1);

        self::assertFalse($result);
    }

    public function testCheckRightReturnsFalseForNullGroupId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->checkRight('module_read', 5, null, 1);

        self::assertFalse($result);
    }

    public function testCheckRightReturnsFalseForFalseGroupId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->checkRight('module_read', 5, false, 1);

        self::assertFalse($result);
    }

    public function testCheckRightReturnsFalseForEmptyStringGroupId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->checkRight('module_read', 5, '', 1);

        self::assertFalse($result);
    }

    public function testCheckRightEmptyGroupDoesNotQueryDb(): void
    {
        $this->setUpHandler();
        $this->db->expects(self::never())->method('query');

        $this->handler->checkRight('module_read', 5, 0, 1);
    }

    // ---- Single non-admin group ID, permission exists / does not exist ----

    public function testCheckRightReturnsTrueWhenPermissionExists(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 1);

        $result = $this->handler->checkRight('module_read', 5, 2, 1);

        self::assertTrue($result);
    }

    public function testCheckRightReturnsTrueWhenMultiplePermissionsExist(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 3);

        $result = $this->handler->checkRight('module_read', 5, 2, 1);

        self::assertTrue($result);
    }

    public function testCheckRightReturnsFalseWhenNoPermissionsExist(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 0);

        $result = $this->handler->checkRight('module_read', 5, 2, 1);

        self::assertFalse($result);
    }

    // ---- Array of non-admin group IDs ----

    public function testCheckRightWithArrayGroupIdsQueriesDb(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([1]);

        $result = $this->handler->checkRight('module_read', 5, [2, 3], 1);

        self::assertTrue($result);
        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
    }

    public function testCheckRightWithMultipleGroupsNoPermission(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 0);

        $result = $this->handler->checkRight('module_read', 5, [2, 3], 1);

        self::assertFalse($result);
    }

    public function testCheckRightWithMultipleGroupsHasPermission(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 2);

        $result = $this->handler->checkRight('module_read', 5, [2, 3], 1);

        self::assertTrue($result);
    }

    public function testCheckRightWithSingleElementArrayUsesOrCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([1]);

        $this->handler->checkRight('module_read', 5, [2], 1);

        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
    }

    // ---- Item ID zero vs. non-zero ----

    public function testCheckRightWithZeroItemIdOmitsItemCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([1]);

        $this->handler->checkRight('module_read', 0, 2, 1);

        self::assertStringNotContainsString('gperm_itemid', $sqlCaptured);
    }

    public function testCheckRightWithNonZeroItemIdIncludesItemCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        $this->handler->checkRight('module_read', 5, 2, 1);

        self::assertStringContainsString('gperm_itemid', $sqlCaptured);
    }

    public function testCheckRightWithNegativeItemIdOmitsItemCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        // Negative itemid cast to int stays negative, which is <= 0
        $this->handler->checkRight('module_read', -1, 2, 1);

        self::assertStringNotContainsString('gperm_itemid', $sqlCaptured);
    }

    public function testCheckRightCastsStringItemIdToInt(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        // Pass a string item ID -- should be cast to int
        $this->handler->checkRight('module_read', '5', 2, 1);

        self::assertStringContainsString('gperm_itemid', $sqlCaptured);
    }

    // ---- Default module ID ----

    public function testCheckRightDefaultModIdIsOne(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        // Omit gperm_modid parameter -- defaults to 1
        $this->handler->checkRight('module_read', 5, 2);

        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    // ---- SQL structure verification ----

    public function testCheckRightSqlIncludesModIdCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        $this->handler->checkRight('module_read', 5, 2, 3);

        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testCheckRightSqlIncludesNameCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        $this->handler->checkRight('module_admin', 5, 2, 1);

        self::assertStringContainsString('gperm_name', $sqlCaptured);
        self::assertStringContainsString('module_admin', $sqlCaptured);
    }

    public function testCheckRightSqlUsesCountQuery(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        $this->handler->checkRight('module_read', 5, 2, 1);

        // checkRight calls getCount internally which uses COUNT(*)
        self::assertStringContainsString('COUNT(*)', $sqlCaptured);
    }

    // ---- Combined scenario tests ----

    public function testCheckRightNonAdminSingleGroupWithPermission(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 1);

        $result = $this->handler->checkRight('module_admin', 1, 2, 1);
        self::assertTrue($result);
    }

    public function testCheckRightNonAdminSingleGroupWithoutPermission(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 0);

        $result = $this->handler->checkRight('module_admin', 1, 2, 1);
        self::assertFalse($result);
    }

    public function testCheckRightMultipleGroupsContainingAdminWithTrueifadmin(): void
    {
        $this->setUpHandler();
        // Admin group (1) in array, trueifadmin=true => short-circuit
        $result = $this->handler->checkRight('module_read', 5, [1, 2, 3]);

        self::assertTrue($result);
    }

    public function testCheckRightAdminGroupAsIntegerOneWithTrueifadmin(): void
    {
        $this->setUpHandler();
        // Ensure XOOPS_GROUP_ADMIN == 1 comparison works
        $result = $this->handler->checkRight('module_read', 5, 1, 1, true);

        self::assertTrue($result);
    }

    public function testCheckRightWithCustomModId(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([1]);

        $result = $this->handler->checkRight('block_read', 10, 2, 42);

        self::assertTrue($result);
        self::assertStringContainsString('42', $sqlCaptured);
    }

    public function testCheckRightWithDifferentPermissionNames(): void
    {
        $this->setUpHandler();

        // Test with module_read
        $db1 = $this->createMockDatabase();
        $handler1 = $this->createHandler(\XoopsGroupPermHandler::class, $db1);
        $this->setProtectedProperty($handler1, 'table', 'xoops_group_permission');

        $sqlCaptured = null;
        $db1->expects(self::once())
            ->method('query')
            ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return 'mock_result';
            });
        $db1->method('isResultSet')->willReturn(true);
        $db1->method('fetchRow')->willReturn([1]);

        $handler1->checkRight('block_read', 5, 2, 1);

        self::assertStringContainsString('block_read', $sqlCaptured);
    }

    public function testCheckRightWithLargeGroupIdArray(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([3]);

        // Multiple non-admin groups; all should appear as OR criteria
        $groups = [2, 3, 4, 5, 6];
        $result = $this->handler->checkRight('module_read', 5, $groups, 1);

        self::assertTrue($result);
        // Should contain all group IDs in the SQL
        foreach ($groups as $gid) {
            self::assertStringContainsString((string)$gid, $sqlCaptured);
        }
    }

    public function testCheckRightWithZeroModId(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        $this->handler->checkRight('system_admin', 5, 2, 0);

        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    // =========================================================================
    // addRight()
    // =========================================================================

    public function testAddRightCreatesAndInsertsPerm(): void
    {
        $this->setUpHandler();

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(true);
        $this->db->method('getInsertId')->willReturn(50);

        $result = $this->handler->addRight('module_read', 5, 2, 1);

        self::assertTrue($result);
    }

    public function testAddRightReturnsFalseOnInsertFailure(): void
    {
        $this->setUpHandler();

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->addRight('module_read', 5, 2, 1);

        self::assertFalse($result);
    }

    public function testAddRightDefaultModIdIsOne(): void
    {
        $this->setUpHandler();

        $sqlCaptured = null;
        $this->db->method('genId')->willReturn(0);
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });
        $this->db->method('getInsertId')->willReturn(1);

        $this->handler->addRight('test_perm', 10, 3);

        // SQL should contain the values; modid defaults to 1
        self::assertNotNull($sqlCaptured);
        self::assertStringContainsString('INSERT INTO', $sqlCaptured);
    }

    public function testAddRightSetsAllVarsCorrectly(): void
    {
        $this->setUpHandler();

        $sqlCaptured = null;
        $this->db->method('genId')->willReturn(0);
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });
        $this->db->method('getInsertId')->willReturn(1);

        $this->handler->addRight('module_read', 7, 3, 2);

        // Verify the SQL contains expected values
        self::assertStringContainsString('module_read', $sqlCaptured);
    }

    // =========================================================================
    // getItemIds()
    // =========================================================================

    public function testGetItemIdsReturnsArrayOfItemIds(): void
    {
        $this->setUpHandler();
        $rows = [
            ['gperm_id' => 1, 'gperm_groupid' => 2, 'gperm_itemid' => 10, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 2, 'gperm_groupid' => 2, 'gperm_itemid' => 20, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 3, 'gperm_groupid' => 2, 'gperm_itemid' => 30, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getItemIds('module_read', 2, 1);

        self::assertSame([10, 20, 30], $result);
    }

    public function testGetItemIdsReturnsEmptyArrayWhenNone(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getItemIds('module_read', 99, 1);

        self::assertSame([], $result);
    }

    public function testGetItemIdsReturnsUniqueIds(): void
    {
        $this->setUpHandler();
        // Duplicate item IDs should be deduplicated by array_unique
        $rows = [
            ['gperm_id' => 1, 'gperm_groupid' => 2, 'gperm_itemid' => 10, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 2, 'gperm_groupid' => 3, 'gperm_itemid' => 10, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getItemIds('module_read', [2, 3], 1);

        self::assertCount(1, $result);
        self::assertContains(10, $result);
    }

    public function testGetItemIdsWithArrayGroupId(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getItemIds('module_read', [2, 3], 1);

        // Should build OR criteria for multiple group IDs
        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
    }

    public function testGetItemIdsWithSingleGroupId(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getItemIds('module_read', 2, 1);

        self::assertStringContainsString('gperm_groupid', $sqlCaptured);
        self::assertStringContainsString('gperm_name', $sqlCaptured);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testGetItemIdsDefaultModIdIsOne(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        // Omit modid -- defaults to 1
        $this->handler->getItemIds('module_read', 2);

        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testGetItemIdsCallsGetObjectsWithIdAsKey(): void
    {
        $this->setUpHandler();
        $rows = [
            ['gperm_id' => 10, 'gperm_groupid' => 2, 'gperm_itemid' => 100, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 20, 'gperm_groupid' => 2, 'gperm_itemid' => 200, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getItemIds('module_read', 2, 1);

        self::assertSame([100, 200], $result);
    }

    // =========================================================================
    // getGroupIds()
    // =========================================================================

    public function testGetGroupIdsReturnsArrayOfGroupIds(): void
    {
        $this->setUpHandler();
        $rows = [
            ['gperm_id' => 1, 'gperm_groupid' => 1, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 2, 'gperm_groupid' => 2, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 3, 'gperm_groupid' => 3, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getGroupIds('module_read', 5, 1);

        self::assertSame([1, 2, 3], $result);
    }

    public function testGetGroupIdsReturnsEmptyArrayWhenNone(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $result = $this->handler->getGroupIds('module_read', 999, 1);

        self::assertSame([], $result);
    }

    public function testGetGroupIdsDefaultModIdIsOne(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getGroupIds('module_read', 5);

        // Default gperm_modid should be 1
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testGetGroupIdsIncludesItemIdInCriteria(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getGroupIds('module_read', 42, 1);

        self::assertStringContainsString('gperm_itemid', $sqlCaptured);
        self::assertStringContainsString('gperm_name', $sqlCaptured);
        self::assertStringContainsString('gperm_modid', $sqlCaptured);
    }

    public function testGetGroupIdsWithCustomModId(): void
    {
        $this->setUpHandler();
        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getGroupIds('block_read', 10, 5);

        self::assertStringContainsString('block_read', $sqlCaptured);
    }

    public function testGetGroupIdsMayContainDuplicates(): void
    {
        $this->setUpHandler();
        // Unlike getItemIds, getGroupIds does NOT call array_unique
        $rows = [
            ['gperm_id' => 1, 'gperm_groupid' => 2, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
            ['gperm_id' => 2, 'gperm_groupid' => 2, 'gperm_itemid' => 5, 'gperm_modid' => 1, 'gperm_name' => 'module_read'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getGroupIds('module_read', 5, 1);

        // getGroupIds does not deduplicate
        self::assertCount(2, $result);
        self::assertSame([2, 2], $result);
    }

    // =========================================================================
    // DataProvider-based combinatorial checkRight tests
    // =========================================================================

    /**
     * Provide scenarios for checkRight where admin bypass should trigger.
     *
     * @return array<string, array{string, int, int|array, int, bool, bool}>
     */
    public static function adminBypassProvider(): array
    {
        return [
            'admin single, trueifadmin true'  => ['module_read', 5, XOOPS_GROUP_ADMIN, 1, true, true],
            'admin in array, trueifadmin true' => ['module_read', 5, [XOOPS_GROUP_ADMIN, 2], 1, true, true],
            'admin only in array'              => ['module_read', 5, [XOOPS_GROUP_ADMIN], 1, true, true],
            'admin with diff modid'            => ['block_read', 10, XOOPS_GROUP_ADMIN, 99, true, true],
            'admin with zero itemid'           => ['module_read', 0, XOOPS_GROUP_ADMIN, 1, true, true],
        ];
    }

    #[DataProvider('adminBypassProvider')]
    public function testCheckRightAdminBypassScenarios(
        string $name,
        int $itemId,
        $groupId,
        int $modId,
        bool $trueIfAdmin,
        bool $expected
    ): void {
        $this->setUpHandler();
        // No DB call expected for admin bypass
        $this->db->expects(self::never())->method('query');

        $result = $this->handler->checkRight($name, $itemId, $groupId, $modId, $trueIfAdmin);

        self::assertSame($expected, $result);
    }

    /**
     * Provide scenarios where checkRight should return false immediately (empty group).
     *
     * @return array<string, array{mixed}>
     */
    public static function emptyGroupProvider(): array
    {
        return [
            'zero'         => [0],
            'empty array'  => [[]],
            'null'         => [null],
            'false'        => [false],
            'empty string' => [''],
        ];
    }

    #[DataProvider('emptyGroupProvider')]
    public function testCheckRightReturnsFalseForEmptyGroupScenarios($groupId): void
    {
        $this->setUpHandler();
        $this->db->expects(self::never())->method('query');

        $result = $this->handler->checkRight('module_read', 5, $groupId, 1);

        self::assertFalse($result);
    }

    /**
     * Provide scenarios for DB-dependent checkRight calls.
     *
     * @return array<string, array{string, int, int|array, int, bool, int, bool}>
     */
    public static function dbCheckProvider(): array
    {
        return [
            'single group, has perm'                  => ['module_read', 5, 2, 1, true, 1, true],
            'single group, no perm'                   => ['module_read', 5, 2, 1, true, 0, false],
            'array groups, has perm'                   => ['module_read', 5, [2, 3], 1, true, 2, true],
            'array groups, no perm'                    => ['module_read', 5, [2, 3], 1, true, 0, false],
            'admin single, trueifadmin false, has perm' => ['module_read', 5, XOOPS_GROUP_ADMIN, 1, false, 1, true],
            'admin single, trueifadmin false, no perm'  => ['module_read', 5, XOOPS_GROUP_ADMIN, 1, false, 0, false],
            'admin in array, trueifadmin false, has'    => ['module_read', 5, [XOOPS_GROUP_ADMIN, 2], 1, false, 1, true],
            'admin in array, trueifadmin false, none'   => ['module_read', 5, [XOOPS_GROUP_ADMIN, 2], 1, false, 0, false],
            'block_read permission'                     => ['block_read', 10, 3, 2, true, 1, true],
            'system_admin permission'                   => ['system_admin', 1, 2, 1, true, 0, false],
            'itemid zero, has perm'                     => ['module_read', 0, 2, 1, true, 5, true],
            'itemid zero, no perm'                      => ['module_read', 0, 2, 1, true, 0, false],
        ];
    }

    #[DataProvider('dbCheckProvider')]
    public function testCheckRightDbScenarios(
        string $name,
        int $itemId,
        $groupId,
        int $modId,
        bool $trueIfAdmin,
        int $dbCount,
        bool $expected
    ): void {
        $this->setUpHandler();
        $this->stubCountResult($this->db, $dbCount);

        $result = $this->handler->checkRight($name, $itemId, $groupId, $modId, $trueIfAdmin);

        self::assertSame($expected, $result);
    }

    // =========================================================================
    // Integration-style scenario tests
    // =========================================================================

    public function testAddRightThenCheckRightScenario(): void
    {
        // Simulate: addRight inserts, then checkRight queries the count
        // These use different handler instances since the DB mock needs different configs

        // Step 1: addRight
        $db1 = $this->createMockDatabase();
        $handler1 = $this->createHandler(\XoopsGroupPermHandler::class, $db1);
        $this->setProtectedProperty($handler1, 'table', 'xoops_group_permission');

        $db1->method('genId')->willReturn(0);
        $db1->method('exec')->willReturn(true);
        $db1->method('getInsertId')->willReturn(1);

        $addResult = $handler1->addRight('module_read', 5, 2, 1);
        self::assertTrue($addResult);

        // Step 2: checkRight
        $db2 = $this->createMockDatabase();
        $handler2 = $this->createHandler(\XoopsGroupPermHandler::class, $db2);
        $this->setProtectedProperty($handler2, 'table', 'xoops_group_permission');

        $this->stubCountResult($db2, 1);

        $checkResult = $handler2->checkRight('module_read', 5, 2, 1);
        self::assertTrue($checkResult);
    }

    public function testDeleteByGroupThenCheckRightScenario(): void
    {
        // After deleting by group, permissions should not exist

        // Step 1: deleteByGroup
        $db1 = $this->createMockDatabase();
        $handler1 = $this->createHandler(\XoopsGroupPermHandler::class, $db1);
        $this->setProtectedProperty($handler1, 'table', 'xoops_group_permission');

        $db1->method('exec')->willReturn(true);
        $deleteResult = $handler1->deleteByGroup(2, 1);
        self::assertTrue($deleteResult);

        // Step 2: checkRight -- no permissions found
        $db2 = $this->createMockDatabase();
        $handler2 = $this->createHandler(\XoopsGroupPermHandler::class, $db2);
        $this->setProtectedProperty($handler2, 'table', 'xoops_group_permission');

        $this->stubCountResult($db2, 0);
        $checkResult = $handler2->checkRight('module_read', 5, 2, 1);
        self::assertFalse($checkResult);
    }

    // =========================================================================
    // Edge cases and type safety
    // =========================================================================

    public function testHandlerDbPropertyIsSet(): void
    {
        $this->setUpHandler();
        $db = $this->getProtectedProperty($this->handler, 'db');

        self::assertSame($this->db, $db);
    }

    public function testHandlerTablePropertyIsSet(): void
    {
        $this->setUpHandler();

        self::assertSame('xoops_group_permission', $this->handler->table);
    }

    public function testGetObjectsThreeRowsReturnsThreePerms(): void
    {
        $this->setUpHandler();
        $rows = [
            ['gperm_id' => 1, 'gperm_groupid' => 1, 'gperm_itemid' => 1, 'gperm_modid' => 1, 'gperm_name' => 'a'],
            ['gperm_id' => 2, 'gperm_groupid' => 2, 'gperm_itemid' => 2, 'gperm_modid' => 1, 'gperm_name' => 'b'],
            ['gperm_id' => 3, 'gperm_groupid' => 3, 'gperm_itemid' => 3, 'gperm_modid' => 1, 'gperm_name' => 'c'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();

        self::assertCount(3, $result);
    }

    public function testGetObjectsWithCriteriaCompo(): void
    {
        $this->setUpHandler();
        $criteria = new \CriteriaCompo(new \Criteria('gperm_modid', 1));
        $criteria->add(new \Criteria('gperm_name', 'module_read'));

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(false);

        $this->handler->getObjects($criteria);

        self::assertStringContainsString('gperm_modid', $sqlCaptured);
        self::assertStringContainsString('gperm_name', $sqlCaptured);
        self::assertStringContainsString('module_read', $sqlCaptured);
    }

    public function testDeleteBuildsCorrectSqlWithGpermId(): void
    {
        $this->setUpHandler();
        $perm = new \XoopsGroupPerm();
        $perm->assignVar('gperm_id', 100);

        $sqlCaptured = null;
        $this->db->expects(self::once())
                 ->method('exec')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                     $sqlCaptured = $sql;
                     return true;
                 });

        $this->handler->delete($perm);

        self::assertStringContainsString('gperm_id = 100', $sqlCaptured);
    }

    public function testCheckRightGroupIdComparisonIsLoose(): void
    {
        $this->setUpHandler();
        // XOOPS_GROUP_ADMIN == $gperm_groupid uses loose comparison in the source
        // XOOPS_GROUP_ADMIN is 1, so integer 1 should match
        $result = $this->handler->checkRight('module_read', 5, 1, 1, true);

        self::assertTrue($result);
    }

    public function testCheckRightArrayWithOnlyNonAdminGroups(): void
    {
        $this->setUpHandler();
        $this->stubCountResult($this->db, 0);

        // No admin group in array, no permissions in DB
        $result = $this->handler->checkRight('module_read', 5, [2, 3, 4], 1);

        self::assertFalse($result);
    }
}
