<?php

declare(strict_types=1);

namespace kernel;

use XoopsImageSet;
use XoopsImageSetHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/imageset.php';

/**
 * Unit tests for XoopsImageSet and XoopsImageSetHandler.
 */
class XoopsImageSetTest extends KernelTestCase
{
    // =========================================================================
    // XoopsImageSet -- constructor and initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $imgset = new XoopsImageSet();

        $this->assertInstanceOf(XoopsImageSet::class, $imgset);
        $this->assertInstanceOf(XoopsObject::class, $imgset);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $imgset = new XoopsImageSet();

        $expectedVars = [
            'imgset_id',
            'imgset_name',
            'imgset_refid',
        ];

        $vars = $imgset->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testDefaultValues(): void
    {
        $imgset = new XoopsImageSet();

        // XOBJ_DTYPE_INT with null value returns '' via getVar() default format
        $this->assertSame('', $imgset->getVar('imgset_id'));
        // XOBJ_DTYPE_TXTBOX with null value returns '' via htmlSpecialChars
        $this->assertSame('', $imgset->getVar('imgset_name'));
        $this->assertEquals(0, $imgset->getVar('imgset_refid'));
    }

    // =========================================================================
    // XoopsImageSet -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $imgset = new XoopsImageSet();
        $imgset->setVar('imgset_id', 10);

