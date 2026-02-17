<?php

declare(strict_types=1);

namespace modulesprofile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsTestStubDatabase;

#[CoversClass(\ProfileField::class)]
#[CoversClass(\ProfileFieldHandler::class)]
class ProfileFieldTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/profile/class/field.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // ProfileField constructor / var tests
    // ---------------------------------------------------------------

    #[Test]
    public function fieldExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\ProfileField::class, \XoopsObject::class));
    }

    #[Test]
    public function fieldConstructorInitializesAllVars(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $expected = [
            'field_id', 'cat_id', 'field_type', 'field_valuetype',
            'field_name', 'field_title', 'field_description',
            'field_required', 'field_maxlength', 'field_weight',
            'field_default', 'field_notnull', 'field_edit',
            'field_show', 'field_config', 'field_options', 'step_id',
        ];
        foreach ($expected as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: $varName");
        }
    }

    #[Test]
    public function fieldHasSeventeenVars(): void
    {
        $field = new \ProfileField();
        $this->assertCount(17, $field->getVars());
    }

    #[Test]
    public function fieldCatIdIsRequired(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $this->assertTrue($vars['cat_id']['required']);
    }

    #[Test]
    public function fieldNameIsRequired(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $this->assertTrue($vars['field_name']['required']);
    }

    #[Test]
    public function fieldValuetypeIsRequired(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $this->assertTrue($vars['field_valuetype']['required']);
    }

    #[Test]
    public function fieldRequiredDefaultsToZero(): void
    {
        $field = new \ProfileField();
        $this->assertEquals(0, $field->getVar('field_required'));
    }

    #[Test]
    public function fieldMaxlengthDefaultsToZero(): void
    {
        $field = new \ProfileField();
        $this->assertEquals(0, $field->getVar('field_maxlength'));
    }

    #[Test]
    public function fieldWeightDefaultsToZero(): void
    {
        $field = new \ProfileField();
        $this->assertEquals(0, $field->getVar('field_weight'));
    }

    #[Test]
    public function fieldNotnullDefaultsToOne(): void
    {
        $field = new \ProfileField();
        $this->assertEquals(1, $field->getVar('field_notnull'));
    }

    #[Test]
    public function fieldDefaultDefaultsToEmptyString(): void
    {
        $field = new \ProfileField();
        $this->assertSame('', $field->getVar('field_default'));
    }

    #[Test]
    public function fieldOptionsDefaultsToEmptyArray(): void
    {
        $field = new \ProfileField();
        $this->assertSame([], $field->getVar('field_options'));
    }

    #[Test]
    public function fieldStepIdDefaultsToZero(): void
    {
        $field = new \ProfileField();
        $this->assertEquals(0, $field->getVar('step_id'));
    }

    // ---------------------------------------------------------------
    // Data type checks
    // ---------------------------------------------------------------

    #[Test]
    public function fieldIdIsIntType(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['field_id']['data_type']);
    }

    #[Test]
    public function fieldTypeIsTxtboxType(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['field_type']['data_type']);
    }

    #[Test]
    public function fieldDescriptionIsTxtareaType(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['field_description']['data_type']);
    }

    #[Test]
    public function fieldOptionsIsArrayType(): void
    {
        $field = new \ProfileField();
        $vars = $field->getVars();
        $this->assertSame(XOBJ_DTYPE_ARRAY, $vars['field_options']['data_type']);
    }

    // ---------------------------------------------------------------
    // setVar / getVar base64 encoding for field_options
    // ---------------------------------------------------------------

    #[Test]
    public function setVarEncodesFieldOptionsValues(): void
    {
        $field = new \ProfileField();
        $options = ['key1' => 'Value One', 'key2' => 'Value Two'];
        $field->setVar('field_options', $options);

        // Internally stored as base64-encoded
        $raw = parent::class; // Access internal vars via reflection
        $vars = $field->getVars();
        // The raw value should be base64 encoded
        $rawValue = $vars['field_options']['value'];
        if (is_array($rawValue)) {
            foreach ($rawValue as $v) {
                // Values should be base64 encoded strings
                $this->assertNotFalse(base64_decode($v, true));
            }
        }
    }

    #[Test]
    public function getVarDecodesFieldOptionsValues(): void
    {
        $field = new \ProfileField();
        $options = ['opt1' => 'Hello', 'opt2' => 'World'];
        $field->setVar('field_options', $options);

        $result = $field->getVar('field_options');
        $this->assertIsArray($result);
        $this->assertSame('Hello', $result['opt1']);
        $this->assertSame('World', $result['opt2']);
    }

    #[Test]
    public function setVarNonOptionsPassesThrough(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_name', 'test_field');
        $this->assertSame('test_field', $field->getVar('field_name'));
    }

    #[Test]
    public function getVarFieldOptionsEmptyArray(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_options', []);
        $this->assertSame([], $field->getVar('field_options'));
    }

    #[Test]
    public function fieldOptionsRoundTripWithSpecialChars(): void
    {
        $field = new \ProfileField();
        $options = ['k' => 'Héllo Wörld <script>'];
        $field->setVar('field_options', $options);

        $result = $field->getVar('field_options');
        $this->assertSame('Héllo Wörld <script>', $result['k']);
    }

    // ---------------------------------------------------------------
    // getValueForSave tests
    // ---------------------------------------------------------------

    #[Test]
    public function getValueForSaveTextboxReturnsValue(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'textbox');
        $this->assertSame('hello', $field->getValueForSave('hello'));
    }

    #[Test]
    public function getValueForSaveTextareaReturnsValue(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'textarea');
        $this->assertSame('some text', $field->getValueForSave('some text'));
    }

    #[Test]
    public function getValueForSaveCheckboxReturnsArray(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'checkbox');
        $result = $field->getValueForSave('single_value');
        $this->assertIsArray($result);
    }

    #[Test]
    public function getValueForSaveCheckboxPreservesArray(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'checkbox');
        $result = $field->getValueForSave(['a', 'b']);
        $this->assertSame(['a', 'b'], $result);
    }

    #[Test]
    public function getValueForSaveDateConvertsToTimestamp(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'date');
        $result = $field->getValueForSave('2025-01-15');
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    #[Test]
    public function getValueForSaveDateEmptyReturnsEmpty(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'date');
        $result = $field->getValueForSave('');
        $this->assertSame('', $result);
    }

    #[Test]
    public function getValueForSaveDatetimeConvertsToTimestamp(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'datetime');
        $result = $field->getValueForSave(['date' => '2025-01-15', 'time' => 3600]);
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }

    #[Test]
    public function getValueForSaveDatetimeEmptyReturnsValue(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'datetime');
        $result = $field->getValueForSave('');
        $this->assertSame('', $result);
    }

    #[Test]
    public function getValueForSaveSelectReturnsValue(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'select');
        $this->assertSame('option1', $field->getValueForSave('option1'));
    }

    #[Test]
    public function getValueForSaveYesnoReturnsValue(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'yesno');
        $this->assertSame(1, $field->getValueForSave(1));
    }

    #[Test]
    public function getValueForSaveLongdateReturnsValue(): void
    {
        $field = new \ProfileField();
        $field->setVar('field_type', 'longdate');
        $this->assertSame('2025-01-15', $field->getValueForSave('2025-01-15'));
    }

    // ---------------------------------------------------------------
    // ProfileFieldHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function fieldHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileFieldHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function fieldHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileFieldHandler($db);
        $this->assertStringContainsString('profile_field', $handler->table);
    }

    #[Test]
    public function fieldHandlerSetsKeyName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileFieldHandler($db);
        $this->assertSame('field_id', $handler->keyName);
    }

    #[Test]
    public function fieldHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileFieldHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\ProfileField::class, $obj);
        $this->assertTrue($obj->isNew());
    }

    #[Test]
    public function fieldHandlerGetUserVarsReturnsArray(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileFieldHandler($db);
        $vars = $handler->getUserVars();
        $this->assertIsArray($vars);
        $this->assertContains('uid', $vars);
        $this->assertContains('uname', $vars);
        $this->assertContains('email', $vars);
        $this->assertContains('name', $vars);
        $this->assertContains('url', $vars);
    }

    #[Test]
    public function fieldHandlerGetUserVarsContainsExpectedFields(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileFieldHandler($db);
        $vars = $handler->getUserVars();

        $expected = [
            'uid', 'uname', 'name', 'email', 'url', 'user_avatar',
            'user_regdate', 'user_icq', 'user_from', 'user_sig',
            'user_viewemail', 'actkey', 'user_aim', 'user_yim',
            'user_msnm', 'pass', 'posts', 'attachsig', 'rank',
            'level', 'theme', 'timezone_offset', 'last_login',
            'umode', 'uorder', 'notify_method', 'notify_mode',
            'user_occ', 'bio', 'user_intrest', 'user_mailok',
        ];
        foreach ($expected as $field) {
            $this->assertContains($field, $vars, "Missing user var: $field");
        }
    }

    #[Test]
    public function fieldHandlerGetUserVarsHas31Entries(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileFieldHandler($db);
        $this->assertCount(31, $handler->getUserVars());
    }
}
