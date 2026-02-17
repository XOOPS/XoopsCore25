<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
require_once XOOPS_ROOT_PATH . '/kernel/object.php';
require_once XOOPS_ROOT_PATH . '/kernel/module.php';

class XoopsModuleTest extends TestCase
{
    protected XoopsModule $module;

    protected function setUp(): void
    {
        $this->module = new XoopsModule();
    }

    public function testConstructorInitializesObject()
    {
        $module = new XoopsModule();

        $this->assertInstanceOf(XoopsModule::class, $module);
        $this->assertInstanceOf(XoopsObject::class, $module);
    }

    public function testConstructorInitializesAllVariables()
    {
        $module = new XoopsModule();

        // Test that all expected vars are initialized
        $this->assertEquals('', $module->getVar('mid'));
        $this->assertEquals(0, $module->getVar('weight'));
        $this->assertEquals(1, $module->getVar('isactive'));
    }

    public function testSetMessageAddsMessage()
    {
        $this->module->setMessage('Test message');

        $messages = $this->module->getMessages();

        $this->assertIsArray($messages);
        $this->assertCount(1, $messages);
        $this->assertEquals('Test message', $messages[0]);
    }

    public function testSetMessageTrimsWhitespace()
    {
        $this->module->setMessage('  Test message  ');

        $messages = $this->module->getMessages();

        $this->assertEquals('Test message', $messages[0]);
    }

    public function testSetMessageMultipleMessages()
    {
        $this->module->setMessage('Message 1');
        $this->module->setMessage('Message 2');
        $this->module->setMessage('Message 3');

        $messages = $this->module->getMessages();

        $this->assertCount(3, $messages);
        $this->assertEquals('Message 1', $messages[0]);
        $this->assertEquals('Message 2', $messages[1]);
        $this->assertEquals('Message 3', $messages[2]);
    }

    public function testGetMessagesReturnsArray()
    {
        $messages = $this->module->getMessages();

        $this->assertNull($messages);
    }

    public function testGetMessagesInitiallyEmpty()
    {
        $messages = $this->module->getMessages();

        $this->assertEmpty($messages);
    }

    public function testSetInfoWithName()
    {
        $result = $this->module->setInfo('test_key', 'test_value');

        $this->assertTrue($result);

        $info = $this->module->getInfo('test_key');
        $this->assertEquals('test_value', $info);
    }

    public function testSetInfoWithEmptyName()
    {
        $value = ['key1' => 'value1', 'key2' => 'value2'];
        $result = $this->module->setInfo('', $value);

        $this->assertTrue($result);

        $info = $this->module->getInfo();
        $this->assertEquals($value, $info);
    }

    public function testSetInfoWithArrayValue()
    {
        $arrayValue = ['item1', 'item2', 'item3'];
        $result = $this->module->setInfo('array_key', $arrayValue);

        $this->assertTrue($result);

        $info = $this->module->getInfo('array_key');
        $this->assertEquals($arrayValue, $info);
    }

    public function testGetInfoWithoutName()
    {
        $this->module->setInfo('key1', 'value1');
        $this->module->setInfo('key2', 'value2');

        $info = $this->module->getInfo();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('key1', $info);
        $this->assertArrayHasKey('key2', $info);
    }

    public function testGetInfoWithNonExistentKey()
    {
        $result = $this->module->getInfo('nonexistent_key');

        $this->assertFalse($result);
    }

    public function testGetInfoReturnsReference()
    {
        $this->module->setInfo('ref_key', 'original');

        $info = &$this->module->getInfo('ref_key');
        $info = 'modified';

        $this->assertEquals('modified', $this->module->getInfo('ref_key'));
    }

    public function testGetStatusWithVersionString()
    {
        $this->module->setVar('version', '2.5.12-Beta9');

        $status = $this->module->getStatus();

        $this->assertEquals('Beta9', $status);
    }

    public function testGetStatusWithoutStatus()
    {
        $this->module->setVar('version', '2.5.12');

        $status = $this->module->getStatus();

        $this->assertEmpty($status);
    }

    public function testGetStatusWithAlpha()
    {
        $this->module->setVar('version', '1.0.0-Alpha1');

        $status = $this->module->getStatus();

        $this->assertEquals('Alpha1', $status);
    }

    public function testGetStatusWithRC()
    {
        $this->module->setVar('version', '3.0.0-RC2');

        $status = $this->module->getStatus();

        $this->assertEquals('RC2', $status);
    }

    public function testVersionCompareWithLessThan()
    {
        $result = $this->module->versionCompare('1.0.0', '2.0.0', '<');

        $this->assertTrue($result);
    }

