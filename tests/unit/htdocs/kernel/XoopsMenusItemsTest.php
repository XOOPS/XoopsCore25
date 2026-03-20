<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/menusitems.php';

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use XoopsMenusItems;
use XoopsMenusItemsHandler;
use XoopsObject;

#[CoversClass(XoopsMenusItems::class)]
#[CoversClass(XoopsMenusItemsHandler::class)]
class XoopsMenusItemsTest extends KernelTestCase
{
    #[Test]
    public function constructorCreatesInstance(): void
    {
        $item = new XoopsMenusItems();
        $this->assertInstanceOf(XoopsMenusItems::class, $item);
        $this->assertInstanceOf(XoopsObject::class, $item);
    }

    #[Test]
    public function constructorInitializesAllVars(): void
    {
        $item = new XoopsMenusItems();
        $expected = [
            'items_id', 'items_pid', 'items_cid',
            'items_title', 'items_prefix', 'items_suffix',
            'items_url', 'items_target', 'items_position',
            'items_protected', 'items_active',
        ];
        $vars = $item->getVars();
        foreach ($expected as $name) {
            $this->assertArrayHasKey($name, $vars, "Missing var: {$name}");
        }
        $this->assertCount(11, $vars);
    }

    #[Test]
    public function constructorSetsCorrectDataTypes(): void
    {
        $item = new XoopsMenusItems();
        $vars = $item->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['items_id']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['items_pid']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['items_cid']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['items_title']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['items_prefix']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['items_suffix']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['items_url']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['items_target']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['items_position']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['items_protected']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['items_active']['data_type']);
    }

    #[Test]
    public function resolvedTitleReturnsConstantValue(): void
    {
        if (!defined('MENUS_ACCOUNT_LOGIN')) {
            define('MENUS_ACCOUNT_LOGIN', 'Login');
        }
        $item = new XoopsMenusItems();
        $item->setVar('items_title', 'MENUS_ACCOUNT_LOGIN');
        $this->assertSame('Login', $item->getResolvedTitle());
    }

    #[Test]
    public function resolvedTitleReturnsRawWhenNotConstant(): void
    {
        $item = new XoopsMenusItems();
        $item->setVar('items_title', 'Custom Link');
        $this->assertSame('Custom Link', $item->getResolvedTitle());
    }

    #[Test]
    public function handlerBindsCorrectTable(): void
    {
        $db = $this->createMockDatabase();
        $handler = new XoopsMenusItemsHandler($db);
        $this->assertInstanceOf(XoopsMenusItemsHandler::class, $handler);
    }
}
