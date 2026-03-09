<?php

declare(strict_types=1);

namespace modulesprotector;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Protector module lifecycle files — security audit phase 2.
 *
 * Verifies that eval()-based dynamic function definitions work for both the
 * default 'protector' dirname and cloned/renamed module dirnames.
 */
class ProtectorLifecycleTest extends TestCase
{
    private static bool $registryLoaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$registryLoaded) {
            require_once XOOPS_PATH . '/modules/protector/class/registry.php';
            self::$registryLoaded = true;
        }

        // Set up registry entries that the lifecycle files expect
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('mydirname', 'protector');
        $reg->setEntry('mydirpath', XOOPS_PATH . '/modules/protector');
        $reg->setEntry('language', 'english');
    }

    protected function setUp(): void
    {
        // Ensure registry is populated before each test
        $reg = \ProtectorRegistry::getInstance();
        if ($reg->getEntry('mydirname') === null) {
            $reg->setEntry('mydirname', 'protector');
            $reg->setEntry('mydirpath', XOOPS_PATH . '/modules/protector');
            $reg->setEntry('language', 'english');
        }
    }

    // ---------------------------------------------------------------
    // oninstall.php
    // ---------------------------------------------------------------

    #[Test]
    public function oninstallDefinesInstallFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/oninstall.php';

        $this->assertTrue(
            function_exists('xoops_module_install_protector'),
            'oninstall.php should define xoops_module_install_protector()'
        );
    }

    #[Test]
    public function oninstallDefinesBaseFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/oninstall.php';

        $this->assertTrue(
            function_exists('protector_oninstall_base'),
            'oninstall.php should define protector_oninstall_base()'
        );
    }

    // ---------------------------------------------------------------
    // onuninstall.php
    // ---------------------------------------------------------------

    #[Test]
    public function onuninstallDefinesUninstallFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/onuninstall.php';

        $this->assertTrue(
            function_exists('xoops_module_uninstall_protector'),
            'onuninstall.php should define xoops_module_uninstall_protector()'
        );
    }

    #[Test]
    public function onuninstallDefinesBaseFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/onuninstall.php';

        $this->assertTrue(
            function_exists('protector_onuninstall_base'),
            'onuninstall.php should define protector_onuninstall_base()'
        );
    }

    // ---------------------------------------------------------------
    // onupdate.php
    // ---------------------------------------------------------------

    #[Test]
    public function onupdateDefinesUpdateFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/onupdate.php';

        $this->assertTrue(
            function_exists('xoops_module_update_protector'),
            'onupdate.php should define xoops_module_update_protector()'
        );
    }

    #[Test]
    public function onupdateDefinesBaseFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/onupdate.php';

        $this->assertTrue(
            function_exists('protector_onupdate_base'),
            'onupdate.php should define protector_onupdate_base()'
        );
    }

    // ---------------------------------------------------------------
    // notification.php
    // ---------------------------------------------------------------

    #[Test]
    public function notificationDefinesNotifyFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/notification.php';

        $this->assertTrue(
            function_exists('protector_notify_iteminfo'),
            'notification.php should define protector_notify_iteminfo()'
        );
    }

    #[Test]
    public function notificationDefinesBaseFunction(): void
    {
        require_once XOOPS_PATH . '/modules/protector/notification.php';

        $this->assertTrue(
            function_exists('protector_notify_base'),
            'notification.php should define protector_notify_base()'
        );
    }

    // ---------------------------------------------------------------
    // Verify functions are callable (not just defined)
    // ---------------------------------------------------------------

    #[Test]
    public function installFunctionIsCallable(): void
    {
        require_once XOOPS_PATH . '/modules/protector/oninstall.php';

        $this->assertIsCallable('xoops_module_install_protector');
    }

    #[Test]
    public function uninstallFunctionIsCallable(): void
    {
        require_once XOOPS_PATH . '/modules/protector/onuninstall.php';

        $this->assertIsCallable('xoops_module_uninstall_protector');
    }

    #[Test]
    public function updateFunctionIsCallable(): void
    {
        require_once XOOPS_PATH . '/modules/protector/onupdate.php';

        $this->assertIsCallable('xoops_module_update_protector');
    }

    #[Test]
    public function notifyFunctionIsCallable(): void
    {
        require_once XOOPS_PATH . '/modules/protector/notification.php';

        $this->assertIsCallable('protector_notify_iteminfo');
    }

    // ---------------------------------------------------------------
    // Verify eval()-based dynamic naming uses var_export() safely
    // ---------------------------------------------------------------

    #[Test]
    public function oninstallSourceUsesVarExport(): void
    {
        $source = file_get_contents(XOOPS_PATH . '/modules/protector/oninstall.php');
        $this->assertStringContainsString('var_export($mydirname, true)', $source);
    }

    #[Test]
    public function onuninstallSourceUsesVarExport(): void
    {
        $source = file_get_contents(XOOPS_PATH . '/modules/protector/onuninstall.php');
        $this->assertStringContainsString('var_export($mydirname, true)', $source);
    }

    #[Test]
    public function onupdateSourceUsesVarExport(): void
    {
        $source = file_get_contents(XOOPS_PATH . '/modules/protector/onupdate.php');
        $this->assertStringContainsString('var_export($mydirname, true)', $source);
    }

    #[Test]
    public function notificationSourceUsesVarExport(): void
    {
        $source = file_get_contents(XOOPS_PATH . '/modules/protector/notification.php');
        $this->assertStringContainsString('var_export($mydirname, true)', $source);
    }
}
