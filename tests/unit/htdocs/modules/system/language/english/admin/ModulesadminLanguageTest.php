<?php

declare(strict_types=1);

namespace modulessystem;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for modulesadmin language constants (M-6).
 *
 * Verifies the new FK/drop table localization constants are defined
 * and have non-empty string values.
 */
class ModulesadminLanguageTest extends TestCase
{
    protected function setUp(): void
    {
        $langFile = XOOPS_ROOT_PATH . '/modules/system/language/english/admin/modulesadmin.php';
        if (!defined('_AM_SYSTEM_MODULES_FK_DISABLE')) {
            // Need _AM_SYSTEM_DBUPDATED before including the file
            if (!defined('_AM_SYSTEM_DBUPDATED')) {
                define('_AM_SYSTEM_DBUPDATED', 'Database Updated Successfully!');
            }
            require_once $langFile;
        }
    }

    #[Test]
    public function fkDisableConstantIsDefined(): void
    {
        $this->assertTrue(defined('_AM_SYSTEM_MODULES_FK_DISABLE'));
        $this->assertNotEmpty(_AM_SYSTEM_MODULES_FK_DISABLE);
        $this->assertIsString(_AM_SYSTEM_MODULES_FK_DISABLE);
    }

    #[Test]
    public function fkEnableConstantIsDefined(): void
    {
        $this->assertTrue(defined('_AM_SYSTEM_MODULES_FK_ENABLE'));
        $this->assertNotEmpty(_AM_SYSTEM_MODULES_FK_ENABLE);
        $this->assertIsString(_AM_SYSTEM_MODULES_FK_ENABLE);
    }

    #[Test]
    public function dropFailConstantIsDefined(): void
    {
        $this->assertTrue(defined('_AM_SYSTEM_MODULES_DROP_FAIL'));
        $this->assertNotEmpty(_AM_SYSTEM_MODULES_DROP_FAIL);
        $this->assertIsString(_AM_SYSTEM_MODULES_DROP_FAIL);
    }

    #[Test]
    public function dropFailConstantContainsPlaceholder(): void
    {
        $this->assertStringContainsString('%s', _AM_SYSTEM_MODULES_DROP_FAIL);
    }

    #[Test]
    public function dropOkConstantIsDefined(): void
    {
        $this->assertTrue(defined('_AM_SYSTEM_MODULES_DROP_OK'));
        $this->assertNotEmpty(_AM_SYSTEM_MODULES_DROP_OK);
        $this->assertIsString(_AM_SYSTEM_MODULES_DROP_OK);
    }

    #[Test]
    public function dropOkConstantContainsPlaceholder(): void
    {
        $this->assertStringContainsString('%s', _AM_SYSTEM_MODULES_DROP_OK);
    }

    #[Test]
    public function modulesadminFileUsesLocalizedFkStrings(): void
    {
        $source = file_get_contents(
            XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php'
        );
        // Should NOT contain the old hard-coded English strings
        $this->assertStringNotContainsString(
            "'Failed to disable FOREIGN_KEY_CHECKS",
            $source,
            'Hard-coded FK disable string should be replaced with _AM_SYSTEM_MODULES_FK_DISABLE'
        );
        $this->assertStringNotContainsString(
            "'Failed to restore FOREIGN_KEY_CHECKS",
            $source,
            'Hard-coded FK restore string should be replaced with _AM_SYSTEM_MODULES_FK_ENABLE'
        );
        $this->assertStringNotContainsString(
            "'Failed to drop table ",
            $source,
            'Hard-coded drop table string should be replaced with _AM_SYSTEM_MODULES_DROP_FAIL'
        );
        // Should contain the constant references
        $this->assertStringContainsString('_AM_SYSTEM_MODULES_FK_DISABLE', $source);
        $this->assertStringContainsString('_AM_SYSTEM_MODULES_FK_ENABLE', $source);
    }
}
