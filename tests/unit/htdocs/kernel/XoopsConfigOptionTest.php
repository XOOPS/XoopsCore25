<?php

declare(strict_types=1);

namespace kernel;

use XoopsConfigOption;
use XoopsConfigOptionHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/configoption.php';

/**
 * Unit tests for XoopsConfigOption and XoopsConfigOptionHandler.
 */
class XoopsConfigOptionTest extends KernelTestCase
{
    // =========================================================================
    // XoopsConfigOption -- constructor and variable initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $option = new XoopsConfigOption();

        $this->assertInstanceOf(XoopsConfigOption::class, $option);
        $this->assertInstanceOf(XoopsObject::class, $option);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $option = new XoopsConfigOption();

        $expectedVars = ['confop_id', 'confop_name', 'confop_value', 'conf_id'];

        $vars = $option->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testConfIdDefaultsToZero(): void
    {
        $option = new XoopsConfigOption();

        $this->assertEquals(0, $option->getVar('conf_id'));
    }

    public function testConfopNameIsRequired(): void
    {
        $option = new XoopsConfigOption();
        $vars = $option->getVars();

        $this->assertTrue($vars['confop_name']['required']);
    }

    public function testConfopValueIsRequired(): void
    {
        $option = new XoopsConfigOption();
        $vars = $option->getVars();

        $this->assertTrue($vars['confop_value']['required']);
    }

    public function testConfopNameMaxLength(): void
    {
        $option = new XoopsConfigOption();
        $vars = $option->getVars();

        $this->assertEquals(255, $vars['confop_name']['maxlength']);
    }

    public function testConfopValueMaxLength(): void
    {
        $option = new XoopsConfigOption();
        $vars = $option->getVars();

        $this->assertEquals(255, $vars['confop_value']['maxlength']);
    }

    // =========================================================================
    // XoopsConfigOption -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $option = new XoopsConfigOption();
        $option->assignVar('confop_id', 7);

