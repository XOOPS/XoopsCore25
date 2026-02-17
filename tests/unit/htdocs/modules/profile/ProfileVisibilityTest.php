<?php

declare(strict_types=1);

namespace modulesprofile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsTestStubDatabase;

#[CoversClass(\ProfileVisibility::class)]
#[CoversClass(\ProfileVisibilityHandler::class)]
class ProfileVisibilityTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/profile/class/visibility.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // ProfileVisibility tests
    // ---------------------------------------------------------------

    #[Test]
    public function visibilityExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\ProfileVisibility::class, \XoopsObject::class));
    }

    #[Test]
    public function visibilityConstructorInitializesVars(): void
    {
        $vis = new \ProfileVisibility();
        $vars = $vis->getVars();
        $this->assertArrayHasKey('field_id', $vars);
        $this->assertArrayHasKey('user_group', $vars);
        $this->assertArrayHasKey('profile_group', $vars);
    }

    #[Test]
    public function visibilityHasThreeVars(): void
    {
        $vis = new \ProfileVisibility();
        $this->assertCount(3, $vis->getVars());
    }

    #[Test]
    public function visibilityAllVarsAreIntType(): void
    {
        $vis = new \ProfileVisibility();
        $vars = $vis->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['field_id']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['user_group']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['profile_group']['data_type']);
    }

    #[Test]
    public function visibilitySetAndGetFieldId(): void
    {
        $vis = new \ProfileVisibility();
        $vis->setVar('field_id', 42);
        $this->assertEquals(42, $vis->getVar('field_id'));
    }

    #[Test]
    public function visibilitySetAndGetUserGroup(): void
    {
        $vis = new \ProfileVisibility();
        $vis->setVar('user_group', 1);
        $this->assertEquals(1, $vis->getVar('user_group'));
    }

    #[Test]
    public function visibilitySetAndGetProfileGroup(): void
    {
        $vis = new \ProfileVisibility();
        $vis->setVar('profile_group', 2);
        $this->assertEquals(2, $vis->getVar('profile_group'));
    }

    // ---------------------------------------------------------------
    // ProfileVisibilityHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function visibilityHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function visibilityHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $this->assertStringContainsString('profile_visibility', $handler->table);
    }

    #[Test]
    public function visibilityHandlerSetsKeyName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $this->assertSame('field_id', $handler->keyName);
    }

    #[Test]
    public function visibilityHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\ProfileVisibility::class, $obj);
        $this->assertTrue($obj->isNew());
    }

    // ---------------------------------------------------------------
    // visibilitySort tests (protected method via reflection)
    // ---------------------------------------------------------------

    #[Test]
    public function visibilitySortByFieldId(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $method = new \ReflectionMethod($handler, 'visibilitySort');
        $method->setAccessible(true);

        $a = ['field_id' => 1, 'user_group' => 1, 'profile_group' => 1];
        $b = ['field_id' => 2, 'user_group' => 1, 'profile_group' => 1];
        $this->assertLessThan(0, $method->invoke($handler, $a, $b));
    }

    #[Test]
    public function visibilitySortByUserGroupWhenFieldIdEqual(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $method = new \ReflectionMethod($handler, 'visibilitySort');
        $method->setAccessible(true);

        $a = ['field_id' => 1, 'user_group' => 1, 'profile_group' => 1];
        $b = ['field_id' => 1, 'user_group' => 3, 'profile_group' => 1];
        $this->assertLessThan(0, $method->invoke($handler, $a, $b));
    }

    #[Test]
    public function visibilitySortByProfileGroupWhenOthersEqual(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $method = new \ReflectionMethod($handler, 'visibilitySort');
        $method->setAccessible(true);

        $a = ['field_id' => 1, 'user_group' => 1, 'profile_group' => 2];
        $b = ['field_id' => 1, 'user_group' => 1, 'profile_group' => 5];
        $this->assertLessThan(0, $method->invoke($handler, $a, $b));
    }

    #[Test]
    public function visibilitySortReturnsZeroForEqual(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $method = new \ReflectionMethod($handler, 'visibilitySort');
        $method->setAccessible(true);

        $a = ['field_id' => 1, 'user_group' => 2, 'profile_group' => 3];
        $b = ['field_id' => 1, 'user_group' => 2, 'profile_group' => 3];
        $this->assertSame(0, $method->invoke($handler, $a, $b));
    }

    #[Test]
    public function visibilitySortReturnsPositiveForGreater(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileVisibilityHandler($db);
        $method = new \ReflectionMethod($handler, 'visibilitySort');
        $method->setAccessible(true);

        $a = ['field_id' => 5, 'user_group' => 1, 'profile_group' => 1];
        $b = ['field_id' => 2, 'user_group' => 1, 'profile_group' => 1];
        $this->assertGreaterThan(0, $method->invoke($handler, $a, $b));
    }
}
