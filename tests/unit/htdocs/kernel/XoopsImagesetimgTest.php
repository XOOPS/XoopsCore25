<?php

declare(strict_types=1);

namespace kernel;

use XoopsImagesetimg;
use XoopsImagesetimgHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/imagesetimg.php';

/**
 * Unit tests for XoopsImagesetimg and XoopsImagesetimgHandler.
 */
class XoopsImagesetimgTest extends KernelTestCase
{
    // =========================================================================
    // XoopsImagesetimg -- constructor and initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $img = new XoopsImagesetimg();

        $this->assertInstanceOf(XoopsImagesetimg::class, $img);
        $this->assertInstanceOf(XoopsObject::class, $img);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $img = new XoopsImagesetimg();

        $expectedVars = [
            'imgsetimg_id',
            'imgsetimg_file',
            'imgsetimg_body',
            'imgsetimg_imgset',
        ];

        $vars = $img->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testDefaultValues(): void
    {
        $img = new XoopsImagesetimg();

        // XOBJ_DTYPE_INT with null value returns '' via getVar() default format
        $this->assertSame('', $img->getVar('imgsetimg_id'));
        // XOBJ_DTYPE_OTHER with null value returns null (passthrough)
        $this->assertNull($img->getVar('imgsetimg_file'));
        // XOBJ_DTYPE_SOURCE with null value returns null
        $this->assertNull($img->getVar('imgsetimg_body'));
        // XOBJ_DTYPE_INT with null value returns ''
        $this->assertSame('', $img->getVar('imgsetimg_imgset'));
    }

    // =========================================================================
    // XoopsImagesetimg -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $img = new XoopsImagesetimg();
        $img->setVar('imgsetimg_id', 42);

