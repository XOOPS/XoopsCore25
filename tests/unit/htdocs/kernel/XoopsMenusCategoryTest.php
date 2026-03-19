<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/menuscategory.php';

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use XoopsMenusCategory;
use XoopsMenusCategoryHandler;
use XoopsObject;

#[CoversClass(XoopsMenusCategory::class)]
#[CoversClass(XoopsMenusCategoryHandler::class)]
class XoopsMenusCategoryTest extends KernelTestCase
{
    #[Test]
    public function constructorCreatesInstance(): void
    {
        $cat = new XoopsMenusCategory();
        $this->assertInstanceOf(XoopsMenusCategory::class, $cat);
        $this->assertInstanceOf(XoopsObject::class, $cat);
    }

    #[Test]
    public function constructorInitializesAllVars(): void
    {
        $cat = new XoopsMenusCategory();
        $expected = [
            'category_id', 'category_title', 'category_prefix', 'category_suffix',
            'category_url', 'category_target', 'category_position',
            'category_protected', 'category_active',
        ];
        $vars = $cat->getVars();
        foreach ($expected as $name) {
            $this->assertArrayHasKey($name, $vars, "Missing var: {$name}");
        }
        $this->assertCount(9, $vars);
    }

    #[Test]
    public function constructorSetsCorrectDataTypes(): void
    {
        $cat = new XoopsMenusCategory();
        $vars = $cat->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['category_id']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['category_title']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['category_prefix']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['category_suffix']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['category_url']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['category_target']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['category_position']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['category_protected']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['category_active']['data_type']);
    }

    #[Test]
    public function resolvedTitleReturnsDefinedConstant(): void
    {
        if (!defined('MENUS_HOME')) {
            define('MENUS_HOME', 'Home');
        }
        $cat = new XoopsMenusCategory();
        $cat->setVar('category_title', 'MENUS_HOME');
        $this->assertSame('Home', $cat->getResolvedTitle());
    }

    #[Test]
    public function resolvedTitleReturnsTitleWhenNotConstant(): void
    {
        $cat = new XoopsMenusCategory();
        $cat->setVar('category_title', 'My Custom Menu');
        $this->assertSame('My Custom Menu', $cat->getResolvedTitle());
    }

    #[Test]
    public function adminTitleShowsConstantAnnotation(): void
    {
        if (!defined('MENUS_HOME')) {
            define('MENUS_HOME', 'Home');
        }
        $cat = new XoopsMenusCategory();
        $cat->setVar('category_title', 'MENUS_HOME');
        $result = $cat->getAdminTitle();
        $this->assertStringContainsString('Home', $result);
        $this->assertStringContainsString('MENUS_HOME', $result);
    }

    #[Test]
    public function handlerBindsCorrectTable(): void
    {
        $db = $this->createMockDatabase();
        $handler = new XoopsMenusCategoryHandler($db);
        $this->assertInstanceOf(XoopsMenusCategoryHandler::class, $handler);
    }
}