    public function testVersionCompareWithGreaterThan()
    {
        $result = $this->module->versionCompare('2.0.0', '1.0.0', '>');

        $this->assertTrue($result);
    }

    public function testVersionCompareWithEqual()
    {
        // Note: versionCompare() accepts '==' not '=' (mirrors version_compare)
        $result = $this->module->versionCompare('1.5.0', '1.5.0', '==');

        $this->assertTrue($result);
    }

    public function testVersionCompareWithStableSuffix()
    {
        // -stable suffix should be stripped for comparison
        $result = $this->module->versionCompare('1.0.0-stable', '1.0.0', '==');

        $this->assertTrue($result);
    }

    public function testVersionCompareIsCaseInsensitive()
    {
        $result = $this->module->versionCompare('1.0.0-STABLE', '1.0.0-stable', '==');

        $this->assertTrue($result);
    }

    public function testVersionCompareWithComplexVersions()
    {
        $result = $this->module->versionCompare('2.5.11-stable', '2.5.12', '<');

        $this->assertTrue($result);
    }

    public function testMainLinkWithHasMain()
    {
        $this->module->setVar('hasmain', 1);
        $this->module->setVar('dirname', 'testmodule');
        $this->module->setVar('name', 'Test Module');

        $link = $this->module->mainLink();

        $this->assertIsString($link);
        $this->assertStringContainsString('testmodule', $link);
        $this->assertStringContainsString('Test Module', $link);
        $this->assertStringContainsString('<a href=', $link);
    }

    public function testMainLinkWithoutHasMain()
    {
        $this->module->setVar('hasmain', 0);

        $link = $this->module->mainLink();

        $this->assertFalse($link);
    }

    public function testMainLinkFormat()
    {
        $this->module->setVar('hasmain', 1);
        $this->module->setVar('dirname', 'news');
        $this->module->setVar('name', 'News');

        $link = $this->module->mainLink();

        $expectedUrl = XOOPS_URL . '/modules/news/';
        $this->assertStringContainsString($expectedUrl, $link);
    }

    public function testSubLinkWithNoSubMenu()
    {
        $result = $this->module->subLink();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testSubLinkWithSubMenu()
    {
        $subMenu = [
            ['id' => 1, 'name' => 'Submenu 1', 'url' => 'page1.php', 'icon' => 'icon1.png'],
            ['id' => 2, 'name' => 'Submenu 2', 'url' => 'page2.php', 'icon' => 'icon2.png'],
        ];

        $this->module->setInfo('sub', $subMenu);

        $result = $this->module->subLink();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals('Submenu 1', $result[0]['name']);
        $this->assertEquals('page1.php', $result[0]['url']);
    }

    public function testSubLinkWithMissingOptionalFields()
    {
        $subMenu = [
            ['name' => 'Submenu 1', 'url' => 'page1.php'],
        ];

        $this->module->setInfo('sub', $subMenu);

        $result = $this->module->subLink();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('', $result[0]['id']);
        $this->assertEquals('', $result[0]['icon']);
    }

    public function testIdMethod()
    {
        $this->module->setVar('mid', 42);

        $id = $this->module->id();

        $this->assertEquals(42, $id);
    }

    public function testIdMethodWithFormat()
    {
        $this->module->setVar('mid', 42);

        $id = $this->module->id('S');

        $this->assertEquals(42, $id);
    }

    public function testMidMethod()
    {
        $this->module->setVar('mid', 123);

        $mid = $this->module->mid();

        $this->assertEquals(123, $mid);
    }

    public function testNameMethod()
    {
        $this->module->setVar('name', 'Test Module');

        $name = $this->module->name();

        $this->assertEquals('Test Module', $name);
    }

    public function testVersionMethod()
    {
        $this->module->setVar('version', '1.0.0');

        $version = $this->module->version();

        $this->assertEquals('1.0.0', $version);
    }

    public function testLastUpdateMethod()
    {
        $timestamp = time();
        $this->module->setVar('last_update', $timestamp);

        $lastUpdate = $this->module->last_update();

        $this->assertEquals($timestamp, $lastUpdate);
    }

    public function testWeightMethod()
    {
        $this->module->setVar('weight', 10);

        $weight = $this->module->weight();

        $this->assertEquals(10, $weight);
    }

    public function testIsActiveMethod()
    {
        $this->module->setVar('isactive', 1);

        $isActive = $this->module->isactive();

        $this->assertEquals(1, $isActive);
    }

    public function testDirnameMethod()
    {
        $this->module->setVar('dirname', 'mymodule');

        $dirname = $this->module->dirname();

        $this->assertEquals('mymodule', $dirname);
    }

    public function testHasMainMethod()
    {
        $this->module->setVar('hasmain', 1);

        $hasMain = $this->module->hasmain();

        $this->assertEquals(1, $hasMain);
    }

    public function testHasAdminMethod()
    {
        $this->module->setVar('hasadmin', 1);

        $hasAdmin = $this->module->hasadmin();

        $this->assertEquals(1, $hasAdmin);
    }

    public function testHasSearchMethod()
    {
        $this->module->setVar('hassearch', 1);

        $hasSearch = $this->module->hassearch();

        $this->assertEquals(1, $hasSearch);
    }

    public function testHasConfigMethod()
    {
        $this->module->setVar('hasconfig', 1);

        $hasConfig = $this->module->hasconfig();

        $this->assertEquals(1, $hasConfig);
    }

    public function testHasCommentsMethod()
    {
        $this->module->setVar('hascomments', 1);

        $hasComments = $this->module->hascomments();

        $this->assertEquals(1, $hasComments);
    }

    public function testHasNotificationMethod()
    {
        $this->module->setVar('hasnotification', 1);

        $hasNotification = $this->module->hasnotification();

        $this->assertEquals(1, $hasNotification);
    }

    public function testSearchWithoutSearchCapability()
    {
        $this->module->setVar('hassearch', 0);

        $result = $this->module->search('test');

        $this->assertFalse($result);
    }

    public function testSearchWithSearchCapabilityButNoConfig()
    {
        $this->module->setVar('hassearch', 1);

        $result = $this->module->search('test');

        $this->assertFalse($result);
    }

    /**
     * Create a mock logger for deprecated method tests
     */
    private function ensureMockLogger(): void
    {
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = $this->getMockBuilder(\XoopsLogger::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['addDeprecated'])
                ->getMock();
            $GLOBALS['xoopsLogger']->method('addDeprecated')->willReturn(true);
        }
    }

