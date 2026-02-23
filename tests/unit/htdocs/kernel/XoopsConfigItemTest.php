<?php

declare(strict_types=1);

namespace kernel;

use XoopsConfigItem;
use XoopsConfigItemHandler;
use XoopsObject;

require_once XOOPS_ROOT_PATH . '/kernel/configitem.php';

/**
 * Unit tests for XoopsConfigItem and XoopsConfigItemHandler.
 */
class XoopsConfigItemTest extends KernelTestCase
{
    // =========================================================================
    // XoopsConfigItem -- constructor and variable initialization
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $item = new XoopsConfigItem();

        $this->assertInstanceOf(XoopsConfigItem::class, $item);
        $this->assertInstanceOf(XoopsObject::class, $item);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $item = new XoopsConfigItem();

        $expectedVars = [
            'conf_id', 'conf_modid', 'conf_catid', 'conf_name',
            'conf_title', 'conf_value', 'conf_desc', 'conf_formtype',
            'conf_valuetype', 'conf_order',
        ];

        $vars = $item->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testConfOptionsDefaultsToEmptyArray(): void
    {
        $item = new XoopsConfigItem();

        $this->assertSame([], $item->_confOptions);
    }

    // =========================================================================
    // XoopsConfigItem -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_id', 42);

        $this->assertEquals(42, $item->id());
    }

    public function testConfIdAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_id', 10);

        $this->assertEquals(10, $item->conf_id());
    }

    public function testConfModidAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_modid', 5);

        $this->assertEquals(5, $item->conf_modid());
    }

    public function testConfCatidAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_catid', XOOPS_CONF_USER);

        $this->assertEquals(XOOPS_CONF_USER, $item->conf_catid());
    }

    public function testConfNameAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_name', 'sitename');

        $this->assertEquals('sitename', $item->conf_name());
    }

    public function testConfTitleAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_title', '_CI_SITENAME');

        $this->assertEquals('_CI_SITENAME', $item->conf_title());
    }

    public function testConfValueAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_value', 'My Site');

        $this->assertEquals('My Site', $item->conf_value());
    }

    public function testConfDescAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_desc', 'Site description');

        $this->assertEquals('Site description', $item->conf_desc());
    }

    public function testConfFormtypeAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_formtype', 'textbox');

        $this->assertEquals('textbox', $item->conf_formtype());
    }

    public function testConfValuetypeAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'text');

        $this->assertEquals('text', $item->conf_valuetype());
    }

    public function testConfOrderAccessor(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_order', 3);

        $this->assertEquals(3, $item->conf_order());
    }

    // =========================================================================
    // XoopsConfigItem -- getConfValueForOutput
    // =========================================================================

    public function testGetConfValueForOutputInt(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'int');
        $item->assignVar('conf_value', '42');

        $result = $item->getConfValueForOutput();

        $this->assertSame(42, $result);
    }

    public function testGetConfValueForOutputFloat(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'float');
        $item->assignVar('conf_value', '3.14');

        $result = $item->getConfValueForOutput();

        $this->assertSame(3.14, $result);
    }

    public function testGetConfValueForOutputArrayValid(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'array');
        $serialized = serialize(['one', 'two', 'three']);
        $item->assignVar('conf_value', $serialized);

        $result = $item->getConfValueForOutput();

        $this->assertIsArray($result);
        $this->assertSame(['one', 'two', 'three'], $result);
    }

    public function testGetConfValueForOutputArrayInvalidReturnsEmptyArray(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'array');
        $item->assignVar('conf_value', 'not_serialized');

        $result = $item->getConfValueForOutput();

        $this->assertSame([], $result);
    }

    public function testGetConfValueForOutputTextarea(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'textarea');
        $item->assignVar('conf_value', 'Some long text');

        $result = $item->getConfValueForOutput();

        // textarea uses default format (not 'N'), so value goes through getVar with default
        $this->assertNotNull($result);
    }

    public function testGetConfValueForOutputDefaultText(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'text');
        $item->assignVar('conf_value', 'Hello World');

        $result = $item->getConfValueForOutput();

        $this->assertEquals('Hello World', $result);
    }

    public function testGetConfValueForOutputUnknownType(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'other');
        $item->assignVar('conf_value', 'some value');

        $result = $item->getConfValueForOutput();

        $this->assertEquals('some value', $result);
    }

    // =========================================================================
    // XoopsConfigItem -- setConfValueForInput
    // =========================================================================

    public function testSetConfValueForInputArrayFromArray(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'array');

        $value = ['one', 'two', 'three'];
        $item->setConfValueForInput($value);

        $stored = $item->getVar('conf_value', 'N');
        $this->assertEquals(serialize(['one', 'two', 'three']), $stored);
    }

    public function testSetConfValueForInputArrayFromString(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'array');

        $value = 'one|two|three';
        $item->setConfValueForInput($value);

        $stored = $item->getVar('conf_value', 'N');
        $this->assertEquals(serialize(['one', 'two', 'three']), $stored);
    }

    public function testSetConfValueForInputTextTrimsWhitespace(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'text');

        $value = '  Hello World  ';
        $item->setConfValueForInput($value);

        $stored = $item->getVar('conf_value', 'N');
        $this->assertEquals('Hello World', $stored);
    }

    public function testSetConfValueForInputDefaultPassesThrough(): void
    {
        $item = new XoopsConfigItem();
        $item->assignVar('conf_valuetype', 'int');

        $value = '42';
        $item->setConfValueForInput($value);

        $stored = $item->getVar('conf_value', 'N');
        $this->assertEquals('42', $stored);
    }

    // =========================================================================
    // XoopsConfigItem -- setConfOptions / getConfOptions / clearConfOptions
    // =========================================================================

    public function testSetConfOptionsSingleObject(): void
    {
        $item = new XoopsConfigItem();
        $option = new \stdClass();
        $option->name = 'opt1';

        $item->setConfOptions($option);

        $options = &$item->getConfOptions();
        $this->assertCount(1, $options);
    }

    public function testSetConfOptionsArrayOfObjects(): void
    {
        $item = new XoopsConfigItem();
        $opt1 = new \stdClass();
        $opt1->name = 'opt1';
        $opt2 = new \stdClass();
        $opt2->name = 'opt2';

        $item->setConfOptions([$opt1, $opt2]);

        $options = &$item->getConfOptions();
        $this->assertCount(2, $options);
    }

    public function testSetConfOptionsIgnoresNonObject(): void
    {
        $item = new XoopsConfigItem();
        // A non-array, non-object value (string) should not be added
        $item->setConfOptions('not_an_object');

        $options = &$item->getConfOptions();
        $this->assertCount(0, $options);
    }

    public function testGetConfOptionsReturnsReference(): void
    {
        $item = new XoopsConfigItem();
        $options = &$item->getConfOptions();

        $this->assertIsArray($options);
        $this->assertSame([], $options);
    }

    public function testClearConfOptionsResetsArray(): void
    {
        $item = new XoopsConfigItem();
        $opt = new \stdClass();
        $item->setConfOptions($opt);

        $this->assertCount(1, $item->_confOptions);

        $item->clearConfOptions();

        $this->assertSame([], $item->_confOptions);
    }

    // =========================================================================
    // XoopsConfigItem -- XOOPS_CONF constants
    // =========================================================================

    public function testXoopsConfConstantsAreDefined(): void
    {
        $this->assertSame(1, XOOPS_CONF);
        $this->assertSame(2, XOOPS_CONF_USER);
        $this->assertSame(3, XOOPS_CONF_METAFOOTER);
        $this->assertSame(4, XOOPS_CONF_CENSOR);
        $this->assertSame(5, XOOPS_CONF_SEARCH);
        $this->assertSame(6, XOOPS_CONF_MAILER);
        $this->assertSame(7, XOOPS_CONF_AUTH);
    }

    // =========================================================================
    // XoopsConfigItemHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewConfigItem(): void
    {
        $handler = $this->createHandler('XoopsConfigItemHandler');

        $config = $handler->create();

        $this->assertInstanceOf(XoopsConfigItem::class, $config);
        $this->assertTrue($config->isNew());
    }

    public function testHandlerCreateNotNewReturnsExistingConfigItem(): void
    {
        $handler = $this->createHandler('XoopsConfigItemHandler');

        $config = $handler->create(false);

        $this->assertInstanceOf(XoopsConfigItem::class, $config);
        $this->assertFalse($config->isNew());
    }

    // =========================================================================
    // XoopsConfigItemHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsConfigItemForValidId(): void
    {
        $db = $this->createMockDatabase();
        $row = [
            'conf_id'        => 1,
            'conf_modid'     => 0,
            'conf_catid'     => 1,
            'conf_name'      => 'sitename',
            'conf_title'     => '_CI_SITENAME',
            'conf_value'     => 'XOOPS',
            'conf_desc'      => '',
            'conf_formtype'  => 'textbox',
            'conf_valuetype' => 'text',
            'conf_order'     => 0,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $config = $handler->get(1);

        $this->assertInstanceOf(XoopsConfigItem::class, $config);
        $this->assertEquals(1, $config->getVar('conf_id'));
        $this->assertEquals('sitename', $config->getVar('conf_name'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $handler = $this->createHandler('XoopsConfigItemHandler');

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $handler = $this->createHandler('XoopsConfigItemHandler');

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $result = $handler->get(1);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigItemHandler -- insert
    // =========================================================================

    public function testHandlerInsertNewConfigItem(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(10);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->setVar('conf_modid', 0);
        $config->setVar('conf_catid', 1);
        $config->setVar('conf_name', 'sitename');
        $config->setVar('conf_title', '_CI_SITENAME');
        $config->setVar('conf_value', 'XOOPS');
        $config->setVar('conf_desc', '');
        $config->setVar('conf_formtype', 'textbox');
        $config->setVar('conf_valuetype', 'text');
        $config->setVar('conf_order', 0);

        $result = $handler->insert($config);

        $this->assertTrue($result);
        $this->assertEquals(10, $config->getVar('conf_id'));
    }

    public function testHandlerInsertUpdateExistingConfigItem(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $config = new XoopsConfigItem();
        // Not new -- simulate existing record
        $config->assignVar('conf_id', 5);
        $config->setVar('conf_modid', 0);
        $config->setVar('conf_catid', 1);
        $config->setVar('conf_name', 'updated_name');
        $config->setVar('conf_title', '_CI_UPDATED');
        $config->setVar('conf_value', 'Updated');
        $config->setVar('conf_desc', '');
        $config->setVar('conf_formtype', 'textbox');
        $config->setVar('conf_valuetype', 'text');
        $config->setVar('conf_order', 1);

        $result = $handler->insert($config);

        $this->assertTrue($result);
    }

    public function testHandlerInsertReturnsTrueIfNotDirty(): void
    {
        $handler = $this->createHandler('XoopsConfigItemHandler');
        // Create a config but don't set any vars (not dirty)
        $config = new XoopsConfigItem();

        $result = $handler->insert($config);

        $this->assertTrue($result);
    }

    public function testHandlerInsertReturnsFalseForWrongClass(): void
    {
        $handler = $this->createHandler('XoopsConfigItemHandler');
        $notAConfig = new \XoopsObject();

        $result = $handler->insert($notAConfig);

        $this->assertFalse($result);
    }

    public function testHandlerInsertReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $config = new XoopsConfigItem();
        $config->setNew();
        $config->setVar('conf_modid', 0);
        $config->setVar('conf_catid', 1);
        $config->setVar('conf_name', 'test');
        $config->setVar('conf_title', 'Test');
        $config->setVar('conf_value', 'val');
        $config->setVar('conf_desc', '');
        $config->setVar('conf_formtype', 'textbox');
        $config->setVar('conf_valuetype', 'text');
        $config->setVar('conf_order', 0);

        $result = $handler->insert($config);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigItemHandler -- delete
    // =========================================================================

    public function testHandlerDeleteSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);

        $result = $handler->delete($config);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteReturnsFalseForWrongClass(): void
    {
        $handler = $this->createHandler('XoopsConfigItemHandler');
        $notAConfig = new \XoopsObject();

        $result = $handler->delete($notAConfig);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $config = new XoopsConfigItem();
        $config->assignVar('conf_id', 5);

        $result = $handler->delete($config);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsConfigItemHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsArray(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'conf_id' => 1, 'conf_modid' => 0, 'conf_catid' => 1,
                'conf_name' => 'sitename', 'conf_title' => '_CI_SITENAME',
                'conf_value' => 'XOOPS', 'conf_desc' => '', 'conf_formtype' => 'textbox',
                'conf_valuetype' => 'text', 'conf_order' => 0,
            ],
            [
                'conf_id' => 2, 'conf_modid' => 0, 'conf_catid' => 1,
                'conf_name' => 'slogan', 'conf_title' => '_CI_SLOGAN',
                'conf_value' => 'Welcome', 'conf_desc' => '', 'conf_formtype' => 'textbox',
                'conf_valuetype' => 'text', 'conf_order' => 1,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $criteria = new \CriteriaCompo(new \Criteria('conf_modid', 0));
        $result = $handler->getObjects($criteria);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsConfigItem::class, $result[0]);
        $this->assertEquals('sitename', $result[0]->getVar('conf_name'));
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'conf_id' => 10, 'conf_modid' => 0, 'conf_catid' => 1,
                'conf_name' => 'test', 'conf_title' => 'Test',
                'conf_value' => 'val', 'conf_desc' => '', 'conf_formtype' => 'textbox',
                'conf_valuetype' => 'text', 'conf_order' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $result = $handler->getObjects(new \CriteriaCompo(), true);

        $this->assertArrayHasKey(10, $result);
        $this->assertInstanceOf(XoopsConfigItem::class, $result[10]);
    }

    public function testHandlerGetObjectsNoCriteriaReturnsAll(): void
    {
        $db = $this->createMockDatabase();
        $rows = [
            [
                'conf_id' => 1, 'conf_modid' => 0, 'conf_catid' => 1,
                'conf_name' => 'sitename', 'conf_title' => 'Site Name',
                'conf_value' => 'XOOPS', 'conf_desc' => '', 'conf_formtype' => 'textbox',
                'conf_valuetype' => 'text', 'conf_order' => 0,
            ],
        ];
        $this->stubMultiRowResult($db, $rows);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $result = $handler->getObjects(null);

        $this->assertCount(1, $result);
    }

    public function testHandlerGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $result = $handler->getObjects(null);

        $this->assertSame([], $result);
    }

    // =========================================================================
    // XoopsConfigItemHandler -- getCount
    // =========================================================================

    public function testHandlerGetCountReturnsInt(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 5);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $result = $handler->getCount(null);

        $this->assertSame(5, $result);
    }

    public function testHandlerGetCountReturnsZeroOnFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $result = $handler->getCount(null);

        $this->assertSame(0, $result);
    }

    public function testHandlerGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 3);

        $handler = $this->createHandler('XoopsConfigItemHandler', $db);
        $criteria = new \CriteriaCompo(new \Criteria('conf_modid', 0));
        $result = $handler->getCount($criteria);

        $this->assertSame(3, $result);
    }
}
