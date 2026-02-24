<?php
/**
 * Unit tests for modules/system/admin/modulesadmin/modulesadmin.php
 *
 * Tests foreign key check handling during module install/uninstall,
 * reserved table deduplication, file reading safety, and array merge fixes.
 *
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      Tests\Unit\System\ModulesAdmin
 */

declare(strict_types=1);

namespace Tests\Unit\System\ModulesAdmin;

use PHPUnit\Framework\TestCase;

/**
 * Tests for modulesadmin.php foreign key handling and bug fixes
 *
 * These tests verify the behavior of the module installer functions
 * without requiring a full XOOPS bootstrap. We parse the source file
 * to validate structural correctness of the changes.
 */
class ModulesAdminTest extends TestCase
{
    /**
     * @var string The source code of modulesadmin.php
     */
    private string $sourceCode;

    /**
     * @var string Path to the modulesadmin.php file under test
     */
    private string $filePath;

    protected function setUp(): void
    {
        // Use the file from the project root relative to where tests run
        // Adjust path based on your project structure
        $possiblePaths = [
            // Webroot IS htdocs: __DIR__ = tests/unit/htdocs/modules/system
            dirname(__DIR__, 5) . '/modules/system/admin/modulesadmin/modulesadmin.php',
            // XoopsCore25 repo layout: htdocs is a subdirectory
            dirname(__DIR__, 4) . '/htdocs/modules/system/admin/modulesadmin/modulesadmin.php',
            dirname(__DIR__, 5) . '/htdocs/modules/system/admin/modulesadmin/modulesadmin.php',
            dirname(__DIR__, 3) . '/modules/system/admin/modulesadmin/modulesadmin.php',
            dirname(__DIR__, 7) . '/modules/system/admin/modulesadmin/modulesadmin.php',
        ];

        $this->filePath = '';
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $this->filePath = $path;
                break;
            }
        }

        if ($this->filePath === '') {
            $this->markTestSkipped('modulesadmin.php not found in expected locations');
        }

        $this->sourceCode = file_get_contents($this->filePath);
        $this->assertNotEmpty($this->sourceCode, 'Source file should not be empty');
    }

    // =========================================================================
    // Foreign Key Check Tests - Install
    // =========================================================================

    /**
     * Verify that SET FOREIGN_KEY_CHECKS = 0 is called before SQL execution
     * during module installation to allow tables with FK dependencies to be
     * created in any order.
     */
    public function testInstallDisablesForeignKeyChecksBeforeSqlExecution(): void
    {
        // The FK disable should appear BEFORE the foreach ($pieces as $piece) loop
        $fkDisablePos = strpos($this->sourceCode, "SET FOREIGN_KEY_CHECKS = 0");
        $this->assertNotFalse($fkDisablePos, 'Should contain SET FOREIGN_KEY_CHECKS = 0');

        // Find the SQL execution loop in xoops_module_install
        $installFunc = $this->extractFunction('xoops_module_install');
        $this->assertNotEmpty($installFunc, 'xoops_module_install function should exist');

        // FK disable should come before the pieces loop
        $fkDisableInInstall = strpos($installFunc, "SET FOREIGN_KEY_CHECKS = 0");
        $piecesLoop = strpos($installFunc, 'foreach ($pieces as $piece)');
        $this->assertNotFalse($fkDisableInInstall, 'FK disable should be in install function');
        $this->assertNotFalse($piecesLoop, 'Pieces loop should exist in install function');
        $this->assertLessThan(
            $piecesLoop,
            $fkDisableInInstall,
            'FK disable must come BEFORE the SQL execution loop'
        );
    }

    /**
     * Verify that SET FOREIGN_KEY_CHECKS = 1 is called after SQL execution
     * during module installation to restore normal constraint checking.
     */
    public function testInstallReenablesForeignKeyChecksAfterSqlExecution(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        // FK re-enable should come after the pieces loop
        $fkEnablePos = strpos($installFunc, "SET FOREIGN_KEY_CHECKS = 1");
        $this->assertNotFalse($fkEnablePos, 'FK re-enable should be in install function');

        // Should appear after the foreach loop ends
        $piecesLoop = strpos($installFunc, 'foreach ($pieces as $piece)');
        $this->assertLessThan(
            $fkEnablePos,
            $piecesLoop,
            'FK re-enable must come AFTER the SQL execution loop'
        );
    }

    /**
     * Verify that error rollback (dropping created tables) also disables
     * FK checks so dependent tables can be dropped in any order.
     */
    public function testInstallRollbackDisablesForeignKeyChecks(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        // Find the error rollback section: if ($error === true) { ... DROP TABLE ...
        // There should be FK disable before DROP TABLE in rollback
        $errorBlock = $this->extractErrorRollbackBlock($installFunc);
        $this->assertNotEmpty($errorBlock, 'Error rollback block should exist');

        $this->assertStringContainsString(
            'SET FOREIGN_KEY_CHECKS = 0',
            $errorBlock,
            'Error rollback should disable FK checks before dropping tables'
        );
        $this->assertStringContainsString(
            'SET FOREIGN_KEY_CHECKS = 1',
            $errorBlock,
            'Error rollback should re-enable FK checks after dropping tables'
        );
    }

    /**
     * Verify that the module insert failure path also handles FK checks
     * when rolling back created tables.
     */
    public function testInstallModuleInsertFailureRollbackHasFkChecks(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        // Find the section: if (!$module_handler->insert($module))
        // which also drops created tables
        $insertFailure = strpos($installFunc, 'INSERT_DATA_FAILD');
        $this->assertNotFalse($insertFailure, 'Module insert failure handler should exist');

        // The FK disable should appear near this rollback section too
        $rollbackSection = substr($installFunc, $insertFailure, 500);
        $this->assertStringContainsString(
            'SET FOREIGN_KEY_CHECKS = 0',
            $rollbackSection,
            'Module insert failure rollback should disable FK checks'
        );
    }

    // =========================================================================
    // Foreign Key Check Tests - Uninstall
    // =========================================================================

    /**
     * Verify that uninstall wraps table drops with FK check disable/enable
     * so tables with foreign key dependencies can be dropped in any order.
     */
    public function testUninstallDisablesForeignKeyChecksForTableDrops(): void
    {
        $uninstallFunc = $this->extractFunction('xoops_module_uninstall');
        $this->assertNotEmpty($uninstallFunc, 'xoops_module_uninstall function should exist');

        // Find the table dropping section
        $tableDropSection = strpos($uninstallFunc, 'DELETE_MOD_TABLES');
        $this->assertNotFalse($tableDropSection, 'Table drop section should exist');

        // Extract from DELETE_MOD_TABLES to a reasonable distance after
        $dropBlock = substr($uninstallFunc, $tableDropSection, 1500);

        $this->assertStringContainsString(
            'SET FOREIGN_KEY_CHECKS = 0',
            $dropBlock,
            'Uninstall table drops should disable FK checks'
        );
        $this->assertStringContainsString(
            'SET FOREIGN_KEY_CHECKS = 1',
            $dropBlock,
            'Uninstall table drops should re-enable FK checks'
        );
    }

    /**
     * Verify FK disable comes before the DROP TABLE loop in uninstall,
     * and FK enable comes after.
     */
    public function testUninstallForeignKeyCheckOrder(): void
    {
        $uninstallFunc = $this->extractFunction('xoops_module_uninstall');

        // Find the table section specifically
        $tableSection = strpos($uninstallFunc, 'DELETE_MOD_TABLES');
        $sectionCode = substr($uninstallFunc, $tableSection, 1500);

        $fkDisable = strpos($sectionCode, 'FOREIGN_KEY_CHECKS = 0');
        $dropTable = strpos($sectionCode, 'DROP TABLE');
        $fkEnable = strpos($sectionCode, 'FOREIGN_KEY_CHECKS = 1');

        $this->assertNotFalse($fkDisable, 'FK disable should exist in drop section');
        $this->assertNotFalse($dropTable, 'DROP TABLE should exist');
        $this->assertNotFalse($fkEnable, 'FK enable should exist in drop section');

        $this->assertLessThan($dropTable, $fkDisable, 'FK disable must come before DROP TABLE');
        $this->assertLessThan($fkEnable, $dropTable, 'DROP TABLE must come before FK enable');
    }

    // =========================================================================
    // File Reading Safety Tests
    // =========================================================================

    /**
     * Verify that file_get_contents is used instead of fread(fopen()) for
     * reading SQL files. The old pattern fread(fopen($path, 'r'), filesize($path))
     * is unsafe because fopen can fail silently.
     */
    public function testUsesFileGetContentsInsteadOfFreadFopen(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        $this->assertStringNotContainsString(
            'fread(fopen(',
            $installFunc,
            'Should not use unsafe fread(fopen()) pattern'
        );

        $this->assertStringContainsString(
            'file_get_contents',
            $installFunc,
            'Should use file_get_contents() for reading SQL files'
        );
    }

    /**
     * Verify that file_get_contents failure is handled (returns false check).
     */
    public function testHandlesFileGetContentsFailure(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        // Should check for false return from file_get_contents
        $this->assertStringContainsString(
            '=== false',
            $installFunc,
            'Should check for file_get_contents returning false'
        );
    }

    // =========================================================================
    // Reserved Tables Deduplication Tests
    // =========================================================================

    /**
     * Verify that the $reservedTables array in xoops_module_install does not
     * contain duplicate entries. The original had 'banner', 'bannerclient',
     * and 'bannerfinish' listed twice each.
     */
    public function testInstallReservedTablesHaveNoDuplicates(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');
        $tables = $this->extractReservedTables($installFunc);

        $this->assertNotEmpty($tables, 'Should extract reserved tables');

        $duplicates = array_diff_assoc($tables, array_unique($tables));
        $this->assertEmpty(
            $duplicates,
            'Reserved tables should have no duplicates. Found: ' . implode(', ', $duplicates)
        );
    }

    /**
     * Verify that the $reservedTables array in xoops_module_uninstall does not
     * contain duplicate entries.
     */
    public function testUninstallReservedTablesHaveNoDuplicates(): void
    {
        $uninstallFunc = $this->extractFunction('xoops_module_uninstall');
        $tables = $this->extractReservedTables($uninstallFunc);

        $this->assertNotEmpty($tables, 'Should extract reserved tables');

        $duplicates = array_diff_assoc($tables, array_unique($tables));
        $this->assertEmpty(
            $duplicates,
            'Reserved tables should have no duplicates. Found: ' . implode(', ', $duplicates)
        );
    }

    /**
     * Verify the known core tables are still present in the reserved list.
     */
    public function testReservedTablesContainCoreTables(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');
        $tables = $this->extractReservedTables($installFunc);

        $requiredTables = [
            'users', 'groups', 'modules', 'config', 'session',
            'banner', 'bannerclient', 'bannerfinish', 'newblocks',
            'tplfile', 'tplset', 'tplsource',
        ];

        foreach ($requiredTables as $table) {
            $this->assertContains(
                $table,
                $tables,
                "Reserved tables should contain core table: {$table}"
            );
        }
    }

    // =========================================================================
    // Array Merge Bug Fix Tests
    // =========================================================================

    /**
     * Verify that the code uses array_merge() instead of the + operator
     * when combining $msgs with module errors. The + operator silently
     * drops values with duplicate numeric keys.
     */
    public function testUsesArrayMergeInsteadOfPlusOperator(): void
    {
        // Check that $msgs += is NOT used anywhere in the file
        // The pattern "$msgs +=" with the + operator loses data
        $plusPattern = '/\$msgs\s*\+=\s*\$module->getErrors\(\)/';
        $matches = preg_match_all($plusPattern, $this->sourceCode, $found);

        $this->assertEquals(
            0,
            $matches,
            'Should not use $msgs += $module->getErrors() (loses data with duplicate keys). '
            . 'Use array_merge() instead. Found ' . $matches . ' occurrence(s)'
        );
    }

    /**
     * Verify array_merge is used for combining message arrays with errors.
     */
    public function testUsesArrayMergeForErrorCombination(): void
    {
        $mergePattern = '/\$msgs\s*=\s*array_merge\s*\(\s*\$msgs\s*,\s*\$module->getErrors\(\)\s*\)/';
        $matches = preg_match_all($mergePattern, $this->sourceCode, $found);

        $this->assertGreaterThan(
            0,
            $matches,
            'Should use array_merge($msgs, $module->getErrors()) for error combination'
        );
    }

    // =========================================================================
    // Success Message Constant Bug Fix Tests
    // =========================================================================

    /**
     * Verify that the module_read permission success path uses the correct
     * success message constant, not the error constant.
     *
     * Original bug: success path for module_read permission insertion used
     * _AM_SYSTEM_MODULES_ACCESS_USER_ADD_ERROR instead of
     * _AM_SYSTEM_MODULES_ACCESS_USER_ADD
     */
    public function testModuleReadPermissionSuccessUsesCorrectConstant(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        // Find the module_read permission section
        $readPermSection = strpos($installFunc, "module_read");
        $this->assertNotFalse($readPermSection, 'module_read permission section should exist');

        // Extract the permission block (about 600 chars should cover it)
        $permBlock = substr($installFunc, $readPermSection, 600);

        // The success branch (else clause) should use ACCESS_USER_ADD, not ACCESS_USER_ADD_ERROR
        // Look for the pattern: } else { ... ACCESS_USER_ADD ...
        // The error branch should use ACCESS_USER_ADD_ERROR
        // We check that ACCESS_USER_ADD (without _ERROR suffix) appears in this block
        $this->assertMatchesRegularExpression(
            '/}\s*else\s*\{[^}]*ACCESS_USER_ADD[^_E]/',
            $permBlock,
            'Success path for module_read should use _AM_SYSTEM_MODULES_ACCESS_USER_ADD (not _ERROR)'
        );
    }

    // =========================================================================
    // DROP TABLE IF EXISTS Tests
    // =========================================================================

    /**
     * Verify that error rollback uses DROP TABLE IF EXISTS for safety.
     */
    public function testRollbackUsesDropTableIfExists(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        // The rollback sections should use IF EXISTS for safety
        $this->assertStringContainsString(
            'DROP TABLE IF EXISTS',
            $installFunc,
            'Rollback should use DROP TABLE IF EXISTS for safety'
        );
    }

    // =========================================================================
    // Structural Integrity Tests
    // =========================================================================

    /**
     * Verify the file is valid PHP syntax.
     */
    public function testFileHasValidPhpSyntax(): void
    {
        $output = [];
        $returnCode = 0;
        exec('php -l ' . escapeshellarg($this->filePath) . ' 2>&1', $output, $returnCode);

        $this->assertEquals(
            0,
            $returnCode,
            'PHP syntax check failed: ' . implode("\n", $output)
        );
    }

    /**
     * Verify all expected functions exist in the file.
     */
    public function testAllExpectedFunctionsExist(): void
    {
        $expectedFunctions = [
            'xoops_module_install',
            'xoops_module_uninstall',
            'xoops_module_update',
            'xoops_module_activate',
            'xoops_module_deactivate',
            'xoops_module_change',
            'xoops_module_log_header',
            'xoops_module_delayed_clean_cache',
        ];

        foreach ($expectedFunctions as $func) {
            $this->assertStringContainsString(
                "function {$func}(",
                $this->sourceCode,
                "Function {$func} should exist in modulesadmin.php"
            );
        }

        // xoops_module_gettemplate uses return-by-reference syntax
        $this->assertMatchesRegularExpression(
            '/function\s+&?xoops_module_gettemplate\s*\(/',
            $this->sourceCode,
            'Function xoops_module_gettemplate should exist in modulesadmin.php'
        );
    }

    /**
     * Verify the update function also uses array_merge properly.
     */
    public function testUpdateFunctionUsesArrayMerge(): void
    {
        $updateFunc = $this->extractFunction('xoops_module_update');
        $this->assertNotEmpty($updateFunc, 'xoops_module_update should exist');

        $plusPattern = '/\$msgs\s*\+=\s*\$module->getErrors\(\)/';
        $matches = preg_match_all($plusPattern, $updateFunc, $found);

        $this->assertEquals(
            0,
            $matches,
            'xoops_module_update should not use $msgs += for error merging'
        );
    }

    // =========================================================================
    // SET Statement Skipping Tests
    // =========================================================================

    /**
     * Verify that the install function skips SET statements from SQL files.
     *
     * Module SQL files may contain SET FOREIGN_KEY_CHECKS or other SET
     * statements. Since SqlUtility::prefixQuery() only recognizes DDL/DML
     * (CREATE TABLE, INSERT INTO, ALTER TABLE, UPDATE, DROP TABLE), SET
     * statements would cause a "not valid SQL" error. The installer should
     * skip them gracefully.
     */
    public function testInstallSkipsSetStatements(): void
    {
        $installFunc = $this->extractFunction('xoops_module_install');

        // Should contain the preg_match for SET statements
        $this->assertStringContainsString(
            "preg_match('/^SET\\s+/i'",
            $installFunc,
            'Install function should check for SET statements and skip them'
        );

        // The SET skip should come before prefixQuery
        $setSkipPos = strpos($installFunc, "preg_match('/^SET");
        $prefixQueryPos = strpos($installFunc, 'SqlUtility::prefixQuery');
        $this->assertNotFalse($setSkipPos, 'SET skip check should exist');
        $this->assertNotFalse($prefixQueryPos, 'prefixQuery call should exist');
        $this->assertLessThan(
            $prefixQueryPos,
            $setSkipPos,
            'SET statement skip must come BEFORE prefixQuery call'
        );
    }

    // =========================================================================
    // FK Check Pairing Tests
    // =========================================================================

    /**
     * Verify that every SET FOREIGN_KEY_CHECKS = 0 has a matching = 1.
     * This ensures FK checks are always restored.
     */
    public function testForeignKeyChecksAreProperllyPaired(): void
    {
        $disableCount = substr_count($this->sourceCode, 'SET FOREIGN_KEY_CHECKS = 0');
        $enableCount = substr_count($this->sourceCode, 'SET FOREIGN_KEY_CHECKS = 1');

        $this->assertGreaterThan(0, $disableCount, 'Should have FK disable statements');
        $this->assertEquals(
            $disableCount,
            $enableCount,
            "FK check disable ({$disableCount}) and enable ({$enableCount}) counts should match"
        );
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Extract a function body from the source code by function name.
     *
     * Uses brace counting to find the complete function body.
     *
     * @param string $functionName The function name to extract
     * @return string The function body, or empty string if not found
     */
    private function extractFunction(string $functionName): string
    {
        $pattern = '/function\s+' . preg_quote($functionName, '/') . '\s*\(/';
        if (!preg_match($pattern, $this->sourceCode, $matches, PREG_OFFSET_CAPTURE)) {
            return '';
        }

        $startPos = $matches[0][1];
        $bracePos = strpos($this->sourceCode, '{', $startPos);
        if ($bracePos === false) {
            return '';
        }

        $depth = 0;
        $length = strlen($this->sourceCode);
        $endPos = $bracePos;

        for ($i = $bracePos; $i < $length; $i++) {
            $char = $this->sourceCode[$i];
            if ($char === '{') {
                $depth++;
            } elseif ($char === '}') {
                $depth--;
                if ($depth === 0) {
                    $endPos = $i;
                    break;
                }
            }
        }

        return substr($this->sourceCode, $startPos, $endPos - $startPos + 1);
    }

    /**
     * Extract the error rollback block from install function code.
     *
     * Looks for the pattern: if ($error === true) { foreach ... DROP TABLE ...
     *
     * @param string $functionCode The function source code
     * @return string The error rollback block
     */
    private function extractErrorRollbackBlock(string $functionCode): string
    {
        // Find: if ($error === true) { ... foreach ($created_tables ...
        $pos = strpos($functionCode, 'if ($error === true)');
        if ($pos === false) {
            return '';
        }

        // Extract a reasonable chunk that covers the rollback block
        return substr($functionCode, $pos, 500);
    }

    /**
     * Extract reserved table names from a function's source code.
     *
     * @param string $functionCode The function source code
     * @return array List of table name strings
     */
    private function extractReservedTables(string $functionCode): array
    {
        // Find the $reservedTables array declaration
        $start = strpos($functionCode, '$reservedTables');
        if ($start === false) {
            return [];
        }

        // Find the closing bracket of the array
        $arrayStart = strpos($functionCode, '[', $start);
        if ($arrayStart === false) {
            return [];
        }

        $depth = 0;
        $arrayEnd = $arrayStart;
        $length = strlen($functionCode);
        for ($i = $arrayStart; $i < $length; $i++) {
            if ($functionCode[$i] === '[') {
                $depth++;
            } elseif ($functionCode[$i] === ']') {
                $depth--;
                if ($depth === 0) {
                    $arrayEnd = $i;
                    break;
                }
            }
        }

        $arrayStr = substr($functionCode, $arrayStart, $arrayEnd - $arrayStart + 1);

        // Extract quoted strings
        preg_match_all("/['\"]([^'\"]+)['\"]/", $arrayStr, $matches);

        return $matches[1] ?? [];
    }
}
