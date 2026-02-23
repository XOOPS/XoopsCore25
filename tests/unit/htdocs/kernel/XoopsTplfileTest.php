<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/tplfile.php';

// Language constants needed by cleanVars
if (!defined('_XOBJ_ERR_REQUIRED')) {
    define('_XOBJ_ERR_REQUIRED', '%s is required');
}
if (!defined('_XOBJ_ERR_SHORTERTHAN')) {
    define('_XOBJ_ERR_SHORTERTHAN', '%s must be shorter than %d characters.');
}

/**
 * Unit tests for XoopsTplfile and XoopsTplfileHandler.
 */
class XoopsTplfileTest extends KernelTestCase
{
    /** @var \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    /** @var \XoopsTplfileHandler */
    private $handler;

    private function setUpHandler(): void
    {
        $this->db = $this->createMockDatabase();
        $this->handler = $this->createHandler(\XoopsTplfileHandler::class, $this->db);
    }

    // =========================================================================
    // XoopsTplfile -- object tests
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $tpl = new \XoopsTplfile();
        $this->assertInstanceOf(\XoopsTplfile::class, $tpl);
        $this->assertInstanceOf(\XoopsObject::class, $tpl);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $tpl = new \XoopsTplfile();
        $vars = $tpl->getVars();

        $expectedVars = [
            'tpl_id', 'tpl_refid', 'tpl_tplset', 'tpl_file', 'tpl_desc',
            'tpl_lastmodified', 'tpl_lastimported', 'tpl_module', 'tpl_type',
            'tpl_source',
        ];

        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testTplFileIsRequired(): void
    {
        $tpl = new \XoopsTplfile();
        $this->assertTrue($tpl->vars['tpl_file']['required']);
        $this->assertSame(100, $tpl->vars['tpl_file']['maxlength']);
    }

    public function testTplRefidDefaultsToZero(): void
    {
        $tpl = new \XoopsTplfile();
        $this->assertEquals(0, $tpl->getVar('tpl_refid'));
    }

    public function testTplLastmodifiedDefaultsToZero(): void
    {
        $tpl = new \XoopsTplfile();
        $this->assertEquals(0, $tpl->getVar('tpl_lastmodified'));
    }

    public function testTplLastimportedDefaultsToZero(): void
    {
        $tpl = new \XoopsTplfile();
        $this->assertEquals(0, $tpl->getVar('tpl_lastimported'));
    }

    public function testTplSourceIsSourceType(): void
    {
        $tpl = new \XoopsTplfile();
        $this->assertSame(XOBJ_DTYPE_SOURCE, $tpl->vars['tpl_source']['data_type']);
    }

