<?php

declare(strict_types=1);

namespace modulesprofile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsTestStubDatabase;

#[CoversClass(\ProfileCategory::class)]
#[CoversClass(\ProfileCategoryHandler::class)]
class ProfileCategoryTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/profile/class/category.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // ProfileCategory tests
    // ---------------------------------------------------------------

    #[Test]
    public function categoryExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\ProfileCategory::class, \XoopsObject::class));
    }

    #[Test]
    public function categoryConstructorInitializesVars(): void
    {
        $cat = new \ProfileCategory();
        $vars = $cat->getVars();
        $this->assertArrayHasKey('cat_id', $vars);
        $this->assertArrayHasKey('cat_title', $vars);
        $this->assertArrayHasKey('cat_description', $vars);
        $this->assertArrayHasKey('cat_weight', $vars);
    }

    #[Test]
    public function categoryHasFourVars(): void
    {
        $cat = new \ProfileCategory();
        $this->assertCount(4, $cat->getVars());
    }

    #[Test]
    public function categoryCatIdIsRequired(): void
    {
        $cat = new \ProfileCategory();
        $vars = $cat->getVars();
        $this->assertTrue($vars['cat_id']['required']);
    }

    #[Test]
    public function categoryCatIdIsIntType(): void
    {
        $cat = new \ProfileCategory();
        $vars = $cat->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['cat_id']['data_type']);
    }

    #[Test]
    public function categoryCatTitleIsTxtboxType(): void
    {
        $cat = new \ProfileCategory();
        $vars = $cat->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['cat_title']['data_type']);
    }

    #[Test]
    public function categoryCatDescriptionIsTxtareaType(): void
    {
        $cat = new \ProfileCategory();
        $vars = $cat->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['cat_description']['data_type']);
    }

    #[Test]
    public function categoryCatWeightIsIntType(): void
    {
        $cat = new \ProfileCategory();
        $vars = $cat->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['cat_weight']['data_type']);
    }

    #[Test]
    public function categorySetAndGetTitle(): void
    {
        $cat = new \ProfileCategory();
        $cat->setVar('cat_title', 'Personal Info');
        $this->assertSame('Personal Info', $cat->getVar('cat_title'));
    }

    #[Test]
    public function categorySetAndGetDescription(): void
    {
        $cat = new \ProfileCategory();
        $cat->setVar('cat_description', 'User personal information');
        $this->assertSame('User personal information', $cat->getVar('cat_description'));
    }

    #[Test]
    public function categorySetAndGetWeight(): void
    {
        $cat = new \ProfileCategory();
        $cat->setVar('cat_weight', 10);
        $this->assertEquals(10, $cat->getVar('cat_weight'));
    }

    // ---------------------------------------------------------------
    // ProfileCategoryHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function categoryHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileCategoryHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function categoryHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileCategoryHandler($db);
        $this->assertStringContainsString('profile_category', $handler->table);
    }

    #[Test]
    public function categoryHandlerSetsKeyName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileCategoryHandler($db);
        $this->assertSame('cat_id', $handler->keyName);
    }

    #[Test]
    public function categoryHandlerSetsIdentifierName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileCategoryHandler($db);
        $this->assertSame('cat_title', $handler->identifierName);
    }

    #[Test]
    public function categoryHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileCategoryHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\ProfileCategory::class, $obj);
        $this->assertTrue($obj->isNew());
    }
}
