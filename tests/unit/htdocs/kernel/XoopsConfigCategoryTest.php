<?php

declare(strict_types=1);

namespace kernel;

use XoopsConfigCategory;
use XoopsConfigCategoryHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/configcategory.php';

/**
 * Unit tests for XoopsConfigCategory and XoopsConfigCategoryHandler.
 */
class XoopsConfigCategoryTest extends KernelTestCase
{
    // =========================================================================
    // XoopsConfigCategory -- constructor and variable initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $category = new XoopsConfigCategory();

        $this->assertInstanceOf(XoopsConfigCategory::class, $category);
        $this->assertInstanceOf(XoopsObject::class, $category);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $category = new XoopsConfigCategory();

        $expectedVars = ['confcat_id', 'confcat_name', 'confcat_order'];

        $vars = $category->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testConfcatOrderDefaultsToZero(): void
    {
        $category = new XoopsConfigCategory();

        $this->assertEquals(0, $category->getVar('confcat_order'));
    }

    // =========================================================================
    // XoopsConfigCategory -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $category = new XoopsConfigCategory();
        $category->assignVar('confcat_id', 3);

        $this->assertEquals(3, $category->id());
    }

    public function testConfcatIdAccessor(): void
    {
        $category = new XoopsConfigCategory();
        $category->assignVar('confcat_id', 5);

        $this->assertEquals(5, $category->confcat_id());
    }

    public function testConfcatNameAccessor(): void
    {
        $category = new XoopsConfigCategory();
        $category->assignVar('confcat_name', 'General Settings');

        $this->assertEquals('General Settings', $category->confcat_name());
    }

    public function testConfcatOrderAccessor(): void
    {
        $category = new XoopsConfigCategory();
        $category->assignVar('confcat_order', 10);

        $this->assertEquals(10, $category->confcat_order());
    }

    // =========================================================================
    // XoopsConfigCategory -- assignVars round-trip
    // =========================================================================

    public function testAssignVarsAndRetrieve(): void
    {
        $category = new XoopsConfigCategory();
        $category->assignVars([
            'confcat_id'    => 2,
            'confcat_name'  => 'User Settings',
            'confcat_order' => 5,
        ]);

        $this->assertEquals(2, $category->confcat_id());
        $this->assertEquals('User Settings', $category->confcat_name());
        $this->assertEquals(5, $category->confcat_order());
    }

    // =========================================================================
    // XoopsConfigCategoryHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewCategory(): void
    {
        $handler = $this->createHandler('XoopsConfigCategoryHandler');

        $category = $handler->create();

        $this->assertInstanceOf(XoopsConfigCategory::class, $category);
        $this->assertTrue($category->isNew());
    }

    public function testHandlerCreateNotNewReturnsFlaggedCategory(): void
    {
        $handler = $this->createHandler('XoopsConfigCategoryHandler');

        $category = $handler->create(false);

        $this->assertInstanceOf(XoopsConfigCategory::class, $category);
        $this->assertFalse($category->isNew());
    }

    // =========================================================================
    // XoopsConfigCategoryHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsCategoryForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'confcat_id'    => 1,
            'confcat_name'  => 'General Settings',
            'confcat_order' => 0,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $category = $handler->get(1);

        $this->assertInstanceOf(XoopsConfigCategory::class, $category);
        $this->assertEquals(1, $category->getVar('confcat_id'));
        $this->assertEquals('General Settings', $category->getVar('confcat_name'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsConfigCategoryHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsConfigCategoryHandler');

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $result = $handler->get(1);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigCategoryHandler -- insert
    // =========================================================================

    public function testHandlerInsertNewCategoryReturnsId(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(8);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $category = new XoopsConfigCategory();
        $category->setNew();
        $category->setVar('confcat_name', 'New Category');
        $category->setVar('confcat_order', 5);

        $result = $handler->insert($category);

        $this->assertEquals(8, $result);
        $this->assertEquals(8, $category->getVar('confcat_id'));
    }

    public function testHandlerInsertUpdateExistingCategory(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $category = new XoopsConfigCategory();
        // Not new -- simulate existing record
        $category->assignVar('confcat_id', 3);
        $category->setVar('confcat_name', 'Updated Name');
        $category->setVar('confcat_order', 10);

        $result = $handler->insert($category);

        // Returns confcat_id on success
        $this->assertEquals(3, $result);
    }

    public function testHandlerInsertReturnsTrueIfNotDirty(): void
    {
        $handler = $this->createHandler('XoopsConfigCategoryHandler');
        $category = new XoopsConfigCategory();

        $result = $handler->insert($category);

        $this->assertTrue($result);
    }

    public function testHandlerInsertReturnsFalseForWrongClass(): void
    {
        $handler = $this->createHandler('XoopsConfigCategoryHandler');
        $notACategory = new \XoopsObject();

        $result = $handler->insert($notACategory);

        $this->assertFalse($result);
    }

    public function testHandlerInsertReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $category = new XoopsConfigCategory();
        $category->setNew();
        $category->setVar('confcat_name', 'Fail');
        $category->setVar('confcat_order', 0);

        $result = $handler->insert($category);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigCategoryHandler -- delete
    // =========================================================================

    public function testHandlerDeleteSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $category = new XoopsConfigCategory();
        $category->assignVar('confcat_id', 4);

        $result = $handler->delete($category);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteReturnsFalseForWrongClass(): void
    {
        $handler = $this->createHandler('XoopsConfigCategoryHandler');
        $notACategory = new \XoopsObject();

        $result = $handler->delete($notACategory);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $category = new XoopsConfigCategory();
        $category->assignVar('confcat_id', 4);

        $result = $handler->delete($category);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigCategoryHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsArray(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confcat_id' => 1, 'confcat_name' => 'General', 'confcat_order' => 0],
            ['confcat_id' => 2, 'confcat_name' => 'User', 'confcat_order' => 1],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $criteria = new \CriteriaCompo();
        $result = $handler->getObjects($criteria);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsConfigCategory::class, $result[0]);
        $this->assertEquals('General', $result[0]->getVar('confcat_name'));
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confcat_id' => 10, 'confcat_name' => 'Test', 'confcat_order' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $result = $handler->getObjects(new \CriteriaCompo(), true);

        $this->assertArrayHasKey(10, $result);
        $this->assertInstanceOf(XoopsConfigCategory::class, $result[10]);
    }

    public function testHandlerGetObjectsWithValidSort(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confcat_id' => 1, 'confcat_name' => 'Alpha', 'confcat_order' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $criteria = new \CriteriaCompo();
        $criteria->setSort('confcat_name');
        $result = $handler->getObjects($criteria);

        $this->assertCount(1, $result);
    }

    public function testHandlerGetObjectsWithInvalidSortDefaultsToConfcatOrder(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confcat_id' => 1, 'confcat_name' => 'Test', 'confcat_order' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $criteria = new \CriteriaCompo();
        $criteria->setSort('invalid_column');
        $result = $handler->getObjects($criteria);

        // Should still work, defaulting sort to confcat_order
        $this->assertCount(1, $result);
    }

    public function testHandlerGetObjectsWithConfcatIdSort(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confcat_id' => 1, 'confcat_name' => 'A', 'confcat_order' => 2],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $criteria = new \CriteriaCompo();
        $criteria->setSort('confcat_id');
        $result = $handler->getObjects($criteria);

        $this->assertCount(1, $result);
    }

    public function testHandlerGetObjectsWithConfcatOrderSort(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confcat_id' => 1, 'confcat_name' => 'A', 'confcat_order' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $criteria = new \CriteriaCompo();
        $criteria->setSort('confcat_order');
        $result = $handler->getObjects($criteria);

        $this->assertCount(1, $result);
    }

    public function testHandlerGetObjectsNoCriteria(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confcat_id' => 1, 'confcat_name' => 'General', 'confcat_order' => 0],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $result = $handler->getObjects(null);

        $this->assertCount(1, $result);
    }

    public function testHandlerGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigCategoryHandler', $db);
        $result = $handler->getObjects(null);

        $this->assertSame([], $result);
    }

    // =========================================================================
    // XoopsConfigCategoryHandler -- getCatByModule (deprecated)
    // =========================================================================

    public function testGetCatByModuleReturnsFalse(): void
    {
        // Set up a dummy logger to absorb the deprecated call
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = new class {
                public function addDeprecated(string $msg): void {}
            };
        }

        $handler = $this->createHandler('XoopsConfigCategoryHandler');
        $result = $handler->getCatByModule(0);

        $this->assertFalse($result);
    }
}
