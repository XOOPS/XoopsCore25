<?php

declare(strict_types=1);

namespace kernel;

use XoopsImagecategory;
use XoopsImagecategoryHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/imagecategory.php';

/**
 * Unit tests for XoopsImagecategory and XoopsImagecategoryHandler.
 */
class XoopsImagecategoryTest extends KernelTestCase
{
    // =========================================================================
    // XoopsImagecategory -- constructor and initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $imgcat = new XoopsImagecategory();

        $this->assertInstanceOf(XoopsImagecategory::class, $imgcat);
        $this->assertInstanceOf(XoopsObject::class, $imgcat);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $imgcat = new XoopsImagecategory();

        $expectedVars = [
            'imgcat_id',
            'imgcat_name',
            'imgcat_display',
            'imgcat_weight',
            'imgcat_maxsize',
            'imgcat_maxwidth',
            'imgcat_maxheight',
            'imgcat_type',
            'imgcat_storetype',
        ];

        $vars = $imgcat->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testDefaultValues(): void
    {
        $imgcat = new XoopsImagecategory();

        // XOBJ_DTYPE_INT with null value returns '' via getVar() default format
        $this->assertSame('', $imgcat->getVar('imgcat_id'));
        // XOBJ_DTYPE_TXTBOX with null value returns '' via htmlSpecialChars
        $this->assertSame('', $imgcat->getVar('imgcat_name'));
        $this->assertEquals(1, $imgcat->getVar('imgcat_display'));
        $this->assertEquals(0, $imgcat->getVar('imgcat_weight'));
        $this->assertEquals(0, $imgcat->getVar('imgcat_maxsize'));
        $this->assertEquals(0, $imgcat->getVar('imgcat_maxwidth'));
        $this->assertEquals(0, $imgcat->getVar('imgcat_maxheight'));
        // XOBJ_DTYPE_OTHER with null value returns null (passthrough)
        $this->assertNull($imgcat->getVar('imgcat_type'));
        $this->assertNull($imgcat->getVar('imgcat_storetype'));
    }

    // =========================================================================
    // XoopsImagecategory -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_id', 10);

