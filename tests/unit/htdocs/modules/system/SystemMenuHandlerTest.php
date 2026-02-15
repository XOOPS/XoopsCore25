<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\SystemMenuHandler::class)]
class SystemMenuHandlerTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/system/class/menu.php';
            self::$loaded = true;
        }
    }

    private function createMockModule(): object
    {
        return new class {
            public function getVar(string $key)
            {
                if ($key === 'dirname') return 'system';
                if ($key === 'name') return 'System';
                if ($key === 'mid') return 1;
                return '';
            }
            public function getInfo(string $key)
            {
                if ($key === 'image') return 'system.png';
                return '';
            }
        };
    }

    protected function setUp(): void
    {
        $GLOBALS['xoopsModule'] = $this->createMockModule();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['xoopsModule']);
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorSetsObjFromGlobal(): void
    {
        $menu = new \SystemMenuHandler();
        $this->assertNotNull($menu->_obj);
    }

    #[Test]
    public function constructorInitializesEmptyArrays(): void
    {
        $menu = new \SystemMenuHandler();
        $this->assertIsArray($menu->_menutop);
        $this->assertEmpty($menu->_menutop);
        $this->assertIsArray($menu->_menutabs);
        $this->assertEmpty($menu->_menutabs);
    }

    // ---------------------------------------------------------------
    // addMenuTop tests
    // ---------------------------------------------------------------

    #[Test]
    public function addMenuTopWithName(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTop('admin/index.php', 'Dashboard');
        $this->assertSame('Dashboard', $menu->_menutop['admin/index.php']);
    }

    #[Test]
    public function addMenuTopWithoutName(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTop('admin/index.php');
        $this->assertSame('admin/index.php', $menu->_menutop['admin/index.php']);
    }

    #[Test]
    public function addMenuTopMultiple(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTop('index.php', 'Home');
        $menu->addMenuTop('settings.php', 'Settings');
        $this->assertCount(2, $menu->_menutop);
    }

    // ---------------------------------------------------------------
    // addMenuTabs tests
    // ---------------------------------------------------------------

    #[Test]
    public function addMenuTabsWithName(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('admin/index.php', 'Main');
        $this->assertSame('Main', $menu->_menutabs['admin/index.php']);
    }

    #[Test]
    public function addMenuTabsWithoutName(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('admin/index.php');
        $this->assertSame('admin/index.php', $menu->_menutabs['admin/index.php']);
    }

    #[Test]
    public function addMenuTabsMultiple(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('index.php', 'Home');
        $menu->addMenuTabs('settings.php', 'Settings');
        $menu->addMenuTabs('about.php', 'About');
        $this->assertCount(3, $menu->_menutabs);
    }

    // ---------------------------------------------------------------
    // addHeader / addSubHeader tests
    // ---------------------------------------------------------------

    #[Test]
    public function addHeaderSetsValue(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addHeader('Module Administration');
        $this->assertSame('Module Administration', $menu->_header);
    }

    #[Test]
    public function addSubHeaderSetsValue(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addSubHeader('User Management');
        $this->assertSame('User Management', $menu->_subheader);
    }

    // ---------------------------------------------------------------
    // getAddon test
    // ---------------------------------------------------------------

    #[Test]
    public function getAddonReplacesObj(): void
    {
        $menu = new \SystemMenuHandler();
        $addon = $this->createMockModule();
        $menu->getAddon($addon);
        $this->assertSame($addon, $menu->_obj);
    }

    // ---------------------------------------------------------------
    // render tests (display=false returns string)
    // ---------------------------------------------------------------

    #[Test]
    public function renderReturnsHtmlString(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTop('index.php', 'Home');
        $menu->addMenuTabs('index.php', 'Main');
        $menu->addMenuTabs('settings.php', 'Settings');

        $result = $menu->render(0, false);
        $this->assertIsString($result);
        $this->assertStringContainsString('buttontop_mod', $result);
    }

    #[Test]
    public function renderContainsMenuTopLinks(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTop('admin/index.php', 'Dashboard');
        $menu->addMenuTabs('index.php', 'Tab1');

        $result = $menu->render(0, false);
        $this->assertStringContainsString('Dashboard', $result);
    }

    #[Test]
    public function renderContainsMenuTabs(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('index.php', 'Tab One');
        $menu->addMenuTabs('settings.php', 'Tab Two');

        $result = $menu->render(0, false);
        $this->assertStringContainsString('Tab One', $result);
        $this->assertStringContainsString('Tab Two', $result);
    }

    #[Test]
    public function renderContainsHeader(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('index.php', 'Tab');
        $menu->addHeader('Admin Panel');

        $result = $menu->render(0, false);
        $this->assertStringContainsString('admin_header', $result);
    }

    #[Test]
    public function renderContainsSubHeader(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('index.php', 'Tab');
        $menu->addSubHeader('Sub Section');

        $result = $menu->render(0, false);
        $this->assertStringContainsString('Sub Section', $result);
        $this->assertStringContainsString('admin_subheader', $result);
    }

    #[Test]
    public function renderHighlightsCurrentTab(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('index.php', 'Home');
        $menu->addMenuTabs('settings.php', 'Settings');

        $result = $menu->render(1, false);
        $this->assertStringContainsString("id='current'", $result);
    }

    #[Test]
    public function renderContainsModuleName(): void
    {
        $menu = new \SystemMenuHandler();
        $menu->addMenuTabs('index.php', 'Tab');

        $result = $menu->render(0, false);
        $this->assertStringContainsString('System', $result);
    }
}