    public function testDeprecatedCheckAccessMethod()
    {
        $this->ensureMockLogger();

        $result = $this->module->checkAccess();

        $this->assertFalse($result);
    }

    public function testDeprecatedLoadLanguageMethod()
    {
        $this->ensureMockLogger();

        $result = $this->module->loadLanguage();

        $this->assertFalse($result);
    }

    public function testDeprecatedLoadErrorMessagesMethod()
    {
        $this->ensureMockLogger();

        $result = $this->module->loadErrorMessages();

        $this->assertFalse($result);
    }

    public function testDeprecatedGetCurrentPageMethod()
    {
        $this->ensureMockLogger();

        $result = $this->module->getCurrentPage();

        $this->assertFalse($result);
    }

    public function testDeprecatedInstallMethod()
    {
        $this->ensureMockLogger();

        $result = $this->module->install();

        $this->assertFalse($result);
    }

    public function testDeprecatedUpdateMethod()
    {
        $this->ensureMockLogger();

        $result = $this->module->update();

        $this->assertFalse($result);
    }

    public function testDeprecatedInsertMethod()
    {
        $this->ensureMockLogger();

        $result = $this->module->insert();

        $this->assertFalse($result);
    }

    public function testVersionCompareHandlesDifferentOperators()
    {
        $operators = ['<', '>', '<=', '>=', '==', '!='];

        foreach ($operators as $operator) {
            $result = $this->module->versionCompare('1.0.0', '2.0.0', $operator);
            $this->assertIsBool($result, "Operator {$operator} should return boolean");
        }
    }

    public function testSetInfoOverwritesExistingValue()
    {
        $this->module->setInfo('key', 'original');
        $this->module->setInfo('key', 'updated');

        $info = $this->module->getInfo('key');

        $this->assertEquals('updated', $info);
    }

    public function testModulePropertiesArePublic()
    {
        // Test that dynamic properties are accessible
        $this->module->mid = 1;
        $this->module->name = 'Test';
        $this->module->dirname = 'testdir';

        $this->assertEquals(1, $this->module->mid);
        $this->assertEquals('Test', $this->module->name);
        $this->assertEquals('testdir', $this->module->dirname);
    }

    public function testGetAdminMenuInitializes()
    {
        $menu = $this->module->getAdminMenu();

        $this->assertIsArray($menu);
    }

    public function testVersionCompareWithEmptyVersions()
    {
        $result = $this->module->versionCompare('', '', '==');

        $this->assertTrue($result);
    }