        $this->assertEquals(7, $option->id());
    }

    public function testConfopIdAccessor(): void
    {
        $option = new XoopsConfigOption();
        $option->assignVar('confop_id', 12);

        $this->assertEquals(12, $option->confop_id());
    }

    public function testConfopNameAccessor(): void
    {
        $option = new XoopsConfigOption();
        $option->assignVar('confop_name', 'Yes');

        $this->assertEquals('Yes', $option->confop_name());
    }

    public function testConfopValueAccessor(): void
    {
        $option = new XoopsConfigOption();
        $option->assignVar('confop_value', '1');

        $this->assertEquals('1', $option->confop_value());
    }

    public function testConfIdAccessor(): void
    {
        $option = new XoopsConfigOption();
        $option->assignVar('conf_id', 99);

        $this->assertEquals(99, $option->conf_id());
    }

    // =========================================================================
    // XoopsConfigOption -- assignVars round-trip
    // =========================================================================

    public function testAssignVarsAndRetrieve(): void
    {
        $option = new XoopsConfigOption();
        $option->assignVars([
            'confop_id'    => 5,
            'confop_name'  => 'Option A',
            'confop_value' => 'val_a',
            'conf_id'      => 10,
        ]);

        $this->assertEquals(5, $option->confop_id());
        $this->assertEquals('Option A', $option->confop_name());
        $this->assertEquals('val_a', $option->confop_value());
        $this->assertEquals(10, $option->conf_id());
    }

    // =========================================================================
    // XoopsConfigOptionHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewOption(): void
    {
        $handler = $this->createHandler('XoopsConfigOptionHandler');

        $option = $handler->create();

        $this->assertInstanceOf(XoopsConfigOption::class, $option);
        $this->assertTrue($option->isNew());
    }

    public function testHandlerCreateNotNewReturnsFlaggedOption(): void
    {
        $handler = $this->createHandler('XoopsConfigOptionHandler');

        $option = $handler->create(false);

        $this->assertInstanceOf(XoopsConfigOption::class, $option);
        $this->assertFalse($option->isNew());
    }

    // =========================================================================
    // XoopsConfigOptionHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsOptionForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'confop_id'    => 1,
            'confop_name'  => 'Yes',
            'confop_value' => '1',
            'conf_id'      => 5,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $option = $handler->get(1);

        $this->assertInstanceOf(XoopsConfigOption::class, $option);
        $this->assertEquals(1, $option->getVar('confop_id'));
        $this->assertEquals('Yes', $option->getVar('confop_name'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsConfigOptionHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsConfigOptionHandler');

        $result = $handler->get(-5);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $result = $handler->get(1);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigOptionHandler -- insert
    // =========================================================================

    public function testHandlerInsertNewOptionReturnsId(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(15);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $option = new XoopsConfigOption();
        $option->setNew();
        $option->setVar('confop_name', 'Yes');
        $option->setVar('confop_value', '1');
        $option->setVar('conf_id', 5);

        $result = $handler->insert($option);

        $this->assertEquals(15, $result);
        $this->assertEquals(15, $option->getVar('confop_id'));
    }

    public function testHandlerInsertUpdateExistingOption(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $option = new XoopsConfigOption();
        // Not new -- simulate existing record
        $option->assignVar('confop_id', 10);
        $option->setVar('confop_name', 'Updated');
        $option->setVar('confop_value', 'new_val');
        $option->setVar('conf_id', 5);

        $result = $handler->insert($option);

        // insert returns confop_id on success for updates too
        $this->assertEquals(10, $result);
    }

    public function testHandlerInsertReturnsTrueIfNotDirty(): void
    {
        $handler = $this->createHandler('XoopsConfigOptionHandler');
        $option = new XoopsConfigOption();

        $result = $handler->insert($option);

        $this->assertTrue($result);
    }

    public function testHandlerInsertReturnsFalseForWrongClass(): void
    {
        $handler = $this->createHandler('XoopsConfigOptionHandler');
        $notAnOption = new \XoopsObject();

        $result = $handler->insert($notAnOption);

        $this->assertFalse($result);
    }

    public function testHandlerInsertReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $option = new XoopsConfigOption();
        $option->setNew();
        $option->setVar('confop_name', 'Fail');
        $option->setVar('confop_value', '0');
        $option->setVar('conf_id', 1);

        $result = $handler->insert($option);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigOptionHandler -- delete
    // =========================================================================

    public function testHandlerDeleteSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $option = new XoopsConfigOption();
        $option->assignVar('confop_id', 3);

        $result = $handler->delete($option);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteReturnsFalseForWrongClass(): void
    {
        $handler = $this->createHandler('XoopsConfigOptionHandler');
        $notAnOption = new \XoopsObject();

        $result = $handler->delete($notAnOption);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $option = new XoopsConfigOption();
        $option->assignVar('confop_id', 3);

        $result = $handler->delete($option);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigOptionHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsArray(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confop_id' => 1, 'confop_name' => 'Yes', 'confop_value' => '1', 'conf_id' => 5],
            ['confop_id' => 2, 'confop_name' => 'No', 'confop_value' => '0', 'conf_id' => 5],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $criteria = new \CriteriaCompo(new \Criteria('conf_id', 5));
        $result = $handler->getObjects($criteria);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsConfigOption::class, $result[0]);
        $this->assertEquals('Yes', $result[0]->getVar('confop_name'));
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confop_id' => 20, 'confop_name' => 'Maybe', 'confop_value' => '2', 'conf_id' => 5],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $result = $handler->getObjects(new \CriteriaCompo(), true);

        $this->assertArrayHasKey(20, $result);
        $this->assertInstanceOf(XoopsConfigOption::class, $result[20]);
    }

    public function testHandlerGetObjectsNoCriteria(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            ['confop_id' => 1, 'confop_name' => 'Opt1', 'confop_value' => 'v1', 'conf_id' => 1],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $result = $handler->getObjects(null);

        $this->assertCount(1, $result);
    }

    public function testHandlerGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $result = $handler->getObjects(null);

        $this->assertSame([], $result);
    }

    // =========================================================================
    // XoopsConfigOptionHandler -- getCount
    // =========================================================================

    public function testHandlerGetCountReturnsInt(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(['count' => 7]);
        $db->method('freeRecordSet')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $count = $handler->getCount(null);

        $this->assertSame(7, $count);
    }

    public function testHandlerGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(['count' => 3]);
        $db->method('freeRecordSet')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $criteria = new \CriteriaCompo(new \Criteria('conf_id', 5));
        $count = $handler->getCount($criteria);

        $this->assertSame(3, $count);
    }

    public function testHandlerGetCountThrowsOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);
        $db->method('error')->willReturn('mock error');

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);

        $this->expectException(\RuntimeException::class);
        $handler->getCount(null);
    }

    public function testHandlerGetCountCallsFreeRecordSet(): void
    {
        $db = $this->createMockDatabase();
        $result = 'mock_result';
        $db->method('query')->willReturn($result);
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(['count' => 0]);
        $db->expects($this->once())->method('freeRecordSet')->with($result);

        $handler = $this->createHandler('XoopsConfigOptionHandler', $db);
        $handler->getCount(null);
    }
}
