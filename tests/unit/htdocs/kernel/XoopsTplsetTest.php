<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsTplset;
use XoopsTplsetHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/tplset.php';

// Language constants needed by cleanVars
if (!defined('_XOBJ_ERR_REQUIRED')) {
    define('_XOBJ_ERR_REQUIRED', '%s is required');
}
if (!defined('_XOBJ_ERR_SHORTERTHAN')) {
    define('_XOBJ_ERR_SHORTERTHAN', '%s must be shorter than %d characters.');
}

/**
 * Unit tests for XoopsTplset and XoopsTplsetHandler.
 */
final class XoopsTplsetTest extends KernelTestCase
{
    // =========================================================================
    // XoopsTplset -- constructor and initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $tplset = new XoopsTplset();

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
        $this->assertInstanceOf(XoopsObject::class, $tplset);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $tplset = new XoopsTplset();

        $expectedVars = [
            'tplset_id',
            'tplset_name',
            'tplset_desc',
            'tplset_credits',
            'tplset_created',
        ];

        $vars = $tplset->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testVarCount(): void
    {
        $tplset = new XoopsTplset();
        $vars = $tplset->getVars();

        $this->assertCount(5, $vars);
    }

    // =========================================================================
    // XoopsTplset -- data types
    // =========================================================================

    public function testTplsetIdDataType(): void
    {
        $tplset = new XoopsTplset();
        $this->assertSame(XOBJ_DTYPE_INT, $tplset->vars['tplset_id']['data_type']);
    }

    public function testTplsetNameDataType(): void
    {
        $tplset = new XoopsTplset();
        $this->assertSame(XOBJ_DTYPE_OTHER, $tplset->vars['tplset_name']['data_type']);
    }

