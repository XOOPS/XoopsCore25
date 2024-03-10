<?php

declare(strict_types=1);

namespace Xmf\Test\Database;

use PHPUnit\Framework\TestCase;
use Xmf\Database\Migrate;

require_once dirname(__DIR__, 4) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/class/logger/xoopslogger.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/preload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/database/databasefactory.php');

require_once(XOOPS_TU_ROOT_PATH . '/include/functions.php');
require_once(XOOPS_TU_ROOT_PATH . '/kernel/object.php');

require_once(XOOPS_TU_ROOT_PATH . '/class/module.textsanitizer.php');

class MigrateTest extends TestCase
{
    /**
     * @var Migrate
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = new Migrate('profile');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testContracts()
    {
        $this->assertInstanceOf(Migrate::class, $this->object);
    }

    public function testSaveCurrentSchema()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetCurrentSchema()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetTargetDefinitions()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testSynchronizeSchema()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetSynchronizeDDL()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetLastError()
    {
        $actual = $this->object->getLastError();
        $this->assertNull($actual);
    }

    public function testGetLastErrNo()
    {
        $actual = $this->object->getLastErrNo();
        $this->assertNull($actual);
    }
}
