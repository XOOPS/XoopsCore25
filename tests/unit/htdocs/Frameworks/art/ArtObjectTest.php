<?php

declare(strict_types=1);

namespace frameworksart;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ArtObject;
use ArtObjectHandler;
use XoopsTestStubDatabase;

#[CoversClass(ArtObject::class)]
#[CoversClass(ArtObjectHandler::class)]
class ArtObjectTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            // Initialize logger before any tests to avoid "risky" handler warnings
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.ini.php';
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/object.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // ArtObject tests
    // ---------------------------------------------------------------

    #[Test]
    public function artObjectExtendsXoopsObject(): void
    {
        $this->assertTrue(is_subclass_of(ArtObject::class, \XoopsObject::class));
    }

    #[Test]
    public function artObjectConstructorLogsDeprecation(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        new ArtObject();

        $this->assertGreaterThan($countBefore, count($logger->deprecated));
    }

    #[Test]
    public function artObjectDeprecationMessageContainsClassName(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        new ArtObject();

        $lastMsg = $logger->deprecated[count($logger->deprecated) - 1];
        $this->assertStringContainsString('ArtObject', $lastMsg);
        $this->assertStringContainsString('deprecated', $lastMsg);
    }

    #[Test]
    public function artObjectHasPluginPathProperty(): void
    {
        $obj = new ArtObject();
        $this->assertTrue(property_exists($obj, 'plugin_path'));
    }

    // ---------------------------------------------------------------
    // ArtObjectHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function artObjectHandlerExtendsXoopsPersistableObjectHandler(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new ArtObjectHandler($db, 'test_table', 'ArtObject', 'id', 'title');
        $this->assertInstanceOf(\XoopsPersistableObjectHandler::class, $handler);
    }

    #[Test]
    public function artObjectHandlerConstructorLogsDeprecation(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        $db = new XoopsTestStubDatabase();
        new ArtObjectHandler($db, 'test_table', 'ArtObject', 'id', 'title');

        $this->assertGreaterThan($countBefore, count($logger->deprecated));
    }

    #[Test]
    public function artObjectHandlerDeprecationContainsClassName(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        $db = new XoopsTestStubDatabase();
        new ArtObjectHandler($db, 'test_table', 'ArtObject', 'id', 'title');

        $lastMsg = $logger->deprecated[count($logger->deprecated) - 1];
        $this->assertStringContainsString('ArtObjectHandler', $lastMsg);
    }

    #[Test]
    public function artObjectHandlerSetsDbProperty(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new ArtObjectHandler($db, 'test_table', 'ArtObject', 'id', 'title');
        // The public $db property is set by the constructor
        $this->assertInstanceOf(XoopsTestStubDatabase::class, $handler->db);
    }

    #[Test]
    public function artObjectHandlerInsertReturnsFalseForWrongClass(): void
    {
        $db = new XoopsTestStubDatabase();
        $handler = new ArtObjectHandler($db, 'test_table', 'ArtObject', 'id', 'title');

        // Create a plain XoopsObject (not ArtObject)
        $wrongObj = new \XoopsObject();
        $result = $handler->insert($wrongObj, true);
        $this->assertFalse($result);
    }
}