    public function testTplsetDescDataType(): void
    {
        $tplset = new XoopsTplset();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $tplset->vars['tplset_desc']['data_type']);
    }

    public function testTplsetCreditsDataType(): void
    {
        $tplset = new XoopsTplset();
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $tplset->vars['tplset_credits']['data_type']);
    }

    public function testTplsetCreatedDataType(): void
    {
        $tplset = new XoopsTplset();
        $this->assertSame(XOBJ_DTYPE_INT, $tplset->vars['tplset_created']['data_type']);
    }

    // =========================================================================
    // XoopsTplset -- default values
    // =========================================================================

    public function testTplsetIdDefaultIsNull(): void
    {
        $tplset = new XoopsTplset();
        $this->assertNull($tplset->vars['tplset_id']['value']);
    }

    public function testTplsetNameDefaultIsNull(): void
    {
        $tplset = new XoopsTplset();
        $this->assertNull($tplset->vars['tplset_name']['value']);
    }

    public function testTplsetDescDefaultIsNull(): void
    {
        $tplset = new XoopsTplset();
        $this->assertNull($tplset->vars['tplset_desc']['value']);
    }

    public function testTplsetCreditsDefaultIsNull(): void
    {
        $tplset = new XoopsTplset();
        $this->assertNull($tplset->vars['tplset_credits']['value']);
    }

    public function testTplsetCreatedDefaultIsZero(): void
    {
        $tplset = new XoopsTplset();
        $this->assertSame(0, $tplset->vars['tplset_created']['value']);
    }

    // =========================================================================
    // XoopsTplset -- required and maxlength constraints
    // =========================================================================

    public function testNoVarsAreRequired(): void
    {
        $tplset = new XoopsTplset();
        foreach ($tplset->vars as $varName => $meta) {
            $this->assertFalse($meta['required'], "Var {$varName} should not be required");
        }
    }

    public function testTplsetDescMaxLength(): void
    {
        $tplset = new XoopsTplset();
        $this->assertSame(255, $tplset->vars['tplset_desc']['maxlength']);
    }

    // =========================================================================
    // XoopsTplset -- getVar default format behavior
    // =========================================================================

    public function testGetVarTplsetIdNullReturnsEmptyString(): void
    {
        // INT with null value returns '' via getVar default format 's'
        $tplset = new XoopsTplset();
        $this->assertSame('', $tplset->getVar('tplset_id'));
    }

    public function testGetVarTplsetDescNullReturnsEmptyString(): void
    {
        // TXTBOX with null value returns '' via htmlSpecialChars((string)null)
        $tplset = new XoopsTplset();
        $this->assertSame('', $tplset->getVar('tplset_desc'));
    }

    public function testGetVarTplsetNameNullReturnsNull(): void
    {
        // OTHER type with null value: getVar falls through to return null
        $tplset = new XoopsTplset();
        $this->assertNull($tplset->getVar('tplset_name'));
    }

    public function testGetVarTplsetCreatedDefaultReturnsZero(): void
    {
        $tplset = new XoopsTplset();
        $this->assertEquals(0, $tplset->getVar('tplset_created'));
    }

    // =========================================================================
    // XoopsTplset -- accessor methods
    // =========================================================================

    public function testIdAccessorReturnsNumericFormat(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar('tplset_id', 42);
        // id() defaults to 'N' format
        $this->assertSame(42, $tplset->id());
    }

    public function testIdAccessorNullReturnsEmptyString(): void
    {
        $tplset = new XoopsTplset();
        // null INT with 'N' format
        $this->assertSame('', $tplset->id());
    }

    public function testTplsetIdAccessor(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar('tplset_id', 7);
        // tplset_id() defaults to '' which maps to 's'
        $this->assertEquals(7, $tplset->tplset_id());
    }

    public function testTplsetNameAccessor(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar('tplset_name', 'default');
        $this->assertSame('default', $tplset->tplset_name());
    }

    public function testTplsetDescAccessor(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar('tplset_desc', 'A test description');
        $result = $tplset->tplset_desc('n');
        $this->assertSame('A test description', $result);
    }

    public function testTplsetCreditsAccessor(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar('tplset_credits', 'Some credits text');
        $result = $tplset->tplset_credits('n');
        $this->assertSame('Some credits text', $result);
    }

    public function testTplsetCreatedAccessor(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar('tplset_created', 1234567890);
        $this->assertEquals(1234567890, $tplset->tplset_created());
    }

    // =========================================================================
    // XoopsTplset -- setVar / getVar round trip
    // =========================================================================

    public function testSetVarAndGetVarRoundTrip(): void
    {
        $tplset = new XoopsTplset();
        $tplset->setVar('tplset_name', 'mytheme');
        $this->assertSame('mytheme', $tplset->getVar('tplset_name'));
    }

    public function testSetVarMakesObjectDirty(): void
    {
        $tplset = new XoopsTplset();
        $this->assertFalse($tplset->isDirty());
        $tplset->setVar('tplset_name', 'test');
        $this->assertTrue($tplset->isDirty());
    }

    public function testAssignVarsDoesNotSetDirty(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'      => 1,
            'tplset_name'    => 'default',
            'tplset_desc'    => 'Description',
            'tplset_credits' => 'Credits',
            'tplset_created' => 1000000,
        ]);
        $this->assertFalse($tplset->isDirty());
    }

    public function testAssignVarsSetsValues(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'      => 5,
            'tplset_name'    => 'custom',
            'tplset_desc'    => 'My desc',
            'tplset_credits' => 'Credits here',
            'tplset_created' => 999999,
        ]);

        $this->assertEquals(5, $tplset->getVar('tplset_id'));
        $this->assertSame('custom', $tplset->getVar('tplset_name'));
        $this->assertEquals(999999, $tplset->getVar('tplset_created'));
    }

    // =========================================================================
    // XoopsTplset -- isNew / setNew
    // =========================================================================

    public function testNewObjectIsNotNewByDefault(): void
    {
        $tplset = new XoopsTplset();
        $this->assertFalse($tplset->isNew());
    }

    public function testSetNewMarksObjectAsNew(): void
    {
        $tplset = new XoopsTplset();
        $tplset->setNew();
        $this->assertTrue($tplset->isNew());
    }

    // =========================================================================
    // XoopsTplset -- getVar with various formats (data provider)
    // =========================================================================

    /**
     * @return array<string, array{string, mixed, string, mixed}>
     */
    public static function getVarFormatProvider(): array
    {
        return [
            'tplset_id INT format s'  => ['tplset_id', 10, 's', 10],
            'tplset_id INT format n'  => ['tplset_id', 10, 'n', 10],
            'tplset_id INT format e'  => ['tplset_id', 10, 'e', 10],
            'tplset_name OTHER format s'  => ['tplset_name', 'default', 's', 'default'],
            'tplset_name OTHER format n'  => ['tplset_name', 'default', 'n', 'default'],
            'tplset_desc TXTBOX format n' => ['tplset_desc', 'Hello', 'n', 'Hello'],
            'tplset_created INT format s' => ['tplset_created', 12345, 's', 12345],
        ];
    }

    #[DataProvider('getVarFormatProvider')]
    public function testGetVarWithFormats(string $varName, $value, string $format, $expected): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar($varName, $value);
        $this->assertEquals($expected, $tplset->getVar($varName, $format));
    }

    // =========================================================================
    // XoopsTplset -- XSS escaping for TXTBOX desc
    // =========================================================================

    public function testGetVarDescEscapesHtml(): void
    {
        $tplset = new XoopsTplset();
        $tplset->assignVar('tplset_desc', '<script>alert("xss")</script>');
        // Default format 's' sanitizes TXTBOX via htmlSpecialChars
        $result = $tplset->getVar('tplset_desc');
        $this->assertStringNotContainsString('<script>', $result);
    }

    // =========================================================================
    // XoopsTplsetHandler -- create()
    // =========================================================================

    public function testCreateReturnsNewTplset(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $tplset = $handler->create();

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
        $this->assertTrue($tplset->isNew());
    }

    public function testCreateWithFalseReturnsNotNew(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $tplset = $handler->create(false);

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
        $this->assertFalse($tplset->isNew());
    }

    public function testCreateWithTrueReturnsNew(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $tplset = $handler->create(true);

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
        $this->assertTrue($tplset->isNew());
    }

    public function testCreateReturnsDistinctInstances(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $a = $handler->create();
        $b = $handler->create();

        $this->assertNotSame($a, $b);
    }

    // =========================================================================
    // XoopsTplsetHandler -- get()
    // =========================================================================

    public function testGetReturnsTplsetForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'tplset_id'      => 1,
            'tplset_name'    => 'default',
            'tplset_desc'    => 'Default template set',
            'tplset_credits' => 'XOOPS',
            'tplset_created' => 1000000,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $tplset = $handler->get(1);

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
        $this->assertEquals(1, $tplset->getVar('tplset_id'));
        $this->assertSame('default', $tplset->getVar('tplset_name'));
    }

    public function testGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testGetReturnsFalseWhenQueryFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->get(1);

        $this->assertFalse($result);
    }

    public function testGetReturnsFalseWhenNoRows(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(0);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->get(999);

        $this->assertFalse($result);
    }

    public function testGetReturnsFalseWhenMultipleRows(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(2);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->get(1);

        $this->assertFalse($result);
    }

    public function testGetCastsStringIdToInt(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'tplset_id'      => 1,
            'tplset_name'    => 'default',
            'tplset_desc'    => '',
            'tplset_credits' => '',
            'tplset_created' => 0,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        // Pass string id -- should be cast to int internally
        $tplset = $handler->get('1');

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
    }

    public function testGetQueryIncludesCorrectTable(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return 'mock_result';
           });
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(0);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $handler->get(5);

        $this->assertStringContainsString('xoops_tplset', $sqlCaptured);
        $this->assertStringContainsString('tplset_id=5', $sqlCaptured);
    }

    // =========================================================================
    // XoopsTplsetHandler -- getByName()
    // =========================================================================

    public function testGetByNameReturnsTplsetForValidName(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'tplset_id'      => 1,
            'tplset_name'    => 'default',
            'tplset_desc'    => 'Default set',
            'tplset_credits' => 'XOOPS',
            'tplset_created' => 1000000,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $tplset = $handler->getByName('default');

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
        $this->assertSame('default', $tplset->getVar('tplset_name'));
    }

    public function testGetByNameReturnsFalseForEmptyName(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $result = $handler->getByName('');

        $this->assertFalse($result);
    }

    public function testGetByNameReturnsFalseForWhitespaceOnlyName(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $result = $handler->getByName('   ');

        $this->assertFalse($result);
    }

    public function testGetByNameTrimsInput(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'tplset_id'      => 1,
            'tplset_name'    => 'default',
            'tplset_desc'    => '',
            'tplset_credits' => '',
            'tplset_created' => 0,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $tplset = $handler->getByName('  default  ');

        $this->assertInstanceOf(XoopsTplset::class, $tplset);
    }

    public function testGetByNameReturnsFalseWhenQueryFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getByName('default');

        $this->assertFalse($result);
    }

    public function testGetByNameReturnsFalseWhenNoRows(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(0);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getByName('nonexistent');

        $this->assertFalse($result);
    }

    public function testGetByNameReturnsFalseWhenMultipleRows(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(2);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getByName('default');

        $this->assertFalse($result);
    }

    public function testGetByNameQueryUsesQuotedName(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return 'mock_result';
           });
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(0);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $handler->getByName('default');

        $this->assertStringContainsString('xoops_tplset', $sqlCaptured);
        $this->assertStringContainsString("tplset_name='default'", $sqlCaptured);
    }

    // =========================================================================
    // XoopsTplsetHandler -- insert() new object
    // =========================================================================

    public function testInsertNewTplsetSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(10);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->setNew();
        $tplset->setVar('tplset_name', 'newset');
        $tplset->setVar('tplset_desc', 'A new template set');
        $tplset->setVar('tplset_credits', 'Author');
        $tplset->setVar('tplset_created', 1234567890);

        $result = $handler->insert($tplset);

        $this->assertTrue($result);
        $this->assertEquals(10, $tplset->getVar('tplset_id'));
    }

    public function testInsertNewTplsetUsesGenIdWhenAvailable(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(42);
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->setNew();
        $tplset->setVar('tplset_name', 'genid_test');

        $result = $handler->insert($tplset);

        $this->assertTrue($result);
        $this->assertEquals(42, $tplset->getVar('tplset_id'));
    }

    public function testInsertNewTplsetGeneratesInsertSql(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('getInsertId')->willReturn(1);

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('exec')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return true;
           });

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->setNew();
        $tplset->setVar('tplset_name', 'testset');

        $handler->insert($tplset);

        $this->assertStringContainsString('INSERT INTO', $sqlCaptured);
        $this->assertStringContainsString('xoops_tplset', $sqlCaptured);
        $this->assertStringContainsString('tplset_name', $sqlCaptured);
    }

    // =========================================================================
    // XoopsTplsetHandler -- insert() update existing
    // =========================================================================

    public function testInsertUpdateExistingTplset(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'      => 3,
            'tplset_name'    => 'existing',
            'tplset_desc'    => 'Old desc',
            'tplset_credits' => 'Old credits',
            'tplset_created' => 100000,
        ]);
        $tplset->setVar('tplset_desc', 'Updated desc');

        $result = $handler->insert($tplset);

        $this->assertTrue($result);
    }

    public function testInsertUpdateGeneratesUpdateSql(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('exec')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return true;
           });

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'      => 5,
            'tplset_name'    => 'existing',
            'tplset_desc'    => 'Old desc',
            'tplset_credits' => 'Old credits',
            'tplset_created' => 100000,
        ]);
        $tplset->setVar('tplset_name', 'updated');

        $handler->insert($tplset);

        $this->assertStringContainsString('UPDATE', $sqlCaptured);
        $this->assertStringContainsString('xoops_tplset', $sqlCaptured);
        $this->assertStringContainsString('WHERE tplset_id', $sqlCaptured);
    }

    // =========================================================================
    // XoopsTplsetHandler -- insert() failure and edge cases
    // =========================================================================

    public function testInsertRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $foreign = new XoopsObject();

        $result = $handler->insert($foreign);

        $this->assertFalse($result);
    }

    public function testInsertReturnsTrueWhenNotDirty(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $tplset = new XoopsTplset();
        // Not dirty -- should return true immediately
        $result = $handler->insert($tplset);

        $this->assertTrue($result);
    }

    public function testInsertReturnsFalseWhenExecFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->setNew();
        $tplset->setVar('tplset_name', 'failset');

        $result = $handler->insert($tplset);

        $this->assertFalse($result);
    }

    public function testInsertUpdateReturnsFalseWhenExecFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'      => 5,
            'tplset_name'    => 'existing',
            'tplset_desc'    => '',
            'tplset_credits' => '',
            'tplset_created' => 0,
        ]);
        $tplset->setVar('tplset_name', 'fail_update');

        $result = $handler->insert($tplset);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsTplsetHandler -- delete()
    // =========================================================================

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        $db = $this->createMockDatabase();
        // delete() uses query(), not exec()
        // First query: DELETE from tplset, second: DELETE from imgset_tplset_link
        $db->method('query')->willReturn('mock_result');

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'   => 5,
            'tplset_name' => 'removeme',
        ]);

        $result = $handler->delete($tplset);

        $this->assertTrue($result);
    }

    public function testDeleteRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');

        $foreign = new XoopsObject();

        $result = $handler->delete($foreign);

        $this->assertFalse($result);
    }

    public function testDeleteReturnsFalseWhenFirstQueryFails(): void
    {
        $db = $this->createMockDatabase();
        // First DELETE query fails
        $db->method('query')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'   => 5,
            'tplset_name' => 'faildelete',
        ]);

        $result = $handler->delete($tplset);

        $this->assertFalse($result);
    }

    public function testDeleteCleansUpImgsetTplsetLink(): void
    {
        $db = $this->createMockDatabase();

        $sqlCalls = [];
        $db->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCalls) {
               $sqlCalls[] = $sql;
               return 'mock_result';
           });

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'   => 5,
            'tplset_name' => 'cleanuptest',
        ]);

        $handler->delete($tplset);

        $this->assertCount(2, $sqlCalls);
        $this->assertStringContainsString('xoops_tplset', $sqlCalls[0]);
        $this->assertStringContainsString('tplset_id', $sqlCalls[0]);
        $this->assertStringContainsString('xoops_imgset_tplset_link', $sqlCalls[1]);
        $this->assertStringContainsString('tplset_name', $sqlCalls[1]);
    }

    public function testDeleteStillReturnsTrueWhenSecondQueryFails(): void
    {
        $db = $this->createMockDatabase();

        $callCount = 0;
        $db->method('query')
           ->willReturnCallback(function () use (&$callCount) {
               $callCount++;
               if ($callCount === 1) {
                   return 'mock_result'; // First DELETE succeeds
               }
               return false; // Second DELETE fails (imgset_tplset_link)
           });

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $tplset = new XoopsTplset();
        $tplset->assignVars([
            'tplset_id'   => 5,
            'tplset_name' => 'partial_delete',
        ]);

        // delete() returns true after successful first DELETE,
        // the second DELETE failure doesn't affect the return value
        $result = $handler->delete($tplset);

        $this->assertTrue($result);
    }

    // =========================================================================
    // XoopsTplsetHandler -- getObjects()
    // =========================================================================

    public function testGetObjectsReturnsArrayOfTplsets(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'tplset_id'      => 1,
                'tplset_name'    => 'default',
                'tplset_desc'    => 'Default set',
                'tplset_credits' => 'XOOPS',
                'tplset_created' => 1000000,
            ],
            [
                'tplset_id'      => 2,
                'tplset_name'    => 'custom',
                'tplset_desc'    => 'Custom set',
                'tplset_credits' => 'Author',
                'tplset_created' => 2000000,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getObjects();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsTplset::class, $result[0]);
        $this->assertInstanceOf(XoopsTplset::class, $result[1]);
    }

    public function testGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'tplset_id'      => 10,
                'tplset_name'    => 'keyed',
                'tplset_desc'    => '',
                'tplset_credits' => '',
                'tplset_created' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $result);
        $this->assertInstanceOf(XoopsTplset::class, $result[10]);
    }

    public function testGetObjectsWithoutIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'tplset_id'      => 10,
                'tplset_name'    => 'indexed',
                'tplset_desc'    => '',
                'tplset_credits' => '',
                'tplset_created' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getObjects(null, false);

        $this->assertArrayHasKey(0, $result);
        $this->assertInstanceOf(XoopsTplset::class, $result[0]);
    }

    public function testGetObjectsReturnsEmptyArrayWhenNoResults(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getObjects();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function testGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getObjects();

        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function testGetObjectsWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'tplset_id'      => 5,
                'tplset_name'    => 'filtered',
                'tplset_desc'    => '',
                'tplset_credits' => '',
                'tplset_created' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $criteria = new \Criteria('tplset_name', 'filtered');
        $result = $handler->getObjects($criteria);

        $this->assertCount(1, $result);
        $this->assertEquals(5, $result[0]->getVar('tplset_id'));
    }

    public function testGetObjectsWithCriteriaAppliesToSql(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return 'mock_result';
           });
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $criteria = new \Criteria('tplset_name', 'default');
        $handler->getObjects($criteria);

        $this->assertStringContainsString('tplset_name', $sqlCaptured);
        $this->assertStringContainsString('ORDER BY tplset_id', $sqlCaptured);
    }

    public function testGetObjectsWithNullCriteriaOmitsWhere(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return 'mock_result';
           });
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $handler->getObjects(null);

        $this->assertStringContainsString('SELECT * FROM xoops_tplset', $sqlCaptured);
        $this->assertStringNotContainsString('WHERE', $sqlCaptured);
    }

    // =========================================================================
    // XoopsTplsetHandler -- getCount()
    // =========================================================================

    public function testGetCountReturnsInteger(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 12);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getCount();

        $this->assertSame(12, $result);
    }

    public function testGetCountReturnsZeroOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getCount();

        $this->assertSame(0, $result);
    }

    public function testGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 3);

        $sqlCaptured = null;
        // Override the query stub to capture SQL
        // Note: stubCountResult already set up query, but we need to capture
        // We re-create the mock to capture.
        $db2 = $this->createMockDatabase();
        $db2->expects($this->once())
            ->method('query')
            ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
                $sqlCaptured = $sql;
                return 'mock_result';
            });
        $db2->method('isResultSet')->willReturn(true);
        $db2->method('fetchRow')->willReturn([3]);

        $handler = $this->createHandler('XoopsTplsetHandler', $db2);
        $criteria = new \Criteria('tplset_name', 'default');

        $result = $handler->getCount($criteria);

        $this->assertSame(3, $result);
        $this->assertStringContainsString('COUNT(*)', $sqlCaptured);
        $this->assertStringContainsString('tplset_name', $sqlCaptured);
    }

    public function testGetCountWithNullCriteriaOmitsWhere(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return 'mock_result';
           });
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchRow')->willReturn([5]);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $handler->getCount(null);

        $this->assertStringContainsString('SELECT COUNT(*) FROM xoops_tplset', $sqlCaptured);
        $this->assertStringNotContainsString('WHERE', $sqlCaptured);
    }

    public function testGetCountReturnsZeroForEmptyTable(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 0);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getCount();

        $this->assertSame(0, $result);
    }

    // =========================================================================
    // XoopsTplsetHandler -- getList()
    // =========================================================================

    public function testGetListReturnsNameNameMap(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'tplset_id'      => 1,
                'tplset_name'    => 'default',
                'tplset_desc'    => '',
                'tplset_credits' => '',
                'tplset_created' => 0,
            ],
            [
                'tplset_id'      => 2,
                'tplset_name'    => 'custom',
                'tplset_desc'    => '',
                'tplset_credits' => '',
                'tplset_created' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $list = $handler->getList();

        $this->assertIsArray($list);
        // getList returns tplset_name => tplset_name pairs
        $this->assertArrayHasKey('default', $list);
        $this->assertSame('default', $list['default']);
        $this->assertArrayHasKey('custom', $list);
        $this->assertSame('custom', $list['custom']);
    }

    public function testGetListReturnsEmptyArrayWhenNoResults(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $list = $handler->getList();

        $this->assertIsArray($list);
        $this->assertEmpty($list);
    }

    public function testGetListPassesCriteriaToGetObjects(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return 'mock_result';
           });
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $criteria = new \Criteria('tplset_name', 'default');
        $handler->getList($criteria);

        $this->assertStringContainsString('tplset_name', $sqlCaptured);
    }

    public function testGetListReturnsCorrectCount(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['tplset_id' => 1, 'tplset_name' => 'set1', 'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
            ['tplset_id' => 2, 'tplset_name' => 'set2', 'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
            ['tplset_id' => 3, 'tplset_name' => 'set3', 'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $list = $handler->getList();

        $this->assertCount(3, $list);
    }

    // =========================================================================
    // XoopsTplsetHandler -- getObjects() with CriteriaCompo
    // =========================================================================

    public function testGetObjectsWithCriteriaCompo(): void
    {
        $db = $this->createMockDatabase();

        $sqlCaptured = null;
        $db->expects($this->once())
           ->method('query')
           ->willReturnCallback(function ($sql) use (&$sqlCaptured) {
               $sqlCaptured = $sql;
               return 'mock_result';
           });
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $criteria = new \CriteriaCompo(new \Criteria('tplset_name', 'default'));
        $criteria->add(new \Criteria('tplset_created', 0, '>'));
        $handler->getObjects($criteria);

        $this->assertStringContainsString('tplset_name', $sqlCaptured);
        $this->assertStringContainsString('tplset_created', $sqlCaptured);
    }

    // =========================================================================
    // Edge cases and type safety
    // =========================================================================

    public function testGetVarNonexistentKeyReturnsNull(): void
    {
        $tplset = new XoopsTplset();
        $this->assertNull($tplset->getVar('nonexistent_key'));
    }

    public function testSetVarWithEmptyKeyDoesNothing(): void
    {
        $tplset = new XoopsTplset();
        $tplset->setVar('', 'value');
        $this->assertFalse($tplset->isDirty());
    }

    public function testInsertWithCleanVarsFailure(): void
    {
        // Create a tplset with a desc longer than maxlength (255 chars)
        // cleanVars should fail for the TXTBOX field
        $handler = $this->createHandler('XoopsTplsetHandler');

        $tplset = new XoopsTplset();
        $tplset->setNew();
        // Set a required field to test cleanVars path
        // tplset_desc is TXTBOX with maxlength 255
        $tplset->setVar('tplset_desc', str_repeat('A', 300));
        $tplset->setVar('tplset_name', 'test');

        $result = $handler->insert($tplset);

        // cleanVars should fail because tplset_desc exceeds 255 chars
        $this->assertFalse($result);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function invalidIdProvider(): array
    {
        return [
            'zero'     => [0],
            'negative' => [-1],
            'large negative' => [-999],
        ];
    }

    #[DataProvider('invalidIdProvider')]
    public function testGetReturnsFalseForInvalidIds(int $id): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');
        $result = $handler->get($id);
        $this->assertFalse($result);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function emptyNameProvider(): array
    {
        return [
            'empty string'    => [''],
            'single space'    => [' '],
            'multiple spaces' => ['   '],
            'tab'             => ["\t"],
            'newline'         => ["\n"],
            'mixed whitespace' => [" \t\n "],
        ];
    }

    #[DataProvider('emptyNameProvider')]
    public function testGetByNameReturnsFalseForEmptyNames(string $name): void
    {
        $handler = $this->createHandler('XoopsTplsetHandler');
        $result = $handler->getByName($name);
        $this->assertFalse($result);
    }

    public function testGetObjectsMultipleRowsPreservesOrder(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['tplset_id' => 3, 'tplset_name' => 'third',  'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
            ['tplset_id' => 1, 'tplset_name' => 'first',  'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
            ['tplset_id' => 2, 'tplset_name' => 'second', 'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $result = $handler->getObjects();

        $this->assertCount(3, $result);
        // Order should match the order returned by the DB
        $this->assertEquals(3, $result[0]->getVar('tplset_id'));
        $this->assertEquals(1, $result[1]->getVar('tplset_id'));
        $this->assertEquals(2, $result[2]->getVar('tplset_id'));
    }

    public function testGetListDeduplicatesNames(): void
    {
        // getList uses tplset_name as both key and value
        // If two tplsets have the same name, only one entry remains
        $db = $this->createMockDatabase();
        $rows = [
            ['tplset_id' => 1, 'tplset_name' => 'samename', 'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
            ['tplset_id' => 2, 'tplset_name' => 'samename', 'tplset_desc' => '', 'tplset_credits' => '', 'tplset_created' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsTplsetHandler', $db);
        $list = $handler->getList();

        // Only one entry because key is the name
        $this->assertCount(1, $list);
        $this->assertArrayHasKey('samename', $list);
    }

    public function testHandlerDbPropertyIsSet(): void
    {
        $db = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsTplsetHandler', $db);

        $dbValue = $this->getProtectedProperty($handler, 'db');
        $this->assertSame($db, $dbValue);
    }
}