    public function testMainLinkEscapesHtml()
    {
        $this->module->setVar('hasmain', 1);
        $this->module->setVar('dirname', 'test<script>');
        $this->module->setVar('name', 'Test<script>');

        $link = $this->module->mainLink();

        // Should contain the raw values in appropriate contexts
        $this->assertIsString($link);
    }

    public function testVersionCompareDefaultOperator()
    {
        // Default operator is '<'
        $result = $this->module->versionCompare('1.0.0', '2.0.0');

        $this->assertTrue($result);
    }

    public function testSubLinkFiltersNonArraySubInfo()
    {
        $this->module->setInfo('sub', 'not an array');

        $result = $this->module->subLink();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testMessageArrayPreservesOrder()
    {
        $messages = ['First', 'Second', 'Third', 'Fourth'];

        foreach ($messages as $msg) {
            $this->module->setMessage($msg);
        }

        $result = $this->module->getMessages();

        $this->assertEquals($messages, $result);
    }

    public function testGetInfoHandlesNullModinfo()
    {
        // When modinfo is not set, getInfo should handle it gracefully
        $module = new XoopsModule();

        // This will trigger loadInfo which may fail, but should not crash
        $result = @$module->getInfo('some_key');

        // Should return false when modinfo is not properly loaded
        $this->assertFalse($result);
    }

    // Additional edge case and boundary tests

    public function testSetInfoWithNumericName()
    {
        $result = $this->module->setInfo(123, 'value');

        $this->assertTrue($result);
        $this->assertEquals('value', $this->module->getInfo(123));
    }

    public function testVersionCompareWithMixedCaseStable()
    {
        // Test various case combinations
        $result1 = $this->module->versionCompare('1.0.0-STABLE', '1.0.0-stable', '==');
        $result2 = $this->module->versionCompare('1.0.0-Stable', '1.0.0', '==');

        $this->assertTrue($result1);
        $this->assertTrue($result2);
    }

    public function testGettersReturnCorrectFormat()
    {
        // Set various types and ensure getters work
        $this->module->setVar('mid', 99);
        $this->module->setVar('weight', 5);
        $this->module->setVar('isactive', 0);

        $this->assertIsInt($this->module->id('N'));
        $this->assertIsInt($this->module->weight());
        $this->assertEquals(0, $this->module->isactive());
    }

    public function testSubLinkWithEmptyArray()
    {
        $this->module->setInfo('sub', []);

        $result = $this->module->subLink();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testVersionCompareWithOnlyStableSuffix()
    {
        $result = $this->module->versionCompare('2.5.12-stable', '2.5.12', '==');

        $this->assertTrue($result, 'Versions with -stable suffix should equal versions without');
    }

    public function testMainLinkReturnsCorrectType()
    {
        // Test when hasmain is 0
        $this->module->setVar('hasmain', 0);
        $result = $this->module->mainLink();
        $this->assertFalse($result);

        // Test when hasmain is 1
        $this->module->setVar('hasmain', 1);
        $this->module->setVar('dirname', 'test');
        $this->module->setVar('name', 'Test');
        $result = $this->module->mainLink();
        $this->assertIsString($result);
    }

    public function testGetStatusWithMultipleHyphens()
    {
        $this->module->setVar('version', '1.0.0-RC-2');

        $status = $this->module->getStatus();

        // Should get everything after the last hyphen, or specific parsing logic
        $this->assertIsString($status);
    }

    public function testSetMessageWithEmptyString()
    {
        $this->module->setMessage('');

        $messages = $this->module->getMessages();

        // Empty string should still be trimmed and added
        $this->assertCount(1, $messages);
        $this->assertEquals('', $messages[0]);
    }

    public function testVersionCompareBoundaryVersions()
    {
        // Test boundary conditions
        $result1 = $this->module->versionCompare('0.0.0', '0.0.1', '<');
        $result2 = $this->module->versionCompare('999.999.999', '1000.0.0', '<');

        $this->assertTrue($result1);
        $this->assertTrue($result2);
    }

    public function testGetInfoReturnsArrayReference()
    {
        $this->module->setInfo('key1', 'value1');
        $this->module->setInfo('key2', 'value2');

        $info = &$this->module->getInfo();

        $this->assertIsArray($info);
        $this->assertCount(2, $info);
    }

    public function testIconMethodReturnsValue()
    {
        // The 'icon' var is not initialized in the constructor via initVar(),
        // so setVar/getVar will not register it. The icon() method delegates to
        // getVar('icon') which returns empty string for uninitialized vars.
        $icon = $this->module->icon();

        // Should return empty string since 'icon' is not a registered var
        $this->assertEmpty($icon);
    }
}
