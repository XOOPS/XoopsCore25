<?php

declare(strict_types=1);

namespace kernel;

use XoopsImage;
use XoopsImageHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/image.php';

/**
 * Unit tests for XoopsImage and XoopsImageHandler.
 */
class XoopsImageTest extends KernelTestCase
{
    // =========================================================================
    // XoopsImage -- constructor and initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $image = new XoopsImage();

        $this->assertInstanceOf(XoopsImage::class, $image);
        $this->assertInstanceOf(XoopsObject::class, $image);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $image = new XoopsImage();

        $expectedVars = [
            'image_id',
            'image_name',
            'image_nicename',
            'image_mimetype',
            'image_created',
            'image_display',
            'image_weight',
            'image_body',
            'imgcat_id',
        ];

        $vars = $image->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testDefaultValues(): void
    {
        $image = new XoopsImage();

        // XOBJ_DTYPE_INT with null value returns '' via getVar() default format
        $this->assertSame('', $image->getVar('image_id'));
        // XOBJ_DTYPE_OTHER with null value returns null (passthrough)
        $this->assertNull($image->getVar('image_name'));
        // XOBJ_DTYPE_TXTBOX with null value returns '' via htmlSpecialChars
        $this->assertSame('', $image->getVar('image_nicename'));
        // XOBJ_DTYPE_OTHER with null value returns null
        $this->assertNull($image->getVar('image_mimetype'));
        // XOBJ_DTYPE_INT with null value returns ''
        $this->assertSame('', $image->getVar('image_created'));
        $this->assertEquals(1, $image->getVar('image_display'));
        $this->assertEquals(0, $image->getVar('image_weight'));
        // XOBJ_DTYPE_SOURCE with null value returns null
        $this->assertNull($image->getVar('image_body'));
        $this->assertEquals(0, $image->getVar('imgcat_id'));
    }

    // =========================================================================
    // XoopsImage -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_id', 42);

