<?php

declare(strict_types=1);

namespace Xmf\Test\Module\Helper;

use PHPUnit\Framework\TestCase;
use Xmf\Module\Helper\GenericHelper;

require_once dirname(__DIR__, 5) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/include/functions.php');
require_once(XOOPS_TU_ROOT_PATH . '/kernel/object.php');

require_once(XOOPS_TU_ROOT_PATH . '/class/logger/xoopslogger.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/preload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/database/databasefactory.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/module.textsanitizer.php');

class GenericHelperTestHelper extends GenericHelper
{
    public static function getHelper($dirname = 'system')
    {
        $instance = new static($dirname);
        return $instance;
    }
}

if (!function_exists('xoops_getHandler')) {
    function xoops_getHandler($name, $optional = false)
    {
        $handler = \Xoops\Core\Handler\Factory::newSpec()
                                              ->scheme('kernel')
                                              ->name($name)
                                              ->optional((bool)$optional)
                                              ->build();
        return $handler;
    }
}

class GenericHelperTest extends TestCase
{
    /**
     * @var GenericHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = GenericHelperTestHelper::getHelper();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testGetModule()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetConfig()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetHandler()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testLoadLanguage()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testSetDebug()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testAddLog()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testIsCurrentModule()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testIsUserAdmin()
    {
        include_once XOOPS_ROOT_PATH . '/kernel/user.php';
        $GLOBALS['xoopsUser'] = '';
        $this->assertFalse($this->object->isUserAdmin());

        $GLOBALS['xoopsUser'] = new \XoopsUser();
        $this->assertFalse($this->object->isUserAdmin());
    }

    public function testUrl()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testPath()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testRedirect()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}
