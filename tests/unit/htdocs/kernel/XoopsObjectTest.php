<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/object.php';

// Language constants needed by cleanVars
if (!defined('_XOBJ_ERR_REQUIRED')) {
    define('_XOBJ_ERR_REQUIRED', '%s is required');
}
if (!defined('_XOBJ_ERR_SHORTERTHAN')) {
    define('_XOBJ_ERR_SHORTERTHAN', '%s must be shorter than %d characters.');
}

/**
 * Concrete subclass of XoopsObject for testing purposes.
 *
 * XoopsObject requires initVar() calls to have meaningful vars;
 * this subclass provides a predictable set for tests.
 */
class ConcreteTestObject extends \XoopsObject
{
    // PHP 8.2 dynamic properties compatibility
    public $item_id;
    public $name;
    public $description;

    public function __construct()
    {
        parent::__construct();
        $this->initVar('item_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false);
    }
}

/**
 * Comprehensive unit tests for XoopsObject base class.
 *
 * Tests the core data-object functionality: variable initialization, get/set,
 * dirty/new flag management, error handling, cloning, cleanVars validation,
 * and type safety.
 */
class XoopsObjectTest extends KernelTestCase
{
    /** @var \XoopsObject */
    private $object;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
        $this->object = new \XoopsObject();
    }

    /**
     * Helper: create a ConcreteTestObject with predefined vars.
     *
     * @return ConcreteTestObject
     */
    private function createTestObject(): ConcreteTestObject
    {
        return new ConcreteTestObject();
    }

    // =========================================================================
    // Constructor / Default State
    // =========================================================================

    public function testConstructorSetsDefaultState(): void
    {
        $obj = new \XoopsObject();
        $this->assertFalse($obj->isNew(), 'New XoopsObject should NOT be marked as new');
        $this->assertFalse($obj->isDirty(), 'New XoopsObject should NOT be marked as dirty');
        $this->assertSame([], $obj->getErrors(), 'New XoopsObject should have no errors');
        $this->assertSame([], $obj->vars, 'New XoopsObject should have empty vars');
        $this->assertSame([], $obj->cleanVars, 'New XoopsObject should have empty cleanVars');
    }

    // =========================================================================
    // setNew / unsetNew / isNew
    // =========================================================================

    public function testSetNewAndUnsetNew(): void
    {
        $this->object->setNew();
        $this->assertTrue($this->object->isNew());

        $this->object->unsetNew();
        $this->assertFalse($this->object->isNew());
    }

    public function testIsNewOnFreshObject(): void
    {
        $obj = new \XoopsObject();
        $this->assertFalse($obj->isNew());
    }

    public function testSetNewCanBeCalledMultipleTimes(): void
    {
        $this->object->setNew();
        $this->object->setNew();
        $this->assertTrue($this->object->isNew());
    }

    // =========================================================================
    // setDirty / unsetDirty / isDirty
    // =========================================================================

    public function testSetDirtyAndUnsetDirty(): void
    {
        $this->assertFalse($this->object->isDirty());

        $this->object->setDirty();
        $this->assertTrue($this->object->isDirty());

        $this->object->unsetDirty();
        $this->assertFalse($this->object->isDirty());
    }

    // =========================================================================
    // initVar
    // =========================================================================