        $this->assertEquals(42, $image->id());
    }

    public function testImageIdAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_id', 99);

        $this->assertEquals(99, $image->image_id());
    }

    public function testImageNameAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_name', 'logo.png');

        $this->assertEquals('logo.png', $image->image_name());
    }

    public function testImageNicenameAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_nicename', 'Site Logo');

        $result = $image->image_nicename('n');
        $this->assertEquals('Site Logo', $result);
    }

    public function testImageMimetypeAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_mimetype', 'image/png');

        $this->assertEquals('image/png', $image->image_mimetype());
    }

    public function testImageCreatedAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_created', 1700000000);

        $this->assertEquals(1700000000, $image->image_created());
    }

    public function testImageDisplayAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_display', 0);

        $this->assertEquals(0, $image->image_display());
    }

    public function testImageWeightAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_weight', 5);

        $this->assertEquals(5, $image->image_weight());
    }

    public function testImageBodyAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('image_body', 'binary_data_here');

        $result = $image->image_body('n');
        $this->assertEquals('binary_data_here', $result);
    }

    public function testImgcatIdAccessor(): void
    {
        $image = new XoopsImage();
        $image->setVar('imgcat_id', 7);

        $this->assertEquals(7, $image->imgcat_id());
    }

    // =========================================================================
    // XoopsImageHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewImage(): void
    {
        $handler = $this->createHandler('XoopsImageHandler');

        $image = $handler->create();

        $this->assertInstanceOf(XoopsImage::class, $image);
        $this->assertTrue($image->isNew());
    }

    public function testHandlerCreateNotNewReturnsExistingImage(): void
    {
        $handler = $this->createHandler('XoopsImageHandler');

        $image = $handler->create(false);

        $this->assertInstanceOf(XoopsImage::class, $image);
        $this->assertFalse($image->isNew());
    }

    // =========================================================================
    // XoopsImageHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsImageForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'image_id'       => 1,
            'image_name'     => 'test.png',
            'image_nicename' => 'Test Image',
            'image_mimetype' => 'image/png',
            'image_created'  => 1700000000,
            'image_display'  => 1,
            'image_weight'   => 0,
            'image_body'     => 'binarydata',
            'imgcat_id'      => 3,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $image = $handler->get(1);

        $this->assertInstanceOf(XoopsImage::class, $image);
        $this->assertEquals(1, $image->getVar('image_id'));
        $this->assertEquals('test.png', $image->getVar('image_name'));
        $this->assertEquals('Test Image', $image->getVar('image_nicename'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsImageHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsImageHandler');

        $result = $handler->get(-5);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseWhenQueryFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImageHandler', $db);
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

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $image = $handler->get(999);

        $this->assertFalse($image);
    }

    // =========================================================================
    // XoopsImageHandler -- insert (new with body)
    // =========================================================================

    public function testHandlerInsertNewImageWithBody(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(10);

        $handler = $this->createHandler('XoopsImageHandler', $db);

        $image = new XoopsImage();
        $image->setNew();
        $image->setVar('image_name', 'new.png');
        $image->setVar('image_nicename', 'New Image');
        $image->setVar('image_mimetype', 'image/png');
        $image->setVar('image_display', 1);
        $image->setVar('image_weight', 0);
        $image->setVar('image_body', 'binarydata');
        $image->setVar('imgcat_id', 1);

        $result = $handler->insert($image);

        $this->assertTrue($result);
        $this->assertEquals(10, $image->getVar('image_id'));
    }

    // =========================================================================
    // XoopsImageHandler -- insert (new without body)
    // =========================================================================

    public function testHandlerInsertNewImageWithoutBody(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(11);

        $handler = $this->createHandler('XoopsImageHandler', $db);

        $image = new XoopsImage();
        $image->setNew();
        $image->setVar('image_name', 'no_body.png');
        $image->setVar('image_nicename', 'No Body');
        $image->setVar('image_mimetype', 'image/png');
        $image->setVar('image_display', 1);
        $image->setVar('image_weight', 0);
        $image->setVar('imgcat_id', 2);

        $result = $handler->insert($image);

        $this->assertTrue($result);
        $this->assertEquals(11, $image->getVar('image_id'));
    }

    // =========================================================================
    // XoopsImageHandler -- insert (update existing)
    // =========================================================================

    public function testHandlerInsertUpdateExistingImage(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImageHandler', $db);

        $image = new XoopsImage();
        // Not new -- simulate existing record
        $image->assignVars([
            'image_id'       => 5,
            'image_name'     => 'old.png',
            'image_nicename' => 'Old Image',
            'image_mimetype' => 'image/png',
            'image_display'  => 1,
            'image_weight'   => 0,
            'imgcat_id'      => 1,
        ]);
        // Mark dirty by changing a value
        $image->setVar('image_nicename', 'Updated Image');

        $result = $handler->insert($image);

        $this->assertTrue($result);
    }

    // =========================================================================
    // XoopsImageHandler -- insert (imagebody failure rolls back)
    // =========================================================================

    public function testHandlerInsertNewImageBodyFailureRollsBack(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        // First exec succeeds (image insert), second fails (imagebody insert),
        // third call is the rollback DELETE
        $db->method('exec')->willReturnOnConsecutiveCalls(true, false, true);
        $db->method('getInsertId')->willReturn(12);

        $handler = $this->createHandler('XoopsImageHandler', $db);

        $image = new XoopsImage();
        $image->setNew();
        $image->setVar('image_name', 'fail.png');
        $image->setVar('image_nicename', 'Fail Image');
        $image->setVar('image_mimetype', 'image/png');
        $image->setVar('image_display', 1);
        $image->setVar('image_weight', 0);
        $image->setVar('image_body', 'binarydata');
        $image->setVar('imgcat_id', 1);

        $result = $handler->insert($image);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageHandler -- insert (not dirty returns true)
    // =========================================================================

    public function testHandlerInsertNotDirtyReturnsTrue(): void
    {
        $handler = $this->createHandler('XoopsImageHandler');

        $image = new XoopsImage();
        // Not new, not dirty -- assignVars does not set dirty flag
        $image->assignVars([
            'image_id'       => 5,
            'image_name'     => 'clean.png',
            'image_nicename' => 'Clean',
            'image_mimetype' => 'image/png',
            'image_display'  => 1,
            'image_weight'   => 0,
            'imgcat_id'      => 1,
        ]);

        $result = $handler->insert($image);

        $this->assertTrue($result);
    }

    // =========================================================================
    // XoopsImageHandler -- insert (wrong type returns false)
    // =========================================================================

    public function testHandlerInsertRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImageHandler');

        $foreign = new XoopsObject();

        $result = $handler->insert($foreign);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageHandler -- insert (image exec failure)
    // =========================================================================

    public function testHandlerInsertNewImageExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImageHandler', $db);

        $image = new XoopsImage();
        $image->setNew();
        $image->setVar('image_name', 'fail.png');
        $image->setVar('image_nicename', 'Fail');
        $image->setVar('image_mimetype', 'image/png');
        $image->setVar('image_display', 1);
        $image->setVar('image_weight', 0);
        $image->setVar('imgcat_id', 1);

        $result = $handler->insert($image);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageHandler -- delete
    // =========================================================================

    public function testHandlerDeleteSuccess(): void
    {
        $db = $this->createMockDatabase();
        // First exec for image delete succeeds, second for imagebody delete
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImageHandler', $db);

        $image = new XoopsImage();
        $image->assignVars(['image_id' => 5]);

        $result = $handler->delete($image);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteFailsOnExec(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImageHandler', $db);

        $image = new XoopsImage();
        $image->assignVars(['image_id' => 5]);

        $result = $handler->delete($image);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImageHandler');

        $foreign = new XoopsObject();

        $result = $handler->delete($foreign);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsArrayOfImages(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'image_id'       => 1,
                'image_name'     => 'one.png',
                'image_nicename' => 'One',
                'image_mimetype' => 'image/png',
                'image_created'  => 1700000000,
                'image_display'  => 1,
                'image_weight'   => 0,
                'imgcat_id'      => 1,
            ],
            [
                'image_id'       => 2,
                'image_name'     => 'two.png',
                'image_nicename' => 'Two',
                'image_mimetype' => 'image/jpeg',
                'image_created'  => 1700000001,
                'image_display'  => 1,
                'image_weight'   => 1,
                'imgcat_id'      => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $images = $handler->getObjects();

        $this->assertCount(2, $images);
        $this->assertInstanceOf(XoopsImage::class, $images[0]);
        $this->assertEquals('one.png', $images[0]->getVar('image_name'));
        $this->assertEquals('two.png', $images[1]->getVar('image_name'));
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'image_id'       => 10,
                'image_name'     => 'keyed.png',
                'image_nicename' => 'Keyed',
                'image_mimetype' => 'image/png',
                'image_created'  => 1700000000,
                'image_display'  => 1,
                'image_weight'   => 0,
                'imgcat_id'      => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $images = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $images);
        $this->assertInstanceOf(XoopsImage::class, $images[10]);
    }

    public function testHandlerGetObjectsReturnsEmptyOnFailedQuery(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $images = $handler->getObjects();

        $this->assertIsArray($images);
        $this->assertEmpty($images);
    }

    public function testHandlerGetObjectsWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'image_id'       => 3,
                'image_name'     => 'filtered.png',
                'image_nicename' => 'Filtered',
                'image_mimetype' => 'image/png',
                'image_created'  => 1700000000,
                'image_display'  => 1,
                'image_weight'   => 0,
                'imgcat_id'      => 2,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $criteria = new \Criteria('imgcat_id', 2);
        $images = $handler->getObjects($criteria);

        $this->assertCount(1, $images);
        $this->assertEquals(3, $images[0]->getVar('image_id'));
    }

    // =========================================================================
    // XoopsImageHandler -- getCount
    // =========================================================================

    public function testHandlerGetCountReturnsInteger(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 5);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $count = $handler->getCount();

        $this->assertSame(5, $count);
    }

    public function testHandlerGetCountReturnsZeroOnFailedQuery(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $count = $handler->getCount();

        $this->assertSame(0, $count);
    }

    public function testHandlerGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 3);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $criteria = new \Criteria('imgcat_id', 2);
        $count = $handler->getCount($criteria);

        $this->assertSame(3, $count);
    }

    // =========================================================================
    // XoopsImageHandler -- getList
    // =========================================================================

    public function testHandlerGetListReturnsNameNicenameMap(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'image_id'       => 1,
                'image_name'     => 'logo.png',
                'image_nicename' => 'Site Logo',
                'image_mimetype' => 'image/png',
                'image_created'  => 1700000000,
                'image_display'  => 1,
                'image_weight'   => 0,
                'image_body'     => 'data',
                'imgcat_id'      => 5,
            ],
            [
                'image_id'       => 2,
                'image_name'     => 'banner.jpg',
                'image_nicename' => 'Top Banner',
                'image_mimetype' => 'image/jpeg',
                'image_created'  => 1700000001,
                'image_display'  => 1,
                'image_weight'   => 1,
                'image_body'     => 'data2',
                'imgcat_id'      => 5,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $list = $handler->getList(5);

        $this->assertIsArray($list);
        $this->assertArrayHasKey('logo.png', $list);
        $this->assertEquals('Site Logo', $list['logo.png']);
        $this->assertArrayHasKey('banner.jpg', $list);
        $this->assertEquals('Top Banner', $list['banner.jpg']);
    }

    public function testHandlerGetListReturnsEmptyForNoCategoryImages(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        $list = $handler->getList(999);

        $this->assertIsArray($list);
        $this->assertEmpty($list);
    }

    // =========================================================================
    // Type safety
    // =========================================================================

    public function testGetCastsIdToInt(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'image_id'       => 1,
            'image_name'     => 'test.png',
            'image_nicename' => 'Test',
            'image_mimetype' => 'image/png',
            'image_created'  => 1700000000,
            'image_display'  => 1,
            'image_weight'   => 0,
            'image_body'     => '',
            'imgcat_id'      => 1,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImageHandler', $db);
        // Pass a string id -- should be cast to int
        $image = $handler->get('1');

        $this->assertInstanceOf(XoopsImage::class, $image);
    }

    public function testPublicPropertiesAreAccessible(): void
    {
        $image = new XoopsImage();

        // PHP 8.2 dynamic properties fix - these should exist as public properties
        $image->image_id = 1;
        $image->image_nicename = 'test';

        $this->assertEquals(1, $image->image_id);
        $this->assertEquals('test', $image->image_nicename);
    }
}