        $this->assertEquals(42, $img->id());
    }

    public function testImgsetimgIdAccessor(): void
    {
        $img = new XoopsImagesetimg();
        $img->setVar('imgsetimg_id', 99);

        $this->assertEquals(99, $img->imgsetimg_id());
    }

    public function testImgsetimgFileAccessor(): void
    {
        $img = new XoopsImagesetimg();
        $img->setVar('imgsetimg_file', 'icon.gif');

        $this->assertEquals('icon.gif', $img->imgsetimg_file());
    }

    public function testImgsetimgBodyAccessor(): void
    {
        $img = new XoopsImagesetimg();
        $img->setVar('imgsetimg_body', 'binary_gif_data');

        $result = $img->imgsetimg_body('n');
        $this->assertEquals('binary_gif_data', $result);
    }

    public function testImgsetimgImgsetAccessor(): void
    {
        $img = new XoopsImagesetimg();
        $img->setVar('imgsetimg_imgset', 5);

        $this->assertEquals(5, $img->imgsetimg_imgset());
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewImagesetimg(): void
    {
        $handler = $this->createHandler('XoopsImagesetimgHandler');

        $img = $handler->create();

        $this->assertInstanceOf(XoopsImagesetimg::class, $img);
        $this->assertTrue($img->isNew());
    }

    public function testHandlerCreateNotNewReturnsExistingImagesetimg(): void
    {
        $handler = $this->createHandler('XoopsImagesetimgHandler');

        $img = $handler->create(false);

        $this->assertInstanceOf(XoopsImagesetimg::class, $img);
        $this->assertFalse($img->isNew());
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsImagesetimgForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'imgsetimg_id'    => 1,
            'imgsetimg_file'  => 'icon.gif',
            'imgsetimg_body'  => 'binarydata',
            'imgsetimg_imgset' => 2,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $img = $handler->get(1);

        $this->assertInstanceOf(XoopsImagesetimg::class, $img);
        $this->assertEquals(1, $img->getVar('imgsetimg_id'));
        $this->assertEquals('icon.gif', $img->getVar('imgsetimg_file'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsImagesetimgHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsImagesetimgHandler');

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseWhenQueryFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
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

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $img = $handler->get(999);

        $this->assertFalse($img);
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- insert
    // =========================================================================

    public function testHandlerInsertNewImagesetimg(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(7);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);

        $img = new XoopsImagesetimg();
        $img->setNew();
        $img->setVar('imgsetimg_file', 'new_icon.gif');
        $img->setVar('imgsetimg_body', 'gifdata');
        $img->setVar('imgsetimg_imgset', 1);

        $result = $handler->insert($img);

        $this->assertTrue($result);
        $this->assertEquals(7, $img->getVar('imgsetimg_id'));
    }

    public function testHandlerInsertUpdateExisting(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);

        $img = new XoopsImagesetimg();
        $img->assignVars([
            'imgsetimg_id'     => 5,
            'imgsetimg_file'   => 'old.gif',
            'imgsetimg_body'   => 'olddata',
            'imgsetimg_imgset' => 1,
        ]);
        $img->setVar('imgsetimg_file', 'updated.gif');

        $result = $handler->insert($img);

        $this->assertTrue($result);
    }

    public function testHandlerInsertNotDirtyReturnsTrue(): void
    {
        $handler = $this->createHandler('XoopsImagesetimgHandler');

        $img = new XoopsImagesetimg();
        $img->assignVars([
            'imgsetimg_id'     => 5,
            'imgsetimg_file'   => 'clean.gif',
            'imgsetimg_body'   => 'data',
            'imgsetimg_imgset' => 1,
        ]);

        $result = $handler->insert($img);

        $this->assertTrue($result);
    }

    public function testHandlerInsertRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImagesetimgHandler');

        $foreign = new XoopsObject();

        $result = $handler->insert($foreign);

        $this->assertFalse($result);
    }

    public function testHandlerInsertExecFailureReturnsFalse(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);

        $img = new XoopsImagesetimg();
        $img->setNew();
        $img->setVar('imgsetimg_file', 'fail.gif');
        $img->setVar('imgsetimg_body', 'data');
        $img->setVar('imgsetimg_imgset', 1);

        $result = $handler->insert($img);

        $this->assertFalse($result);
    }

    /**
     * The insert method checks `instanceof 'XoopsImageSetImg'` (mixed case).
     * PHP instanceof is case-insensitive, so it matches XoopsImagesetimg.
     */
    public function testHandlerInsertCaseInsensitiveInstanceof(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(1);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);

        $img = new XoopsImagesetimg();
        $img->setNew();
        $img->setVar('imgsetimg_file', 'case_test.gif');
        $img->setVar('imgsetimg_body', 'data');
        $img->setVar('imgsetimg_imgset', 1);

        $result = $handler->insert($img);
        $this->assertTrue($result);
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- delete
    // =========================================================================

    public function testHandlerDeleteSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);

        $img = new XoopsImagesetimg();
        $img->assignVars(['imgsetimg_id' => 5]);

        $result = $handler->delete($img);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteFailsOnExec(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);

        $img = new XoopsImagesetimg();
        $img->assignVars(['imgsetimg_id' => 5]);

        $result = $handler->delete($img);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImagesetimgHandler');

        $foreign = new XoopsObject();

        $result = $handler->delete($foreign);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsArray(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgsetimg_id'     => 1,
                'imgsetimg_file'   => 'icon1.gif',
                'imgsetimg_body'   => 'data1',
                'imgsetimg_imgset' => 1,
            ],
            [
                'imgsetimg_id'     => 2,
                'imgsetimg_file'   => 'icon2.gif',
                'imgsetimg_body'   => 'data2',
                'imgsetimg_imgset' => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $imgs = $handler->getObjects();

        $this->assertCount(2, $imgs);
        $this->assertInstanceOf(XoopsImagesetimg::class, $imgs[0]);
        $this->assertEquals('icon1.gif', $imgs[0]->getVar('imgsetimg_file'));
        $this->assertEquals('icon2.gif', $imgs[1]->getVar('imgsetimg_file'));
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgsetimg_id'     => 10,
                'imgsetimg_file'   => 'keyed.gif',
                'imgsetimg_body'   => 'data',
                'imgsetimg_imgset' => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $imgs = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $imgs);
        $this->assertInstanceOf(XoopsImagesetimg::class, $imgs[10]);
    }

    public function testHandlerGetObjectsReturnsEmptyOnFailedQuery(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $imgs = $handler->getObjects();

        $this->assertIsArray($imgs);
        $this->assertEmpty($imgs);
    }

    public function testHandlerGetObjectsWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgsetimg_id'     => 3,
                'imgsetimg_file'   => 'filtered.gif',
                'imgsetimg_body'   => 'data',
                'imgsetimg_imgset' => 2,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $criteria = new \Criteria('imgsetimg_imgset', 2);
        $imgs = $handler->getObjects($criteria);

        $this->assertCount(1, $imgs);
        $this->assertEquals(3, $imgs[0]->getVar('imgsetimg_id'));
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- getCount
    // =========================================================================

    public function testHandlerGetCountReturnsInteger(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 12);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $count = $handler->getCount();

        $this->assertSame(12, $count);
    }

    public function testHandlerGetCountReturnsZeroOnFailedQuery(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $count = $handler->getCount();

        $this->assertSame(0, $count);
    }

    public function testHandlerGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 4);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $criteria = new \CriteriaCompo(new \Criteria('imgsetimg_imgset', 2));
        $count = $handler->getCount($criteria);

        $this->assertSame(4, $count);
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- getByImageset
    // =========================================================================

    public function testGetByImagesetReturnsImages(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgsetimg_id'     => 1,
                'imgsetimg_file'   => 'a.gif',
                'imgsetimg_body'   => 'data_a',
                'imgsetimg_imgset' => 3,
            ],
            [
                'imgsetimg_id'     => 2,
                'imgsetimg_file'   => 'b.gif',
                'imgsetimg_body'   => 'data_b',
                'imgsetimg_imgset' => 3,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $imgs = $handler->getByImageset(3);

        $this->assertCount(2, $imgs);
        $this->assertInstanceOf(XoopsImagesetimg::class, $imgs[0]);
        $this->assertEquals('a.gif', $imgs[0]->getVar('imgsetimg_file'));
    }

    public function testGetByImagesetWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgsetimg_id'     => 10,
                'imgsetimg_file'   => 'keyed.gif',
                'imgsetimg_body'   => 'data',
                'imgsetimg_imgset' => 5,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $imgs = $handler->getByImageset(5, true);

        $this->assertArrayHasKey(10, $imgs);
    }

    public function testGetByImagesetReturnsEmptyForNonexistent(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $imgs = $handler->getByImageset(999);

        $this->assertIsArray($imgs);
        $this->assertEmpty($imgs);
    }

    // =========================================================================
    // XoopsImagesetimgHandler -- imageExists
    // =========================================================================

    public function testImageExistsReturnsTrue(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 1);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $result = $handler->imageExists('icon.gif', 1);

        $this->assertTrue($result);
    }

    public function testImageExistsReturnsFalse(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 0);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $result = $handler->imageExists('nonexistent.gif', 1);

        $this->assertFalse($result);
    }

    public function testImageExistsWithMultipleMatches(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 3);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $result = $handler->imageExists('common.gif', 1);

        $this->assertTrue($result);
    }

    public function testImageExistsWithEmptyFilename(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 0);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        $result = $handler->imageExists('', 1);

        $this->assertFalse($result);
    }

    // =========================================================================
    // Type safety and edge cases
    // =========================================================================

    public function testGetCastsIdToInt(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'imgsetimg_id'     => 1,
            'imgsetimg_file'   => 'test.gif',
            'imgsetimg_body'   => 'data',
            'imgsetimg_imgset' => 1,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        // Pass a string id -- should be cast to int
        $img = $handler->get('1');

        $this->assertInstanceOf(XoopsImagesetimg::class, $img);
    }

    public function testPublicPropertiesAreAccessible(): void
    {
        $img = new XoopsImagesetimg();

        // PHP 8.2 dynamic properties fix
        $img->imgsetimg_id = 1;
        $img->imgsetimg_file = 'test.gif';
        $img->imgsetimg_body = 'data';
        $img->imgsetimg_imgset = 2;

        $this->assertEquals(1, $img->imgsetimg_id);
        $this->assertEquals('test.gif', $img->imgsetimg_file);
        $this->assertEquals('data', $img->imgsetimg_body);
        $this->assertEquals(2, $img->imgsetimg_imgset);
    }

    public function testGetByImagesetCastsToInt(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImagesetimgHandler', $db);
        // Pass a string -- should be cast to int inside getByImageset
        $imgs = $handler->getByImageset('5');

        $this->assertIsArray($imgs);
    }
}