    public function testIdAccessorReturnsTplId(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 42);
        $this->assertSame(42, $tpl->id());
    }

    public function testTplIdAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 7);
        $this->assertSame(7, $tpl->tpl_id());
    }

    public function testTplFileAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_file', 'mymod_index.tpl');
        $this->assertSame('mymod_index.tpl', $tpl->tpl_file('n'));
    }

    public function testTplModuleAccessor(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_module', 'system');
        $this->assertSame('system', $tpl->tpl_module());
    }

    public function testGetSourceReturnsSource(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_source', '<div>Hello</div>');
        $this->assertSame('<div>Hello</div>', $tpl->getSource());
    }

    public function testGetLastModifiedReturnsTimestamp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_lastmodified', 1234567890);
        $this->assertEquals(1234567890, $tpl->getLastModified());
    }

    // =========================================================================
    // XoopsTplfileHandler -- create()
    // =========================================================================

    public function testCreateReturnsNewTplfile(): void
    {
        $this->setUpHandler();
        $tpl = $this->handler->create();
        $this->assertInstanceOf(\XoopsTplfile::class, $tpl);
        $this->assertTrue($tpl->isNew());
    }

    public function testCreateWithFalseReturnsNotNew(): void
    {
        $this->setUpHandler();
        $tpl = $this->handler->create(false);
        $this->assertInstanceOf(\XoopsTplfile::class, $tpl);
        $this->assertFalse($tpl->isNew());
    }

    // =========================================================================
    // XoopsTplfileHandler -- get()
    // =========================================================================

    public function testGetWithoutSourceReturnsTplfile(): void
    {
        $this->setUpHandler();
        $row = [
            'tpl_id' => 1, 'tpl_refid' => 0, 'tpl_tplset' => 'default',
            'tpl_file' => 'test.tpl', 'tpl_desc' => '', 'tpl_lastmodified' => 100,
            'tpl_lastimported' => 50, 'tpl_module' => 'system', 'tpl_type' => 'module',
        ];
        $this->stubSingleRowResult($this->db, $row);

        $tpl = $this->handler->get(1);
        $this->assertInstanceOf(\XoopsTplfile::class, $tpl);
    }

    public function testGetWithSourceJoinsTplsource(): void
    {
        $this->setUpHandler();
        $row = [
            'tpl_id' => 1, 'tpl_refid' => 0, 'tpl_tplset' => 'default',
            'tpl_file' => 'test.tpl', 'tpl_desc' => '', 'tpl_lastmodified' => 100,
            'tpl_lastimported' => 50, 'tpl_module' => 'system', 'tpl_type' => 'module',
            'tpl_source' => '<div>Source</div>',
        ];

        $sqlCaptured = null;
        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturnCallback(function ($sql) use (&$sqlCaptured, $row) {
                     $sqlCaptured = $sql;
                     return 'mock_result';
                 });
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('getRowsNum')->willReturn(1);
        $this->db->method('fetchArray')->willReturn($row);

        $tpl = $this->handler->get(1, true);
        $this->assertInstanceOf(\XoopsTplfile::class, $tpl);
        $this->assertStringContainsString('LEFT JOIN', $sqlCaptured);
        $this->assertStringContainsString('tpl_source', $sqlCaptured);
    }

    public function testGetReturnsFalseForZeroId(): void
    {
        $this->setUpHandler();
        $result = $this->handler->get(0);
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
    // XoopsTplfileHandler -- loadSource()
    // =========================================================================

    public function testLoadSourceReturnsFalseForNonTplfileObject(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->loadSource($obj);
        $this->assertFalse($result);
    }

    public function testLoadSourceReturnsTrueWhenSourceAlreadySet(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 1);
        $tpl->assignVar('tpl_source', '<div>Already loaded</div>');

        $result = $this->handler->loadSource($tpl);
        $this->assertTrue($result);
    }

    public function testLoadSourceFetchesFromDatabase(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);
        // tpl_source not set -- should query DB

        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')->willReturn(['tpl_source' => '<p>Loaded</p>']);

        $result = $this->handler->loadSource($tpl);
        $this->assertTrue($result);
    }

    public function testLoadSourceReturnsFalseOnQueryFailure(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);

        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->loadSource($tpl);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsTplfileHandler -- insert()
    // =========================================================================

    public function testInsertReturnsFalseForNonTplfileObject(): void
    {
        $this->setUpHandler();
        $obj = new \XoopsObject();
        $result = $this->handler->insert($obj);
        $this->assertFalse($result);
    }

    public function testInsertReturnsTrueWhenNotDirty(): void
    {
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $result = $this->handler->insert($tpl);
        $this->assertTrue($result);
    }

    public function testInsertNewTplfileWithSource(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->setNew();
        $tpl->setVar('tpl_file', 'mymod_index.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');
        $tpl->setVar('tpl_source', '<div>Source</div>');

        $this->db->method('genId')->willReturn(0);
        // exec is called: INSERT tplfile, INSERT tplsource
        $this->db->expects($this->exactly(2))
                 ->method('exec')
                 ->willReturn(true);
        $this->db->method('getInsertId')->willReturn(10);

        $result = $this->handler->insert($tpl);
        $this->assertTrue($result);
        $this->assertEquals(10, $tpl->getVar('tpl_id'));
    }

    public function testInsertNewTplfileWithoutSource(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->setNew();
        $tpl->setVar('tpl_file', 'mymod_header.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');
        // No tpl_source set

        $this->db->method('genId')->willReturn(0);
        // exec called once for INSERT tplfile only
        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(true);
        $this->db->method('getInsertId')->willReturn(11);

        $result = $this->handler->insert($tpl);
        $this->assertTrue($result);
    }

    public function testInsertNewTplfileRollsBackOnSourceFail(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->setNew();
        $tpl->setVar('tpl_file', 'fail.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');
        $tpl->setVar('tpl_source', '<div>Will Fail</div>');

        $this->db->method('genId')->willReturn(0);
        $this->db->method('getInsertId')->willReturn(20);

        // First exec (INSERT tplfile) succeeds, second (INSERT tplsource) fails,
        // third (DELETE rollback) succeeds
        $execCount = 0;
        $this->db->method('exec')
                 ->willReturnCallback(function () use (&$execCount) {
                     $execCount++;
                     if ($execCount === 1) {
                         return true;  // INSERT tplfile
                     }
                     if ($execCount === 2) {
                         return false; // INSERT tplsource fails
                     }
                     return true;      // DELETE rollback
                 });

        $result = $this->handler->insert($tpl);
        $this->assertFalse($result);
    }

    public function testInsertUpdateExistingTplfile(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);
        $tpl->setVar('tpl_file', 'updated.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');

        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->insert($tpl);
        $this->assertTrue($result);
    }

    public function testInsertUpdateWithSourceUpdatesSource(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);
        $tpl->setVar('tpl_file', 'updated.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');
        $tpl->setVar('tpl_source', '<div>Updated source</div>');

        // exec called twice: UPDATE tplfile, UPDATE tplsource
        $this->db->expects($this->exactly(2))
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->insert($tpl);
        $this->assertTrue($result);
    }

    public function testInsertReturnsFalseWhenExecFails(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->setNew();
        $tpl->setVar('tpl_file', 'fail.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');

        $this->db->method('genId')->willReturn(0);
        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->insert($tpl);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsTplfileHandler -- forceUpdate()
    // =========================================================================

    public function testForceUpdateExistingTplfile(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);
        $tpl->setVar('tpl_file', 'forced.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');

        $this->db->method('exec')->willReturn(true);

        $result = $this->handler->forceUpdate($tpl);
        $this->assertTrue($result);
    }

    public function testForceUpdateReturnsFalseForNewTplfile(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->setNew();
        $tpl->setVar('tpl_file', 'new.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');

        $result = $this->handler->forceUpdate($tpl);
        $this->assertFalse($result);
    }

    public function testForceUpdateReturnsTrueWhenNotDirty(): void
    {
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);
        // Not dirty
        $result = $this->handler->forceUpdate($tpl);
        $this->assertTrue($result);
    }

    public function testForceUpdateReturnsFalseOnExecFailure(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);
        $tpl->setVar('tpl_file', 'fail_update.tpl');
        $tpl->setVar('tpl_tplset', 'default');
        $tpl->setVar('tpl_module', 'mymod');
        $tpl->setVar('tpl_type', 'module');

        $this->db->method('exec')->willReturn(false);

        $result = $this->handler->forceUpdate($tpl);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsTplfileHandler -- delete()
    // =========================================================================

    public function testDeleteReturnsFalseForNonTplfileObject(): void
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
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);

        // exec called twice: DELETE tplfile, DELETE tplsource
        $this->db->expects($this->exactly(2))
                 ->method('exec')
                 ->willReturn(true);

        $result = $this->handler->delete($tpl);
        $this->assertTrue($result);
    }

    public function testDeleteReturnsFalseOnFirstExecFailure(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->setUpHandler();
        $tpl = new \XoopsTplfile();
        $tpl->assignVar('tpl_id', 5);

        $this->db->expects($this->once())
                 ->method('exec')
                 ->willReturn(false);

        $result = $this->handler->delete($tpl);
        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsTplfileHandler -- getObjects()
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

    public function testGetObjectsReturnsArrayOfTplfiles(): void
    {
        $this->setUpHandler();
        $rows = [
            ['tpl_id' => 1, 'tpl_refid' => 0, 'tpl_tplset' => 'default', 'tpl_file' => 'a.tpl', 'tpl_desc' => '', 'tpl_lastmodified' => 0, 'tpl_lastimported' => 0, 'tpl_module' => 'system', 'tpl_type' => 'module'],
            ['tpl_id' => 2, 'tpl_refid' => 0, 'tpl_tplset' => 'default', 'tpl_file' => 'b.tpl', 'tpl_desc' => '', 'tpl_lastmodified' => 0, 'tpl_lastimported' => 0, 'tpl_module' => 'system', 'tpl_type' => 'module'],
        ];
        $this->stubMultiRowResult($this->db, $rows);

        $result = $this->handler->getObjects();
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\XoopsTplfile::class, $result[0]);
    }

    public function testGetObjectsWithGetSourceJoins(): void
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

        $this->handler->getObjects(null, true);
        $this->assertStringContainsString('LEFT JOIN', $sqlCaptured);
        $this->assertStringContainsString('tpl_source', $sqlCaptured);
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
    // XoopsTplfileHandler -- getCount()
    // =========================================================================

    public function testGetCountReturnsInteger(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([12]);

        $result = $this->handler->getCount();
        $this->assertSame(12, $result);
    }

    public function testGetCountWithCriteria(): void
    {
        $this->setUpHandler();
        $criteria = new \Criteria('tpl_module', 'system');

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
        $this->assertStringContainsString('tpl_module', $sqlCaptured);
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
    // XoopsTplfileHandler -- getModuleTplCount()
    // =========================================================================

    public function testGetModuleTplCountReturnsModuleCounts(): void
    {
        $this->setUpHandler();
        $rows = [
            ['tpl_module' => 'system', 'count' => 5],
            ['tpl_module' => 'publisher', 'count' => 3],
        ];
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')
                 ->willReturnOnConsecutiveCalls($rows[0], $rows[1], false);

        $result = $this->handler->getModuleTplCount('default');
        $this->assertIsArray($result);
        $this->assertSame(5, $result['system']);
        $this->assertSame(3, $result['publisher']);
    }

    public function testGetModuleTplCountSkipsEmptyModule(): void
    {
        $this->setUpHandler();
        $rows = [
            ['tpl_module' => '', 'count' => 2],
            ['tpl_module' => 'system', 'count' => 5],
        ];
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchArray')
                 ->willReturnOnConsecutiveCalls($rows[0], $rows[1], false);

        $result = $this->handler->getModuleTplCount('default');
        $this->assertArrayNotHasKey('', $result);
        $this->assertArrayHasKey('system', $result);
    }

    public function testGetModuleTplCountReturnsEmptyOnQueryFailure(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn(false);
        $this->db->method('isResultSet')->willReturn(false);

        $result = $this->handler->getModuleTplCount('default');
        $this->assertSame([], $result);
    }

    // =========================================================================
    // XoopsTplfileHandler -- find()
    // =========================================================================

    public function testFindWithTplsetParam(): void
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

        $result = $this->handler->find('default');
        $this->assertIsArray($result);
        $this->assertStringContainsString('tpl_tplset', $sqlCaptured);
    }

    public function testFindWithArrayType(): void
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

        $result = $this->handler->find('default', ['module', 'block']);
        $this->assertIsArray($result);
        // The array type should produce OR conditions
        $this->assertStringContainsString('tpl_type', $sqlCaptured);
    }

    public function testFindWithAllParams(): void
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

        $result = $this->handler->find('default', 'module', '1', 'system', 'test.tpl');
        $this->assertIsArray($result);
        $this->assertStringContainsString('tpl_tplset', $sqlCaptured);
        $this->assertStringContainsString('tpl_module', $sqlCaptured);
        $this->assertStringContainsString('tpl_refid', $sqlCaptured);
        $this->assertStringContainsString('tpl_file', $sqlCaptured);
        $this->assertStringContainsString('tpl_type', $sqlCaptured);
    }

    // =========================================================================
    // XoopsTplfileHandler -- templateExists()
    // =========================================================================

    public function testTemplateExistsReturnsTrueWhenFound(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([1]);

        $result = $this->handler->templateExists('system_block_login.tpl', 'default');
        $this->assertTrue($result);
    }

    public function testTemplateExistsReturnsFalseWhenNotFound(): void
    {
        $this->setUpHandler();
        $this->db->method('query')->willReturn('mock_result');
        $this->db->method('isResultSet')->willReturn(true);
        $this->db->method('fetchRow')->willReturn([0]);

        $result = $this->handler->templateExists('nonexistent.tpl', 'default');
        $this->assertFalse($result);
    }
}
