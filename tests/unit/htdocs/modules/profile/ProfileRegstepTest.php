<?php

declare(strict_types=1);

namespace modulesprofile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsTestStubDatabase;

#[CoversClass(\ProfileRegstep::class)]
#[CoversClass(\ProfileRegstepHandler::class)]
class ProfileRegstepTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/profile/class/regstep.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // ProfileRegstep tests
    // ---------------------------------------------------------------

    #[Test]
    public function regstepExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(\ProfileRegstep::class, \XoopsObject::class));
    }

    #[Test]
    public function regstepConstructorInitializesVars(): void
    {
        $step = new \ProfileRegstep();
        $vars = $step->getVars();
        $this->assertArrayHasKey('step_id', $vars);
        $this->assertArrayHasKey('step_name', $vars);
        $this->assertArrayHasKey('step_desc', $vars);
        $this->assertArrayHasKey('step_order', $vars);
        $this->assertArrayHasKey('step_save', $vars);
    }

    #[Test]
    public function regstepHasFiveVars(): void
    {
        $step = new \ProfileRegstep();
        $this->assertCount(5, $step->getVars());
    }

    #[Test]
    public function regstepStepIdIsIntType(): void
    {
        $step = new \ProfileRegstep();
        $vars = $step->getVars();
        $this->assertSame(XOBJ_DTYPE_INT, $vars['step_id']['data_type']);
    }

    #[Test]
    public function regstepStepNameIsTxtboxType(): void
    {
        $step = new \ProfileRegstep();
        $vars = $step->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['step_name']['data_type']);
    }

    #[Test]
    public function regstepStepDescIsTxtareaType(): void
    {
        $step = new \ProfileRegstep();
        $vars = $step->getVars();
        $this->assertSame(XOBJ_DTYPE_TXTAREA, $vars['step_desc']['data_type']);
    }

    #[Test]
    public function regstepStepOrderDefaultsToOne(): void
    {
        $step = new \ProfileRegstep();
        $this->assertEquals(1, $step->getVar('step_order'));
    }

    #[Test]
    public function regstepStepSaveDefaultsToZero(): void
    {
        $step = new \ProfileRegstep();
        $this->assertEquals(0, $step->getVar('step_save'));
    }

    #[Test]
    public function regstepSetAndGetName(): void
    {
        $step = new \ProfileRegstep();
        $step->setVar('step_name', 'Basic Info');
        $this->assertSame('Basic Info', $step->getVar('step_name'));
    }

    #[Test]
    public function regstepSetAndGetDesc(): void
    {
        $step = new \ProfileRegstep();
        $step->setVar('step_desc', 'Enter your basic information');
        $this->assertSame('Enter your basic information', $step->getVar('step_desc'));
    }

    #[Test]
    public function regstepSetAndGetOrder(): void
    {
        $step = new \ProfileRegstep();
        $step->setVar('step_order', 5);
        $this->assertEquals(5, $step->getVar('step_order'));
    }

    // ---------------------------------------------------------------
    // ProfileRegstepHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function regstepHandlerExtendsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileRegstepHandler($db);
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function regstepHandlerSetsTable(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileRegstepHandler($db);
        $this->assertStringContainsString('profile_regstep', $handler->table);
    }

    #[Test]
    public function regstepHandlerSetsKeyName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileRegstepHandler($db);
        $this->assertSame('step_id', $handler->keyName);
    }

    #[Test]
    public function regstepHandlerSetsIdentifierName(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileRegstepHandler($db);
        $this->assertSame('step_name', $handler->identifierName);
    }

    #[Test]
    public function regstepHandlerCreatesObject(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new \ProfileRegstepHandler($db);
        $obj = $handler->create();
        $this->assertInstanceOf(\ProfileRegstep::class, $obj);
        $this->assertTrue($obj->isNew());
    }
}