    public function testInitVarCreatesVariable(): void
    {
        $this->object->initVar('title', XOBJ_DTYPE_TXTBOX, null, true, 255);
        $this->assertArrayHasKey('title', $this->object->vars);
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $this->object->vars['title']['data_type']);
        $this->assertTrue($this->object->vars['title']['required']);
        $this->assertSame(255, $this->object->vars['title']['maxlength']);
        $this->assertFalse($this->object->vars['title']['changed']);
    }

    public function testInitVarSetsDefaultValue(): void
    {
        $this->object->initVar('status', XOBJ_DTYPE_OTHER, 'active', false);
        $this->assertSame('active', $this->object->vars['status']['value']);
    }

    public function testInitVarWithNullValue(): void
    {
        $this->object->initVar('nullable', XOBJ_DTYPE_INT, null, false);
        $this->assertNull($this->object->vars['nullable']['value']);
    }

    public function testInitVarSetsOptions(): void
    {
        $this->object->initVar('choice', XOBJ_DTYPE_OTHER, null, false, null, 'a|b|c');
        $this->assertSame('a|b|c', $this->object->vars['choice']['options']);
    }

    // =========================================================================
    // setVar
    // =========================================================================

    public function testSetVarAndGetVarRawFormat(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', 'Test Name');
        $this->assertSame('Test Name', $obj->getVar('name', 'n'));
    }

    public function testSetVarAndGetVarEditFormat(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', '<script>alert("xss")</script>');
        $edit = $obj->getVar('name', 'e');
        $this->assertStringNotContainsString('<script>', $edit);
        $this->assertStringContainsString('&lt;script&gt;', $edit);
    }

    public function testSetVarAndGetVarShowFormat(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', '<b>Bold</b>');
        $show = $obj->getVar('name', 's');
        $this->assertStringNotContainsString('<b>', $show);
    }

    public function testSetVarMarksDirty(): void
    {
        $obj = $this->createTestObject();
        $this->assertFalse($obj->isDirty());
        $obj->setVar('name', 'Changed');
        $this->assertTrue($obj->isDirty());
        $this->assertTrue($obj->vars['name']['changed']);
    }

    public function testSetVarSetsNotGpcFlag(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', 'Safe Value', true);
        $this->assertTrue($obj->vars['name']['not_gpc']);
    }

    public function testSetVarIgnoresNonExistentKey(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('nonexistent', 'value');
        $this->assertArrayNotHasKey('nonexistent', $obj->vars);
        $this->assertFalse($obj->isDirty());
    }

    public function testSetVarIgnoresEmptyKey(): void
    {
        $this->object->setVar('', 'value');
        $this->assertFalse($this->object->isDirty());
    }

    // =========================================================================
    // setVars (batch)
    // =========================================================================

    public function testSetVarsMultiple(): void
    {
        $obj = $this->createTestObject();
        $obj->setVars([
            'item_id' => 10,
            'name'    => 'Batch Set',
        ]);
        $this->assertSame(10, $obj->getVar('item_id', 'n'));
        $this->assertSame('Batch Set', $obj->getVar('name', 'n'));
        $this->assertTrue($obj->isDirty());
    }

    public function testSetVarsWithNotGpcFlag(): void
    {
        $obj = $this->createTestObject();
        $obj->setVars(['name' => 'Safe'], true);
        $this->assertTrue($obj->vars['name']['not_gpc']);
    }

    public function testSetVarsIgnoresNonArrayInput(): void
    {
        $this->object->initVar('name', XOBJ_DTYPE_TXTBOX, null, false, 100);
        $this->object->setVars('invalid');
        $this->assertFalse($this->object->isDirty());
    }

    // =========================================================================
    // assignVar
    // =========================================================================

    public function testAssignVarDoesNotMarkDirty(): void
    {
        $obj = $this->createTestObject();
        $this->assertFalse($obj->isDirty());
        $obj->assignVar('name', 'Assigned');
        $this->assertFalse($obj->isDirty(), 'assignVar should NOT mark object as dirty');
        $this->assertSame('Assigned', $obj->getVar('name', 'n'));
    }

    public function testAssignVarDoesNotSetChangedFlag(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVar('name', 'Assigned');
        $this->assertFalse($obj->vars['name']['changed']);
    }

    public function testAssignVarIgnoresNonExistentKey(): void
    {
        $this->object->assignVar('nonexistent', 'value');
        $this->assertArrayNotHasKey('nonexistent', $this->object->vars);
    }

    // =========================================================================
    // assignVars (batch)
    // =========================================================================

    public function testAssignVars(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVars([
            'item_id' => 99,
            'name'    => 'Bulk Assign',
        ]);
        $this->assertSame(99, $obj->getVar('item_id', 'n'));
        $this->assertSame('Bulk Assign', $obj->getVar('name', 'n'));
        $this->assertFalse($obj->isDirty());
    }

    public function testAssignVarsIgnoresNonArrayInput(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVars('not an array');
        $this->assertNull($obj->getVar('name', 'n'));
    }

    public function testAssignVarsIgnoresUnknownKeys(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVars([
            'name'        => 'Valid',
            'unknown_key' => 'Ignored',
        ]);
        $this->assertSame('Valid', $obj->getVar('name', 'n'));
        $this->assertNull($obj->getVar('unknown_key', 'n'));
    }

    // =========================================================================
    // getVars / getValues
    // =========================================================================

    public function testGetVarsReturnsAllVariables(): void
    {
        $obj = $this->createTestObject();
        $vars = $obj->getVars();
        $this->assertIsArray($vars);
        $this->assertArrayHasKey('item_id', $vars);
        $this->assertArrayHasKey('name', $vars);
        $this->assertArrayHasKey('description', $vars);
    }

    public function testGetVarsReturnsByReference(): void
    {
        $this->object->initVar('x', XOBJ_DTYPE_INT, 5, false);
        $vars = &$this->object->getVars();
        $this->assertSame($this->object->vars, $vars);
    }

    public function testGetValuesReturnsSubset(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('item_id', 5);
        $obj->setVar('name', 'Test');

        $values = $obj->getValues(['item_id', 'name'], 'n');
        $this->assertArrayHasKey('item_id', $values);
        $this->assertArrayHasKey('name', $values);
        $this->assertArrayNotHasKey('description', $values);
        $this->assertSame(5, $values['item_id']);
        $this->assertSame('Test', $values['name']);
    }

    public function testGetValuesReturnsAllWhenKeysNull(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVar('item_id', 1);
        $obj->assignVar('name', 'Test');

        $values = $obj->getValues(null, 'n');
        $this->assertArrayHasKey('item_id', $values);
        $this->assertArrayHasKey('name', $values);
        $this->assertArrayHasKey('description', $values);
    }

    public function testGetValuesIgnoresNonExistentKeys(): void
    {
        $obj = $this->createTestObject();
        $values = $obj->getValues(['nonexistent'], 'n');
        $this->assertArrayNotHasKey('nonexistent', $values);
        $this->assertSame([], $values);
    }

    // =========================================================================
    // destroyVars
    // =========================================================================

    public function testDestroyVarsRemovesVariable(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', 'Test');

        $result = $obj->destroyVars('name');
        $this->assertTrue($result);
        // The variable still exists but 'changed' is set to null
        $this->assertNull($obj->vars['name']['changed']);
    }

    public function testDestroyVarsAcceptsArray(): void
    {
        $obj = $this->createTestObject();
        $result = $obj->destroyVars(['name', 'description']);
        $this->assertTrue($result);
        $this->assertNull($obj->vars['name']['changed']);
        $this->assertNull($obj->vars['description']['changed']);
    }

    public function testDestroyVarsReturnsTrueForEmpty(): void
    {
        $this->assertTrue($this->object->destroyVars(''));
    }

    public function testDestroyVarsIgnoresUnknownKeys(): void
    {
        $obj = $this->createTestObject();
        $result = $obj->destroyVars('nonexistent');
        $this->assertTrue($result);
    }

    // =========================================================================
    // cleanVars
    // =========================================================================

    public function testCleanVarsWithValidData(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('item_id', 42);
        $obj->setVar('name', 'Valid Name');

        $result = $obj->cleanVars();
        $this->assertTrue($result);
        $this->assertArrayHasKey('item_id', $obj->cleanVars);
        $this->assertArrayHasKey('name', $obj->cleanVars);
    }

    public function testCleanVarsUnsetsDirtyOnSuccess(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', 'Test');
        $this->assertTrue($obj->isDirty());

        $obj->cleanVars();
        $this->assertFalse($obj->isDirty(), 'cleanVars should unset dirty flag on success');
    }

    public function testCleanVarsFailsOnRequiredEmpty(): void
    {
        $obj = $this->createTestObject();
        // 'name' is required -- set it to empty string via setVar
        $obj->setVar('name', '');

        $result = $obj->cleanVars();
        $this->assertFalse($result);
        $errors = $obj->getErrors();
        $this->assertNotEmpty($errors);
    }

    public function testCleanVarsRequiredFieldAllowsZeroString(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', '0');
        $result = $obj->cleanVars();
        $this->assertTrue($result);
    }

    public function testCleanVarsCastsIntegerType(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('item_id', '99');

        $result = $obj->cleanVars();
        $this->assertTrue($result);
        // cleanVars() casts INT to int, then converts to string via str_replace at the end
        $this->assertSame('99', $obj->cleanVars['item_id']);
    }

    public function testCleanVarsSkipsUnchangedVariables(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', 'Test');

        $result = $obj->cleanVars();
        $this->assertTrue($result);
        // item_id was not changed, but still appears in cleanVars with original value
        $this->assertArrayHasKey('item_id', $obj->cleanVars);
    }

    public function testCleanVarsRespectsMaxlength(): void
    {
        $obj = new \XoopsObject();
        $obj->initVar('short', XOBJ_DTYPE_TXTBOX, null, false, 5);
        $obj->setVar('short', 'This is way too long for the field');

        $result = $obj->cleanVars();
        $this->assertFalse($result);
        $this->assertNotEmpty($obj->getErrors());
    }

    public function testCleanVarsPreservesExistingErrors(): void
    {
        $obj = $this->createTestObject();
        $obj->setErrors('Previous error');
        $obj->setVar('name', 'Valid');
        $obj->cleanVars();
        $errors = $obj->getErrors();
        $this->assertContains('Previous error', $errors);
    }

    // =========================================================================
    // setErrors / getErrors / getHtmlErrors
    // =========================================================================

    public function testSetErrorsAndGetErrors(): void
    {
        $this->object->setErrors('Error 1');
        $this->object->setErrors('Error 2');

        $errors = $this->object->getErrors();
        $this->assertCount(2, $errors);
        $this->assertSame('Error 1', $errors[0]);
        $this->assertSame('Error 2', $errors[1]);
    }

    public function testSetErrorsWithArray(): void
    {
        $this->object->setErrors(['Error A', 'Error B']);
        $errors = $this->object->getErrors();
        $this->assertCount(2, $errors);
    }

    public function testGetErrorsReturnsEmptyArrayByDefault(): void
    {
        $this->assertSame([], $this->object->getErrors());
    }

    public function testSetErrorsTrimsStringErrors(): void
    {
        $this->object->setErrors('  trimmed  ');
        $errors = $this->object->getErrors();
        $this->assertSame('trimmed', $errors[0]);
    }

    public function testGetHtmlErrors(): void
    {
        $this->object->setErrors('First error');
        $this->object->setErrors('Second error');

        $html = $this->object->getHtmlErrors();
        $this->assertStringContainsString('<h4>Errors</h4>', $html);
        $this->assertStringContainsString('First error', $html);
        $this->assertStringContainsString('Second error', $html);
        $this->assertStringContainsString('<br>', $html);
    }

    public function testGetHtmlErrorsWithNoErrors(): void
    {
        $html = $this->object->getHtmlErrors();
        $this->assertStringContainsString('None<br>', $html);
    }

    // =========================================================================
    // xoopsClone
    // =========================================================================

    public function testXoopsClone(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVar('item_id', 100);
        $obj->assignVar('name', 'Original');
        $obj->unsetNew();

        $clone = $obj->xoopsClone();

        $this->assertInstanceOf(ConcreteTestObject::class, $clone);
        $this->assertTrue($clone->isNew(), 'Clone should be marked as new');
        $this->assertSame(100, $clone->getVar('item_id', 'n'));
        $this->assertSame('Original', $clone->getVar('name', 'n'));
    }

    public function testXoopsCloneIsIndependent(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVar('name', 'Original');

        $clone = $obj->xoopsClone();
        $clone->setVar('name', 'Modified');

        // Original should remain unchanged
        $this->assertSame('Original', $obj->getVar('name', 'n'));
        $this->assertSame('Modified', $clone->getVar('name', 'n'));
    }

    public function testPhpCloneSetsNew(): void
    {
        $obj = $this->createTestObject();
        $obj->unsetNew();

        $clone = clone $obj;
        $this->assertTrue($clone->isNew(), '__clone should mark the clone as new');
    }

    // =========================================================================
    // getVar additional formats and types
    // =========================================================================

    public function testGetVarWithIntType(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVar('item_id', '42');
        $value = $obj->getVar('item_id', 'n');
        $this->assertSame(42, $value);
    }

    public function testGetVarIntReturnsEmptyStringForNull(): void
    {
        $this->object->initVar('count', XOBJ_DTYPE_INT, null, false);
        $result = $this->object->getVar('count', 'n');
        $this->assertSame('', $result);
    }

    public function testGetVarReturnsNullForNonExistentKey(): void
    {
        $this->assertNull($this->object->getVar('nonexistent'));
    }

    public function testGetVarDefaultFormatIsShow(): void
    {
        $obj = $this->createTestObject();
        $obj->setVar('name', '<em>test</em>');

        $defaultVal = $obj->getVar('name');
        $showVal = $obj->getVar('name', 's');
        $this->assertSame($showVal, $defaultVal);
    }

    public function testGetVarOtherTypeReturnsRawByDefault(): void
    {
        $this->object->initVar('custom', XOBJ_DTYPE_OTHER, 'raw_value', false);
        $this->assertSame('raw_value', $this->object->getVar('custom', 'n'));
    }

    public function testGetVarSourceTypeEditEscapes(): void
    {
        $this->object->initVar('code', XOBJ_DTYPE_SOURCE, null, false);
        $this->object->assignVar('code', '<script>alert(1)</script>');
        $result = $this->object->getVar('code', 'e');
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    // =========================================================================
    // toArray
    // =========================================================================

    public function testToArrayCallsGetValues(): void
    {
        $obj = $this->createTestObject();
        $obj->assignVar('item_id', 5);
        $array = $obj->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('item_id', $array);
    }

    // =========================================================================
    // Edge cases and type safety
    // =========================================================================

    public function testSetVarWithNullValueIsIgnored(): void
    {
        $obj = $this->createTestObject();
        $beforeDirty = $obj->isDirty();
        $obj->setVar('name', null);
        // setVar checks isset($value) -- null fails that check
        $this->assertSame($beforeDirty, $obj->isDirty());
    }

    public function testMultipleErrorAccumulation(): void
    {
        $this->object->setErrors('Error 1');
        $this->object->setErrors(['Error 2', 'Error 3']);
        $this->object->setErrors('Error 4');

        $errors = $this->object->getErrors();
        $this->assertCount(4, $errors);
    }

    public function testOverwriteInitVarReplacesDefinition(): void
    {
        $this->object->initVar('field', XOBJ_DTYPE_INT, 1, false);
        $this->object->initVar('field', XOBJ_DTYPE_TXTBOX, 'new', true, 50);
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $this->object->vars['field']['data_type']);
        $this->assertSame('new', $this->object->vars['field']['value']);
        $this->assertTrue($this->object->vars['field']['required']);
    }
}