        $this->assertEquals(10, $imgcat->id());
    }

    public function testImgcatIdAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_id', 20);

        $this->assertEquals(20, $imgcat->imgcat_id());
    }

    public function testImgcatNameAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_name', 'General');

        $result = $imgcat->imgcat_name('n');
        $this->assertEquals('General', $result);
    }

    public function testImgcatDisplayAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_display', 0);

        $this->assertEquals(0, $imgcat->imgcat_display());
    }

    public function testImgcatWeightAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_weight', 5);

        $this->assertEquals(5, $imgcat->imgcat_weight());
    }

    public function testImgcatMaxsizeAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_maxsize', 1048576);

        $this->assertEquals(1048576, $imgcat->imgcat_maxsize());
    }

    public function testImgcatMaxwidthAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_maxwidth', 800);

        $this->assertEquals(800, $imgcat->imgcat_maxwidth());
    }

    public function testImgcatMaxheightAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_maxheight', 600);

        $this->assertEquals(600, $imgcat->imgcat_maxheight());
    }

    public function testImgcatTypeAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_type', 'C');

        $this->assertEquals('C', $imgcat->imgcat_type());
    }

    public function testImgcatStoretypeAccessor(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setVar('imgcat_storetype', 'db');

        $this->assertEquals('db', $imgcat->imgcat_storetype());
    }

    // =========================================================================
    // XoopsImagecategory -- setImageCount / getImageCount
    // =========================================================================

    public function testSetImageCountAndGetImageCount(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setImageCount(42);

        $this->assertSame(42, $imgcat->getImageCount());
    }

    public function testSetImageCountCastsToInt(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setImageCount('15');

        $this->assertSame(15, $imgcat->getImageCount());
    }

    public function testGetImageCountDefaultIsNull(): void
    {
        $imgcat = new XoopsImagecategory();

        // _imageCount is not initialized in constructor, so it may be null
        $this->assertNull($imgcat->getImageCount());
    }

    public function testSetImageCountZero(): void
    {
        $imgcat = new XoopsImagecategory();
        $imgcat->setImageCount(0);

        $this->assertSame(0, $imgcat->getImageCount());
    }

    // =========================================================================
    // XoopsImagecategoryHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewImagecategory(): void
    {
        $handler = $this->createHandler('XoopsImagecategoryHandler');

        $imgcat = $handler->create();

        $this->assertInstanceOf(XoopsImagecategory::class, $imgcat);
        $this->assertTrue($imgcat->isNew());
    }

    public function testHandlerCreateNotNewReturnsExistingImagecategory(): void
    {
        $handler = $this->createHandler('XoopsImagecategoryHandler');

        $imgcat = $handler->create(false);

        $this->assertInstanceOf(XoopsImagecategory::class, $imgcat);
        $this->assertFalse($imgcat->isNew());
    }

    // =========================================================================
    // XoopsImagecategoryHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsImagecategoryForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'imgcat_id'        => 1,
            'imgcat_name'      => 'General',
            'imgcat_display'   => 1,
            'imgcat_weight'    => 0,
            'imgcat_maxsize'   => 1048576,
            'imgcat_maxwidth'  => 800,
            'imgcat_maxheight' => 600,
            'imgcat_type'      => 'C',
            'imgcat_storetype' => 'db',
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $imgcat = $handler->get(1);

        $this->assertInstanceOf(XoopsImagecategory::class, $imgcat);
        $this->assertEquals(1, $imgcat->getVar('imgcat_id'));
        $this->assertEquals('General', $imgcat->getVar('imgcat_name'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsImagecategoryHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsImagecategoryHandler');

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseWhenQueryFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $result = $handler->get(1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseWhenNoRows(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('getRowsNum')->willReturn(0);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $imgcat = $handler->get(999);

        $this->assertFalse($imgcat);
    }

    // =========================================================================
    // XoopsImagecategoryHandler -- insert
    // =========================================================================

    public function testHandlerInsertNewImagecategory(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(5);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);

        $imgcat = new XoopsImagecategory();
        $imgcat->setNew();
        $imgcat->setVar('imgcat_name', 'New Category');
        $imgcat->setVar('imgcat_display', 1);
        $imgcat->setVar('imgcat_weight', 0);
        $imgcat->setVar('imgcat_maxsize', 500000);
        $imgcat->setVar('imgcat_maxwidth', 1024);
        $imgcat->setVar('imgcat_maxheight', 768);
        $imgcat->setVar('imgcat_type', 'C');
        $imgcat->setVar('imgcat_storetype', 'db');

        $result = $handler->insert($imgcat);

        $this->assertTrue($result);
        $this->assertEquals(5, $imgcat->getVar('imgcat_id'));
    }

    public function testHandlerInsertUpdateExisting(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);

        $imgcat = new XoopsImagecategory();
        $imgcat->assignVars([
            'imgcat_id'        => 3,
            'imgcat_name'      => 'Old Name',
            'imgcat_display'   => 1,
            'imgcat_weight'    => 0,
            'imgcat_maxsize'   => 500000,
            'imgcat_maxwidth'  => 1024,
            'imgcat_maxheight' => 768,
            'imgcat_type'      => 'C',
            'imgcat_storetype' => 'db',
        ]);
        $imgcat->setVar('imgcat_name', 'Updated Name');

        $result = $handler->insert($imgcat);

        $this->assertTrue($result);
    }

    public function testHandlerInsertNotDirtyReturnsTrue(): void
    {
        $handler = $this->createHandler('XoopsImagecategoryHandler');

        $imgcat = new XoopsImagecategory();
        $imgcat->assignVars([
            'imgcat_id'        => 3,
            'imgcat_name'      => 'Clean',
            'imgcat_display'   => 1,
            'imgcat_weight'    => 0,
            'imgcat_maxsize'   => 0,
            'imgcat_maxwidth'  => 0,
            'imgcat_maxheight' => 0,
            'imgcat_type'      => 'C',
            'imgcat_storetype' => 'db',
        ]);

        $result = $handler->insert($imgcat);

        $this->assertTrue($result);
    }

    public function testHandlerInsertRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImagecategoryHandler');

        $foreign = new XoopsObject();

        $result = $handler->insert($foreign);

        $this->assertFalse($result);
    }

    public function testHandlerInsertExecFailureReturnsFalse(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);

        $imgcat = new XoopsImagecategory();
        $imgcat->setNew();
        $imgcat->setVar('imgcat_name', 'Fail Category');
        $imgcat->setVar('imgcat_type', 'C');
        $imgcat->setVar('imgcat_storetype', 'db');

        $result = $handler->insert($imgcat);

        $this->assertFalse($result);
    }

    /**
     * The insert method checks `instanceof 'XoopsImageCategory'` (capital C).
     * PHP's instanceof is case-insensitive for class names, so this works
     * correctly with the class `XoopsImagecategory` (lowercase c).
     */
    public function testHandlerInsertCaseInsensitiveInstanceof(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(1);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);

        $imgcat = new XoopsImagecategory();
        $imgcat->setNew();
        $imgcat->setVar('imgcat_name', 'Case Test');
        $imgcat->setVar('imgcat_type', 'C');
        $imgcat->setVar('imgcat_storetype', 'db');

        // This verifies the case-insensitive instanceof check works
        $result = $handler->insert($imgcat);
        $this->assertTrue($result);
    }

    // =========================================================================
    // XoopsImagecategoryHandler -- delete
    // =========================================================================

    public function testHandlerDeleteSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);

        $imgcat = new XoopsImagecategory();
        $imgcat->assignVars(['imgcat_id' => 3]);

        $result = $handler->delete($imgcat);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteFailsOnExec(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);

        $imgcat = new XoopsImagecategory();
        $imgcat->assignVars(['imgcat_id' => 3]);

        $result = $handler->delete($imgcat);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImagecategoryHandler');

        $foreign = new XoopsObject();

        $result = $handler->delete($foreign);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImagecategoryHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsArray(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgcat_id'        => 1,
                'imgcat_name'      => 'General',
                'imgcat_display'   => 1,
                'imgcat_weight'    => 0,
                'imgcat_maxsize'   => 0,
                'imgcat_maxwidth'  => 0,
                'imgcat_maxheight' => 0,
                'imgcat_type'      => 'C',
                'imgcat_storetype' => 'db',
            ],
            [
                'imgcat_id'        => 2,
                'imgcat_name'      => 'Avatars',
                'imgcat_display'   => 1,
                'imgcat_weight'    => 1,
                'imgcat_maxsize'   => 50000,
                'imgcat_maxwidth'  => 100,
                'imgcat_maxheight' => 100,
                'imgcat_type'      => 'C',
                'imgcat_storetype' => 'db',
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $categories = $handler->getObjects();

        $this->assertCount(2, $categories);
        $this->assertInstanceOf(XoopsImagecategory::class, $categories[0]);
        $this->assertEquals('General', $categories[0]->getVar('imgcat_name'));
        $this->assertEquals('Avatars', $categories[1]->getVar('imgcat_name'));
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgcat_id'        => 10,
                'imgcat_name'      => 'Keyed',
                'imgcat_display'   => 1,
                'imgcat_weight'    => 0,
                'imgcat_maxsize'   => 0,
                'imgcat_maxwidth'  => 0,
                'imgcat_maxheight' => 0,
                'imgcat_type'      => 'C',
                'imgcat_storetype' => 'db',
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $categories = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $categories);
        $this->assertInstanceOf(XoopsImagecategory::class, $categories[10]);
    }

    public function testHandlerGetObjectsReturnsEmptyOnFailedQuery(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $categories = $handler->getObjects();

        $this->assertIsArray($categories);
        $this->assertEmpty($categories);
    }

    /**
     * getObjects uses is_subclass_of to check criteria, not method_exists.
     * CriteriaCompo is a subclass of CriteriaElement, so it should work.
     */
    public function testHandlerGetObjectsWithCriteriaCompo(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgcat_id'        => 5,
                'imgcat_name'      => 'Filtered',
                'imgcat_display'   => 1,
                'imgcat_weight'    => 0,
                'imgcat_maxsize'   => 0,
                'imgcat_maxwidth'  => 0,
                'imgcat_maxheight' => 0,
                'imgcat_type'      => 'C',
                'imgcat_storetype' => 'db',
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $criteria = new \CriteriaCompo(new \Criteria('imgcat_display', 1));
        $categories = $handler->getObjects($criteria);

        $this->assertCount(1, $categories);
    }

    // =========================================================================
    // XoopsImagecategoryHandler -- getCount
    // =========================================================================

    public function testHandlerGetCountReturnsInteger(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 8);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $count = $handler->getCount();

        $this->assertSame(8, $count);
    }

    public function testHandlerGetCountReturnsZeroOnFailedQuery(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $count = $handler->getCount();

        $this->assertSame(0, $count);
    }

    public function testHandlerGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 2);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $criteria = new \CriteriaCompo(new \Criteria('imgcat_display', 1));
        $count = $handler->getCount($criteria);

        $this->assertSame(2, $count);
    }

    // =========================================================================
    // XoopsImagecategoryHandler -- getList
    // =========================================================================

    public function testHandlerGetListReturnsIdNameMap(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgcat_id'        => 1,
                'imgcat_name'      => 'General',
                'imgcat_display'   => 1,
                'imgcat_weight'    => 0,
                'imgcat_maxsize'   => 0,
                'imgcat_maxwidth'  => 0,
                'imgcat_maxheight' => 0,
                'imgcat_type'      => 'C',
                'imgcat_storetype' => 'db',
            ],
            [
                'imgcat_id'        => 2,
                'imgcat_name'      => 'Smilies',
                'imgcat_display'   => 1,
                'imgcat_weight'    => 1,
                'imgcat_maxsize'   => 0,
                'imgcat_maxwidth'  => 0,
                'imgcat_maxheight' => 0,
                'imgcat_type'      => 'C',
                'imgcat_storetype' => 'db',
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $list = $handler->getList([1], 'imgcat_read');

        $this->assertIsArray($list);
        $this->assertArrayHasKey(1, $list);
        $this->assertEquals('General', $list[1]);
        $this->assertArrayHasKey(2, $list);
        $this->assertEquals('Smilies', $list[2]);
    }

    public function testHandlerGetListWithEmptyGroups(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $list = $handler->getList([], 'imgcat_read');

        $this->assertIsArray($list);
        $this->assertEmpty($list);
    }

    public function testHandlerGetListWithDisplayFilter(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $list = $handler->getList([1], 'imgcat_read', 1);

        $this->assertIsArray($list);
    }

    public function testHandlerGetListWithStoretypeFilter(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $list = $handler->getList([1], 'imgcat_write', null, 'file');

        $this->assertIsArray($list);
    }

    // =========================================================================
    // Type safety and edge cases
    // =========================================================================

    public function testGetCastsIdToInt(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'imgcat_id'        => 1,
            'imgcat_name'      => 'Test',
            'imgcat_display'   => 1,
            'imgcat_weight'    => 0,
            'imgcat_maxsize'   => 0,
            'imgcat_maxwidth'  => 0,
            'imgcat_maxheight' => 0,
            'imgcat_type'      => 'C',
            'imgcat_storetype' => 'db',
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImagecategoryHandler', $db);
        $imgcat = $handler->get('1');

        $this->assertInstanceOf(XoopsImagecategory::class, $imgcat);
    }

    public function testPublicPropertiesAreAccessible(): void
    {
        $imgcat = new XoopsImagecategory();

        // PHP 8.2 dynamic properties fix
        $imgcat->imgcat_id = 1;
        $imgcat->imgcat_name = 'test';

        $this->assertEquals(1, $imgcat->imgcat_id);
        $this->assertEquals('test', $imgcat->imgcat_name);
    }

    public function testImageCountPropertyIsPublic(): void
    {
        $imgcat = new XoopsImagecategory();

        $imgcat->_imageCount = 99;
        $this->assertEquals(99, $imgcat->_imageCount);
    }
}
