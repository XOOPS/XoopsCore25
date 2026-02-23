<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\SystemBreadcrumb::class)]
class SystemBreadcrumbTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/system/class/breadcrumb.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorSetsDirectory(): void
    {
        $bc = new \SystemBreadcrumb('users');
        $this->assertSame('users', $bc->_directory);
    }

    #[Test]
    public function constructorInitializesEmptyBread(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $this->assertIsArray($bc->_bread);
        $this->assertEmpty($bc->_bread);
    }

    // ---------------------------------------------------------------
    // addLink tests
    // ---------------------------------------------------------------

    #[Test]
    public function addLinkAppendsToBread(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Home', '/admin/index.php', true);
        $this->assertCount(1, $bc->_bread);
    }

    #[Test]
    public function addLinkStoresTitle(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Dashboard', '/admin/dashboard.php');
        $this->assertSame('Dashboard', $bc->_bread[0]['title']);
    }

    #[Test]
    public function addLinkStoresLink(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Home', '/admin/index.php');
        $this->assertSame('/admin/index.php', $bc->_bread[0]['link']);
    }

    #[Test]
    public function addLinkStoresHomeFlag(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Home', '/admin/index.php', true);
        $this->assertTrue($bc->_bread[0]['home']);
    }

    #[Test]
    public function addLinkHomeFlagDefaultsFalse(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Page', '/page.php');
        $this->assertFalse($bc->_bread[0]['home']);
    }

    #[Test]
    public function addLinkMultipleLinks(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Home', '/', true);
        $bc->addLink('Admin', '/admin/');
        $bc->addLink('Users', '/admin/users.php');
        $this->assertCount(3, $bc->_bread);
        $this->assertSame('Users', $bc->_bread[2]['title']);
    }

    #[Test]
    public function addLinkDefaultsToEmptyStrings(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addLink();
        $this->assertSame('', $bc->_bread[0]['title']);
        $this->assertSame('', $bc->_bread[0]['link']);
    }

    // ---------------------------------------------------------------
    // addHelp tests
    // ---------------------------------------------------------------

    #[Test]
    public function addHelpSetsHelpProperty(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addHelp('admin/help/users.html');
        $this->assertSame('admin/help/users.html', $bc->_help);
    }

    #[Test]
    public function addHelpDefaultsToEmpty(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addHelp();
        $this->assertSame('', $bc->_help);
    }

    // ---------------------------------------------------------------
    // addTips tests
    // ---------------------------------------------------------------

    #[Test]
    public function addTipsSetsTipsProperty(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addTips('This is a helpful tip');
        $this->assertSame('This is a helpful tip', $bc->_tips);
    }

    #[Test]
    public function addTipsOverwritesPrevious(): void
    {
        $bc = new \SystemBreadcrumb('test');
        $bc->addTips('Tip 1');
        $bc->addTips('Tip 2');
        $this->assertSame('Tip 2', $bc->_tips);
    }

    // ---------------------------------------------------------------
    // render tests (no $xoopsTpl — HTML output path)
    // ---------------------------------------------------------------

    #[Test]
    public function renderWithoutTplOutputsHtml(): void
    {
        $oldTpl = $GLOBALS['xoopsTpl'] ?? null;
        unset($GLOBALS['xoopsTpl']);

        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Home', '/', true);
        $bc->addLink('Admin', '/admin/');

        ob_start();
        $bc->render();
        $output = ob_get_clean();

        if ($oldTpl !== null) {
            $GLOBALS['xoopsTpl'] = $oldTpl;
        }

        $this->assertStringContainsString('xo-breadcrumb', $output);
        $this->assertStringContainsString('Home', $output);
        $this->assertStringContainsString('Admin', $output);
    }

    #[Test]
    public function renderWithoutTplIncludesTips(): void
    {
        $oldTpl = $GLOBALS['xoopsTpl'] ?? null;
        unset($GLOBALS['xoopsTpl']);

        $bc = new \SystemBreadcrumb('test');
        $bc->addTips('A helpful tip');

        ob_start();
        $bc->render();
        $output = ob_get_clean();

        if ($oldTpl !== null) {
            $GLOBALS['xoopsTpl'] = $oldTpl;
        }

        $this->assertStringContainsString('A helpful tip', $output);
        $this->assertStringContainsString('class="tips"', $output);
    }

    #[Test]
    public function renderWithoutTplHomeUsesImage(): void
    {
        $oldTpl = $GLOBALS['xoopsTpl'] ?? null;
        unset($GLOBALS['xoopsTpl']);

        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Home', '/', true);

        ob_start();
        $bc->render();
        $output = ob_get_clean();

        if ($oldTpl !== null) {
            $GLOBALS['xoopsTpl'] = $oldTpl;
        }

        $this->assertStringContainsString('home.png', $output);
    }

    #[Test]
    public function renderWithoutTplNonHomeUsesTextLink(): void
    {
        $oldTpl = $GLOBALS['xoopsTpl'] ?? null;
        unset($GLOBALS['xoopsTpl']);

        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Settings', '/admin/settings.php');

        ob_start();
        $bc->render();
        $output = ob_get_clean();

        if ($oldTpl !== null) {
            $GLOBALS['xoopsTpl'] = $oldTpl;
        }

        $this->assertStringContainsString('>Settings</a>', $output);
    }

    #[Test]
    public function renderWithoutTplLinklessItemShowsTextOnly(): void
    {
        $oldTpl = $GLOBALS['xoopsTpl'] ?? null;
        unset($GLOBALS['xoopsTpl']);

        $bc = new \SystemBreadcrumb('test');
        $bc->addLink('Current Page', '');

        ob_start();
        $bc->render();
        $output = ob_get_clean();

        if ($oldTpl !== null) {
            $GLOBALS['xoopsTpl'] = $oldTpl;
        }

        $this->assertStringContainsString('<li>Current Page</li>', $output);
    }
}
