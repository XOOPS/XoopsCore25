<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsTestStubDatabase;

#[CoversClass(\SystemBanner::class)]
#[CoversClass(\SystemBannerHandler::class)]
#[CoversClass(\SystemBannerclient::class)]
#[CoversClass(\SystemBannerclientHandler::class)]
class SystemBannerTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/system/class/banner.php';
            require_once XOOPS_ROOT_PATH . '/modules/system/class/bannerclient.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // SystemBanner tests
    // ---------------------------------------------------------------

    #[Test]
    public function bannerExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\SystemBanner::class, \XoopsObject::class));
    }

    #[Test]
    public function bannerConstructorInitializesVars(): void
    {
        $banner = new \SystemBanner();
        $vars = $banner->getVars();
        $this->assertArrayHasKey('bid', $vars);
        $this->assertArrayHasKey('cid', $vars);
        $this->assertArrayHasKey('imptotal', $vars);
        $this->assertArrayHasKey('impmade', $vars);
        $this->assertArrayHasKey('clicks', $vars);
        $this->assertArrayHasKey('imageurl', $vars);
        $this->assertArrayHasKey('clickurl', $vars);
        $this->assertArrayHasKey('date', $vars);
        $this->assertArrayHasKey('htmlbanner', $vars);
        $this->assertArrayHasKey('htmlcode', $vars);
    }

    #[Test]
    public function bannerHasTenVars(): void
    {
        $banner = new \SystemBanner();
        $this->assertCount(10, $banner->getVars());
    }

    #[Test]
    public function bannerIsNotNewByDefault(): void
    {
        // Direct instantiation does not call setNew(); only handler->create() does
        $banner = new \SystemBanner();
        $this->assertFalse($banner->isNew());
    }

    #[Test]
    public function bannerSetAndGetVar(): void
    {
        $banner = new \SystemBanner();
        $banner->setVar('imageurl', 'http://example.com/banner.jpg');
        $this->assertSame('http://example.com/banner.jpg', $banner->getVar('imageurl'));
    }

    #[Test]
    public function bannerBidIsIntType(): void
    {
        $banner = new \SystemBanner();
        $vars = $banner->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['bid']['data_type']);
    }

    #[Test]
    public function bannerImageurlIsTxtboxType(): void
    {
        $banner = new \SystemBanner();
        $vars = $banner->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['imageurl']['data_type']);
    }

    #[Test]
    public function bannerHtmlbannerIsIntType(): void
    {
        $banner = new \SystemBanner();
        $vars = $banner->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['htmlbanner']['data_type']);
    }

    // ---------------------------------------------------------------
    // SystemBannerHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function bannerHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemBannerHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function bannerHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemBannerHandler($db);
        $this->assertStringContainsString('banner', $handler->table);
    }

    #[Test]
    public function bannerHandlerSetsClassName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemBannerHandler($db);
        $this->assertSame('SystemBanner', $handler->className);
    }

    #[Test]
    public function bannerHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemBannerHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\SystemBanner::class, $obj);
        $this->assertTrue($obj->isNew());
    }

    // ---------------------------------------------------------------
    // SystemBannerclient tests
    // ---------------------------------------------------------------

    #[Test]
    public function bannerclientExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\SystemBannerclient::class, \XoopsObject::class));
    }

    #[Test]
    public function bannerclientConstructorInitializesVars(): void
    {
        $client = new \SystemBannerclient();
        $vars = $client->getVars();
        $this->assertArrayHasKey('cid', $vars);
        $this->assertArrayHasKey('name', $vars);
        $this->assertArrayHasKey('contact', $vars);
        $this->assertArrayHasKey('email', $vars);
        $this->assertArrayHasKey('login', $vars);
        $this->assertArrayHasKey('passwd', $vars);
        $this->assertArrayHasKey('extrainfo', $vars);
    }

    #[Test]
    public function bannerclientHasSevenVars(): void
    {
        $client = new \SystemBannerclient();
        $this->assertCount(7, $client->getVars());
    }

    #[Test]
    public function bannerclientIsNotNewByDefault(): void
    {
        $client = new \SystemBannerclient();
        $this->assertFalse($client->isNew());
    }

    #[Test]
    public function bannerclientSetAndGetVar(): void
    {
        $client = new \SystemBannerclient();
        $client->setVar('name', 'Test Client');
        $this->assertSame('Test Client', $client->getVar('name'));
    }

    #[Test]
    public function bannerclientExtraInfoIsTxtareaType(): void
    {
        $client = new \SystemBannerclient();
        $vars = $client->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['extrainfo']['data_type']);
    }

    // ---------------------------------------------------------------
    // SystemBannerclientHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function bannerclientHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemBannerclientHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function bannerclientHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemBannerclientHandler($db);
        $this->assertStringContainsString('bannerclient', $handler->table);
    }

    #[Test]
    public function bannerclientHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemBannerclientHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\SystemBannerclient::class, $obj);
    }
}
