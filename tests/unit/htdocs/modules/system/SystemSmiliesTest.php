<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsTestStubDatabase;

#[CoversClass(\SystemSmilies::class)]
#[CoversClass(\SystemsmiliesHandler::class)]
#[CoversClass(\SystemUserrank::class)]
#[CoversClass(\SystemuserrankHandler::class)]
class SystemSmiliesTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/system/class/smilies.php';
            require_once XOOPS_ROOT_PATH . '/modules/system/class/userrank.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // SystemSmilies tests
    // ---------------------------------------------------------------

    #[Test]
    public function smiliesExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\SystemSmilies::class, \XoopsObject::class));
    }

    #[Test]
    public function smiliesConstructorInitializesVars(): void
    {
        $smiley = new \SystemSmilies();
        $vars = $smiley->getVars();
        $this->assertArrayHasKey('id', $vars);
        $this->assertArrayHasKey('code', $vars);
        $this->assertArrayHasKey('smile_url', $vars);
        $this->assertArrayHasKey('emotion', $vars);
        $this->assertArrayHasKey('display', $vars);
    }

    #[Test]
    public function smiliesHasFiveVars(): void
    {
        $smiley = new \SystemSmilies();
        $this->assertCount(5, $smiley->getVars());
    }

    #[Test]
    public function smiliesIsNotNewByDefault(): void
    {
        // Direct instantiation does not call setNew(); only handler->create() does
        $smiley = new \SystemSmilies();
        $this->assertFalse($smiley->isNew());
    }

    #[Test]
    public function smiliesSetAndGetVar(): void
    {
        $smiley = new \SystemSmilies();
        $smiley->setVar('code', ':-)');
        $this->assertSame(':-)', $smiley->getVar('code'));
    }

    #[Test]
    public function smiliesEmotionSetAndGet(): void
    {
        $smiley = new \SystemSmilies();
        $smiley->setVar('emotion', 'Happy');
        $this->assertSame('Happy', $smiley->getVar('emotion'));
    }

    #[Test]
    public function smiliesIdIsIntType(): void
    {
        $smiley = new \SystemSmilies();
        $vars = $smiley->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['id']['data_type']);
    }

    #[Test]
    public function smiliesCodeIsTxtboxType(): void
    {
        $smiley = new \SystemSmilies();
        $vars = $smiley->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['code']['data_type']);
    }

    // ---------------------------------------------------------------
    // SystemsmiliesHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function smiliesHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemsmiliesHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function smiliesHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemsmiliesHandler($db);
        $this->assertStringContainsString('smiles', $handler->table);
    }

    #[Test]
    public function smiliesHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemsmiliesHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\SystemSmilies::class, $obj);
    }

    // ---------------------------------------------------------------
    // SystemUserrank tests
    // ---------------------------------------------------------------

    #[Test]
    public function userrankExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\SystemUserrank::class, \XoopsObject::class));
    }

    #[Test]
    public function userrankConstructorInitializesVars(): void
    {
        $rank = new \SystemUserrank();
        $vars = $rank->getVars();
        $this->assertArrayHasKey('rank_id', $vars);
        $this->assertArrayHasKey('rank_title', $vars);
        $this->assertArrayHasKey('rank_min', $vars);
        $this->assertArrayHasKey('rank_max', $vars);
        $this->assertArrayHasKey('rank_special', $vars);
        $this->assertArrayHasKey('rank_image', $vars);
    }

    #[Test]
    public function userrankHasSixVars(): void
    {
        $rank = new \SystemUserrank();
        $this->assertCount(6, $rank->getVars());
    }

    #[Test]
    public function userrankIsNotNewByDefault(): void
    {
        $rank = new \SystemUserrank();
        $this->assertFalse($rank->isNew());
    }

    #[Test]
    public function userrankSetAndGetVar(): void
    {
        $rank = new \SystemUserrank();
        $rank->setVar('rank_title', 'Admin');
        $this->assertSame('Admin', $rank->getVar('rank_title'));
    }

    #[Test]
    public function userrankMinMaxAreIntType(): void
    {
        $rank = new \SystemUserrank();
        $vars = $rank->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['rank_min']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['rank_max']['data_type']);
    }

    #[Test]
    public function userrankSpecialIsIntType(): void
    {
        $rank = new \SystemUserrank();
        $vars = $rank->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['rank_special']['data_type']);
    }

    // ---------------------------------------------------------------
    // SystemuserrankHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function userrankHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemuserrankHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function userrankHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemuserrankHandler($db);
        $this->assertStringContainsString('ranks', $handler->table);
    }

    #[Test]
    public function userrankHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \SystemuserrankHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\SystemUserrank::class, $obj);
    }
}