        $this->assertEquals(10, $imgset->id());
    }

    public function testImgsetIdAccessor(): void
    {
        $imgset = new XoopsImageSet();
        $imgset->setVar('imgset_id', 20);

        $this->assertEquals(20, $imgset->imgset_id());
    }

    public function testImgsetNameAccessor(): void
    {
        $imgset = new XoopsImageSet();
        $imgset->setVar('imgset_name', 'Default');

        $result = $imgset->imgset_name('n');
        $this->assertEquals('Default', $result);
    }

    public function testImgsetRefidAccessor(): void
    {
        $imgset = new XoopsImageSet();
        $imgset->setVar('imgset_refid', 5);

        $this->assertEquals(5, $imgset->imgset_refid());
    }

    // =========================================================================
    // XoopsImageSetHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewImageSet(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $imgset = $handler->create();

        $this->assertInstanceOf(XoopsImageSet::class, $imgset);
        $this->assertTrue($imgset->isNew());
    }

    public function testHandlerCreateNotNewReturnsExistingImageSet(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $imgset = $handler->create(false);

        $this->assertInstanceOf(XoopsImageSet::class, $imgset);
        $this->assertFalse($imgset->isNew());
    }

    // =========================================================================
    // XoopsImageSetHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsImageSetForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'imgset_id'    => 1,
            'imgset_name'  => 'Default',
            'imgset_refid' => 0,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $imgset = $handler->get(1);

        $this->assertInstanceOf(XoopsImageSet::class, $imgset);
        $this->assertEquals(1, $imgset->getVar('imgset_id'));
        $this->assertEquals('Default', $imgset->getVar('imgset_name'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseWhenQueryFails(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
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

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $imgset = $handler->get(999);

        $this->assertFalse($imgset);
    }

    // =========================================================================
    // XoopsImageSetHandler -- insert
    // =========================================================================

    public function testHandlerInsertNewImageSet(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(5);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);

        $imgset = new XoopsImageSet();
        $imgset->setNew();
        $imgset->setVar('imgset_name', 'New Set');
        $imgset->setVar('imgset_refid', 0);

        $result = $handler->insert($imgset);

        $this->assertTrue($result);
        $this->assertEquals(5, $imgset->getVar('imgset_id'));
    }

    public function testHandlerInsertUpdateExisting(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);

        $imgset = new XoopsImageSet();
        $imgset->assignVars([
            'imgset_id'    => 3,
            'imgset_name'  => 'Old Set',
            'imgset_refid' => 0,
        ]);
        $imgset->setVar('imgset_name', 'Updated Set');

        $result = $handler->insert($imgset);

        $this->assertTrue($result);
    }

    public function testHandlerInsertNotDirtyReturnsTrue(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $imgset = new XoopsImageSet();
        $imgset->assignVars([
            'imgset_id'    => 3,
            'imgset_name'  => 'Clean',
            'imgset_refid' => 0,
        ]);

        $result = $handler->insert($imgset);

        $this->assertTrue($result);
    }

    public function testHandlerInsertRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $foreign = new XoopsObject();

        $result = $handler->insert($foreign);

        $this->assertFalse($result);
    }

    public function testHandlerInsertExecFailureReturnsFalse(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);

        $imgset = new XoopsImageSet();
        $imgset->setNew();
        $imgset->setVar('imgset_name', 'Fail');
        $imgset->setVar('imgset_refid', 0);

        $result = $handler->insert($imgset);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageSetHandler -- delete (also cleans up link table)
    // =========================================================================

    public function testHandlerDeleteSuccess(): void
    {
        $db = $this->createMockDatabase();
        // Both exec calls succeed (imgset + imgset_tplset_link)
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);

        $imgset = new XoopsImageSet();
        $imgset->assignVars(['imgset_id' => 3]);

        $result = $handler->delete($imgset);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteFailsOnExec(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);

        $imgset = new XoopsImageSet();
        $imgset->assignVars(['imgset_id' => 3]);

        $result = $handler->delete($imgset);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteRejectsForeignObject(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $foreign = new XoopsObject();

        $result = $handler->delete($foreign);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageSetHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsArray(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgset_id'    => 1,
                'imgset_name'  => 'Default',
                'imgset_refid' => 0,
            ],
            [
                'imgset_id'    => 2,
                'imgset_name'  => 'Custom',
                'imgset_refid' => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $imgsets = $handler->getObjects();

        $this->assertCount(2, $imgsets);
        $this->assertInstanceOf(XoopsImageSet::class, $imgsets[0]);
        $this->assertEquals('Default', $imgsets[0]->getVar('imgset_name'));
        $this->assertEquals('Custom', $imgsets[1]->getVar('imgset_name'));
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgset_id'    => 10,
                'imgset_name'  => 'Keyed',
                'imgset_refid' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $imgsets = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $imgsets);
        $this->assertInstanceOf(XoopsImageSet::class, $imgsets[10]);
    }

    public function testHandlerGetObjectsReturnsEmptyOnFailedQuery(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $imgsets = $handler->getObjects();

        $this->assertIsArray($imgsets);
        $this->assertEmpty($imgsets);
    }

    public function testHandlerGetObjectsWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgset_id'    => 5,
                'imgset_name'  => 'Filtered',
                'imgset_refid' => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $criteria = new \Criteria('imgset_refid', 1);
        $imgsets = $handler->getObjects($criteria);

        $this->assertCount(1, $imgsets);
        $this->assertEquals(5, $imgsets[0]->getVar('imgset_id'));
    }

    // =========================================================================
    // XoopsImageSetHandler -- linkThemeset
    // =========================================================================

    public function testLinkThemesetSuccess(): void
    {
        $db = $this->createMockDatabase();
        // First exec for unlinkThemeset DELETE, second for INSERT
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $result = $handler->linkThemeset(1, 'default');

        $this->assertTrue($result);
    }

    public function testLinkThemesetWithZeroIdReturnsFalse(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->linkThemeset(0, 'default');

        $this->assertFalse($result);
    }

    public function testLinkThemesetWithNegativeIdReturnsFalse(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->linkThemeset(-1, 'default');

        $this->assertFalse($result);
    }

    public function testLinkThemesetWithEmptyNameReturnsFalse(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->linkThemeset(1, '');

        $this->assertFalse($result);
    }

    public function testLinkThemesetWithWhitespaceOnlyNameReturnsFalse(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->linkThemeset(1, '   ');

        $this->assertFalse($result);
    }

    public function testLinkThemesetInsertFailureReturnsFalse(): void
    {
        $db = $this->createMockDatabase();
        // First exec (unlink DELETE) succeeds, second (INSERT) fails
        $db->method('exec')->willReturnOnConsecutiveCalls(true, false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $result = $handler->linkThemeset(1, 'default');

        $this->assertFalse($result);
    }

    public function testLinkThemesetUnlinkFailureReturnsFalse(): void
    {
        $db = $this->createMockDatabase();
        // Unlink DELETE fails
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $result = $handler->linkThemeset(1, 'default');

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageSetHandler -- unlinkThemeset
    // =========================================================================

    public function testUnlinkThemesetSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $result = $handler->unlinkThemeset(1, 'default');

        $this->assertTrue($result);
    }

    public function testUnlinkThemesetWithZeroIdReturnsFalse(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->unlinkThemeset(0, 'default');

        $this->assertFalse($result);
    }

    public function testUnlinkThemesetWithNegativeIdReturnsFalse(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->unlinkThemeset(-1, 'default');

        $this->assertFalse($result);
    }

    public function testUnlinkThemesetWithEmptyNameReturnsFalse(): void
    {
        $handler = $this->createHandler('XoopsImageSetHandler');

        $result = $handler->unlinkThemeset(1, '');

        $this->assertFalse($result);
    }

    public function testUnlinkThemesetExecFailureReturnsFalse(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $result = $handler->unlinkThemeset(1, 'default');

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsImageSetHandler -- getList
    // =========================================================================

    public function testHandlerGetListReturnsIdNameMap(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgset_id'    => 1,
                'imgset_name'  => 'Default',
                'imgset_refid' => 0,
            ],
            [
                'imgset_id'    => 2,
                'imgset_name'  => 'Custom',
                'imgset_refid' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $list = $handler->getList();

        $this->assertIsArray($list);
        $this->assertArrayHasKey(1, $list);
        $this->assertEquals('Default', $list[1]);
        $this->assertArrayHasKey(2, $list);
        $this->assertEquals('Custom', $list[2]);
    }

    public function testHandlerGetListWithRefid(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'imgset_id'    => 3,
                'imgset_name'  => 'Ref Set',
                'imgset_refid' => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $list = $handler->getList(1);

        $this->assertIsArray($list);
        $this->assertArrayHasKey(3, $list);
        $this->assertEquals('Ref Set', $list[3]);
    }

    public function testHandlerGetListWithTplset(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $list = $handler->getList(null, 'default');

        $this->assertIsArray($list);
        $this->assertEmpty($list);
    }

    public function testHandlerGetListReturnsEmptyWhenNoResults(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $list = $handler->getList();

        $this->assertIsArray($list);
        $this->assertEmpty($list);
    }

    // =========================================================================
    // Type safety and edge cases
    // =========================================================================

    public function testGetCastsIdToInt(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'imgset_id'    => 1,
            'imgset_name'  => 'Test',
            'imgset_refid' => 0,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        $imgset = $handler->get('1');

        $this->assertInstanceOf(XoopsImageSet::class, $imgset);
    }

    public function testPublicPropertiesAreAccessible(): void
    {
        $imgset = new XoopsImageSet();

        // PHP 8.2 dynamic properties fix
        $imgset->imgset_id = 1;
        $imgset->imgset_name = 'test';
        $imgset->imgset_refid = 0;

        $this->assertEquals(1, $imgset->imgset_id);
        $this->assertEquals('test', $imgset->imgset_name);
        $this->assertEquals(0, $imgset->imgset_refid);
    }

    public function testLinkThemesetTrimsName(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsImageSetHandler', $db);
        // Name with leading/trailing spaces should be trimmed
        $result = $handler->linkThemeset(1, '  default  ');

        $this->assertTrue($result);
    }
}
