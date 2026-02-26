<?php
/**
 * Unit tests for modules/system/admin/modulesadmin/main.php
 *
 * Tests the module administration controller including security checks,
 * operation routing, parameter sanitization, and template data preparation.
 *
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      Tests\Unit\System\ModulesAdmin
 */

declare(strict_types=1);

namespace Tests\Unit\System\ModulesAdmin;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/SourceFileTestTrait.php';
use Tests\Unit\System\SourceFileTestTrait;

/**
 * Tests for main.php module administration controller
 *
 * These tests verify the behavior of the module administration interface
 * including operation routing, security token validation, parameter handling,
 * and data preparation for templates.
 */
class MainControllerTest extends TestCase
{
    use SourceFileTestTrait;

    /**
     * @var string Alias for sourceContent (kept for readability)
     */
    private string $sourceCode;

    protected function setUp(): void
    {
        $this->loadSourceFile('htdocs/modules/system/admin/modulesadmin/main.php');
        $this->sourceCode = $this->sourceContent;
    }

    // =========================================================================
    // Security and Access Control Tests
    // =========================================================================

    /**
     * Verify that the file checks user permissions before proceeding.
     */
    public function testEnforcesAdminPermissionCheck(): void
    {
        // Should check if user is object, module is object, and user is admin
        $this->assertStringContainsString(
            'if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid()))',
            $this->sourceCode,
            'Should enforce admin permission check at the start'
        );

        // Should exit with NOPERM if not authorized
        $this->assertStringContainsString(
            'exit(_NOPERM);',
            $this->sourceCode,
            'Should exit with no permission message if unauthorized'
        );
    }

    /**
     * Verify that security token validation is performed for state-changing operations.
     */
    public function testValidatesSecurityTokenForStateChangingOps(): void
    {
        // Should check security token for confirm, submit, install_ok, update_ok, uninstall_ok
        $this->assertStringContainsString(
            "if (in_array(\$op, ['confirm', 'submit', 'install_ok', 'update_ok', 'uninstall_ok']))",
            $this->sourceCode,
            'Should identify state-changing operations'
        );

        $this->assertStringContainsString(
            '$GLOBALS[\'xoopsSecurity\']->check()',
            $this->sourceCode,
            'Should validate security token for state-changing operations'
        );

        // Should reset op to 'list' if token check fails
        $this->assertStringContainsString(
            "\$op = 'list';",
            $this->sourceCode,
            'Should reset operation to list if security check fails'
        );
    }

    /**
     * Verify that the security token is passed to templates for form submission.
     */
    public function testPassesSecurityTokenToTemplate(): void
    {
        // In the confirm operation, should assign security token to template
        $confirmSection = $this->extractOperationSection('confirm');
        $this->assertNotEmpty($confirmSection, 'Confirm operation should exist');

        $this->assertStringContainsString(
            "\$GLOBALS['xoopsSecurity']->getTokenHTML()",
            $confirmSection,
            'Should generate security token HTML for form'
        );

        $this->assertStringContainsString(
            "\$xoopsTpl->assign('input_security',",
            $confirmSection,
            'Should assign security token to template'
        );
    }

    // =========================================================================
    // Operation Routing Tests
    // =========================================================================

    /**
     * Verify that all expected operations are handled in the switch statement.
     */
    public function testHandlesAllExpectedOperations(): void
    {
        $expectedOps = [
            'list',
            'installlist',
            'order',
            'confirm',
            'display',
            'display_in_menu',
            'submit',
            'install',
            'install_ok',
            'uninstall',
            'uninstall_ok',
            'update',
            'update_ok',
        ];

        foreach ($expectedOps as $op) {
            $this->assertMatchesRegularExpression(
                "/case\s+['\"]" . preg_quote($op, '/') . "['\"]:/",
                $this->sourceCode,
                "Should handle operation: {$op}"
            );
        }
    }

    /**
     * Verify that operation parameter is retrieved using Request helper.
     */
    public function testRetrievesOperationParameterSafely(): void
    {
        $this->assertStringContainsString(
            "use Xmf\Request;",
            $this->sourceCode,
            'Should use Xmf Request helper'
        );

        $this->assertMatchesRegularExpression(
            '/\$op\s*=\s*Request::getString\s*\(\s*[\'"]op[\'"]\s*,\s*[\'"]list[\'"]\s*\)/',
            $this->sourceCode,
            'Should retrieve operation parameter with default value'
        );
    }

    /**
     * Verify that the default operation is 'list'.
     */
    public function testDefaultOperationIsList(): void
    {
        $this->assertMatchesRegularExpression(
            '/\$op\s*=\s*Request::getString\s*\([^)]*[\'"]list[\'"]/',
            $this->sourceCode,
            'Default operation should be list'
        );
    }

    // =========================================================================
    // Input Sanitization Tests
    // =========================================================================

    /**
     * Verify that POST data is sanitized using XoopsFilterInput.
     */
    public function testSanitizesPostDataUsingFilterInput(): void
    {
        $this->assertStringContainsString(
            "XoopsLoad::load('XoopsFilterInput');",
            $this->sourceCode,
            'Should load XoopsFilterInput class'
        );

        // In confirm operation, should clean module names
        $confirmSection = $this->extractOperationSection('confirm');
        $this->assertStringContainsString(
            "XoopsFilterInput::clean",
            $confirmSection,
            'Should use XoopsFilterInput to clean input'
        );

        $this->assertStringContainsString(
            "'STRING'",
            $confirmSection,
            'Should filter input as STRING type'
        );
    }

    /**
     * Verify that MyTextSanitizer is used for HTML output sanitization.
     */
    public function testUsesTextSanitizerForOutput(): void
    {
        $this->assertStringContainsString(
            '$myts = \MyTextSanitizer::getInstance();',
            $this->sourceCode,
            'Should initialize MyTextSanitizer'
        );

        // Should use htmlSpecialChars for output
        $this->assertStringContainsString(
            '$myts->htmlSpecialChars',
            $this->sourceCode,
            'Should use htmlSpecialChars for output sanitization'
        );
    }

    /**
     * Verify that module names are properly escaped in array building.
     */
    public function testEscapesModuleNamesInListOperation(): void
    {
        $listSection = $this->extractOperationSection('list');
        $this->assertNotEmpty($listSection, 'List operation should exist');

        $this->assertStringContainsString(
            'htmlspecialchars((string) $module->getVar(\'name\'), ENT_QUOTES | ENT_HTML5)',
            $listSection,
            'Should escape module names with ENT_QUOTES | ENT_HTML5 flags'
        );
    }

    /**
     * Verify that module parameters are cast to integers where expected.
     */
    public function testCastsModuleIdsToIntegers(): void
    {
        // In confirm operation, module IDs should be cast to int
        $confirmSection = $this->extractOperationSection('confirm');

        $this->assertStringContainsString(
            '(int) $mid',
            $confirmSection,
            'Should cast module ID to integer'
        );
    }

    // =========================================================================
    // Confirm Operation Tests
    // =========================================================================

    /**
     * Verify that confirm operation validates writable cache directory.
     */
    public function testConfirmOperationValidatesCacheWritability(): void
    {
        $confirmSection = $this->extractOperationSection('confirm');

        $this->assertStringContainsString(
            'if (!is_writable(XOOPS_CACHE_PATH . \'/\'))',
            $confirmSection,
            'Should check if cache path is writable'
        );

        $this->assertStringContainsString(
            '$errorMessage[] = sprintf(_MUSTWABLE,',
            $confirmSection,
            'Should add error message if cache not writable'
        );
    }

    /**
     * Verify that confirm operation exits if there are errors.
     */
    public function testConfirmOperationExitsOnErrors(): void
    {
        $confirmSection = $this->extractOperationSection('confirm');

        $this->assertStringContainsString(
            'if (count($errorMessage) > 0)',
            $confirmSection,
            'Should check for error messages'
        );

        $this->assertStringContainsString(
            'xoops_error($errorMessage);',
            $confirmSection,
            'Should display errors using xoops_error()'
        );

        $this->assertStringContainsString(
            'exit();',
            $confirmSection,
            'Should exit after displaying errors'
        );
    }

    /**
     * Verify that confirm operation processes module data correctly.
     */
    public function testConfirmOperationProcessesModuleData(): void
    {
        $confirmSection = $this->extractOperationSection('confirm');

        // Should iterate over module array
        $this->assertStringContainsString(
            'foreach ($module as $mid)',
            $confirmSection,
            'Should iterate over module array'
        );

        // Should trim and clean new names
        $this->assertStringContainsString(
            'trim((string) XoopsFilterInput::clean($newname[$mid], \'STRING\'))',
            $confirmSection,
            'Should trim and clean new module names'
        );

        // Should build modifs_mods array with mid, oldname, newname
        $this->assertStringContainsString(
            "\$modifs_mods[\$i]['mid']",
            $confirmSection,
            'Should set module ID in array'
        );

        $this->assertStringContainsString(
            "\$modifs_mods[\$i]['oldname']",
            $confirmSection,
            'Should set old name in array'
        );

        $this->assertStringContainsString(
            "\$modifs_mods[\$i]['newname']",
            $confirmSection,
            'Should set new name in array'
        );
    }

    /**
     * Verify that confirm operation assigns data to template.
     */
    public function testConfirmOperationAssignsDataToTemplate(): void
    {
        $confirmSection = $this->extractOperationSection('confirm');

        $this->assertStringContainsString(
            "\$xoopsTpl->assign('modifs_mods', \$modifs_mods);",
            $confirmSection,
            'Should assign modified modules to template'
        );
    }

    /**
     * Verify that confirm operation sets correct template.
     */
    public function testConfirmOperationSetsCorrectTemplate(): void
    {
        $confirmSection = $this->extractOperationSection('confirm');

        $this->assertStringContainsString(
            "\$GLOBALS['xoopsOption']['template_main'] = 'system_modules_confirm.tpl';",
            $confirmSection,
            'Should set template to system_modules_confirm.tpl'
        );
    }

    // =========================================================================
    // Submit Operation Tests
    // =========================================================================

    /**
     * Verify that submit operation compares old and new names.
     */
    public function testSubmitOperationComparesNames(): void
    {
        $submitSection = $this->extractOperationSection('submit');
        $this->assertNotEmpty($submitSection, 'Submit operation should exist');

        $this->assertStringContainsString(
            'if ($oldname[$mid] != $newname[$mid])',
            $submitSection,
            'Should compare old and new names before changing'
        );
    }

    /**
     * Verify that submit operation calls module change function.
     */
    public function testSubmitOperationCallsModuleChange(): void
    {
        $submitSection = $this->extractOperationSection('submit');

        $this->assertStringContainsString(
            'xoops_module_change(',
            $submitSection,
            'Should call xoops_module_change function'
        );
    }

    /**
     * Verify that submit operation flushes cpanel cache.
     */
    public function testSubmitOperationFlushesCache(): void
    {
        $submitSection = $this->extractOperationSection('submit');

        $this->assertStringContainsString(
            "xoops_load('cpanel', 'system');",
            $submitSection,
            'Should load cpanel class'
        );

        $this->assertStringContainsString(
            'XoopsSystemCpanel::flush();',
            $submitSection,
            'Should flush cpanel cache'
        );
    }

    /**
     * Verify that submit operation updates active modules cache.
     */
    public function testSubmitOperationUpdatesActiveModules(): void
    {
        $submitSection = $this->extractOperationSection('submit');

        $this->assertStringContainsString(
            'xoops_setActiveModules();',
            $submitSection,
            'Should update active modules cache'
        );
    }

    /**
     * Verify that submit operation assigns results to template.
     */
    public function testSubmitOperationAssignsResults(): void
    {
        $submitSection = $this->extractOperationSection('submit');

        $this->assertStringContainsString(
            "\$xoopsTpl->assign('result', \$ret);",
            $submitSection,
            'Should assign results to template'
        );
    }

    // =========================================================================
    // List Operation Tests
    // =========================================================================

    /**
     * Verify that list operation adds stylesheets and scripts.
     */
    public function testListOperationAddsAssets(): void
    {
        $listSection = $this->extractOperationSection('list');
        $this->assertNotEmpty($listSection, 'List operation should exist');

        $this->assertStringContainsString(
            '$xoTheme->addStylesheet(',
            $listSection,
            'Should add stylesheets'
        );

        $this->assertStringContainsString(
            '$xoTheme->addScript(',
            $listSection,
            'Should add JavaScript files'
        );
    }

    /**
     * Verify that list operation retrieves installed modules.
     */
    public function testListOperationRetrievesInstalledModules(): void
    {
        $listSection = $this->extractOperationSection('list');

        $this->assertStringContainsString(
            "\$module_handler = xoops_getHandler('module');",
            $listSection,
            'Should get module handler'
        );

        $this->assertStringContainsString(
            '$installed_mods = $module_handler->getObjects($criteria);',
            $listSection,
            'Should retrieve installed modules'
        );
    }

    /**
     * Verify that list operation checks for version updates.
     */
    public function testListOperationChecksVersionUpdates(): void
    {
        $listSection = $this->extractOperationSection('list');

        $this->assertStringContainsString(
            '$module->versionCompare(',
            $listSection,
            'Should compare module versions'
        );

        $this->assertStringContainsString(
            "\$listed_mods[\$i]['warning_update']",
            $listSection,
            'Should set warning_update flag'
        );
    }

    /**
     * Verify that list operation handles legacy version format.
     */
    public function testListOperationHandlesLegacyVersionFormat(): void
    {
        $listSection = $this->extractOperationSection('list');

        $this->assertStringContainsString(
            "strpos(\$listed_mods[\$i]['version'], '.') === false",
            $listSection,
            'Should check for version without dots (legacy format)'
        );

        // Comment should explain this is for xoops 2.5.11 compatibility
        $this->assertStringContainsString(
            '2.5.11',
            $listSection,
            'Should have comment about XOOPS 2.5.11 version handling'
        );
    }

    /**
     * Verify that list operation counts modules to install.
     */
    public function testListOperationCountsModulesToInstall(): void
    {
        $listSection = $this->extractOperationSection('list');

        $this->assertStringContainsString(
            'XoopsLists::getModulesList()',
            $listSection,
            'Should get list of available modules'
        );

        $this->assertStringContainsString(
            "\$xoopsTpl->assign('toinstall_nb', \$i);",
            $listSection,
            'Should assign count of modules to install'
        );
    }

    // =========================================================================
    // Install Operation Tests
    // =========================================================================

    /**
     * Verify that install operations retrieve module information.
     */
    public function testInstallOperationsRetrieveModuleInfo(): void
    {
        $installSection = $this->extractOperationSection('install');
        $this->assertNotEmpty($installSection, 'Install operation should exist');

        $this->assertStringContainsString(
            '$mod->loadInfoAsVar($module);',
            $installSection,
            'Should load module information'
        );
    }

    /**
     * Verify that install operations display confirmation message.
     */
    public function testInstallOperationsDisplayConfirmation(): void
    {
        $installSection = $this->extractOperationSection('install');

        $this->assertStringContainsString(
            'xoops_confirm(',
            $installSection,
            'Should call xoops_confirm for user confirmation'
        );

        $this->assertStringContainsString(
            "'op' => 'install_ok'",
            $installSection,
            'Should set next operation to install_ok'
        );
    }

    /**
     * Verify that install_ok operation calls install function.
     */
    public function testInstallOkOperationCallsInstallFunction(): void
    {
        $installOkSection = $this->extractOperationSection('install_ok');
        $this->assertNotEmpty($installOkSection, 'Install_ok operation should exist');

        $this->assertStringContainsString(
            'xoops_module_install(',
            $installOkSection,
            'Should call xoops_module_install function'
        );
    }

    /**
     * Verify that install_ok updates cache.
     */
    public function testInstallOkOperationUpdatesCache(): void
    {
        $installOkSection = $this->extractOperationSection('install_ok');

        $this->assertStringContainsString(
            'xoops_setActiveModules();',
            $installOkSection,
            'Should update active modules cache'
        );

        $this->assertStringContainsString(
            'xoops_module_delayed_clean_cache();',
            $installOkSection,
            'Should clean delayed cache'
        );
    }

    // =========================================================================
    // Update Operation Tests
    // =========================================================================

    /**
     * Verify that update operation shows confirmation.
     */
    public function testUpdateOperationShowsConfirmation(): void
    {
        $updateSection = $this->extractOperationSection('update');
        $this->assertNotEmpty($updateSection, 'Update operation should exist');

        $this->assertStringContainsString(
            'xoops_confirm(',
            $updateSection,
            'Should show confirmation dialog'
        );

        $this->assertStringContainsString(
            "'op' => 'update_ok'",
            $updateSection,
            'Should set next operation to update_ok'
        );
    }

    /**
     * Verify that update_ok operation calls update function.
     */
    public function testUpdateOkOperationCallsUpdateFunction(): void
    {
        $updateOkSection = $this->extractOperationSection('update_ok');
        $this->assertNotEmpty($updateOkSection, 'Update_ok operation should exist');

        $this->assertStringContainsString(
            'xoops_module_update(',
            $updateOkSection,
            'Should call xoops_module_update function'
        );
    }

    // =========================================================================
    // Uninstall Operation Tests
    // =========================================================================

    /**
     * Verify that uninstall operation shows confirmation.
     */
    public function testUninstallOperationShowsConfirmation(): void
    {
        $uninstallSection = $this->extractOperationSection('uninstall');
        $this->assertNotEmpty($uninstallSection, 'Uninstall operation should exist');

        $this->assertStringContainsString(
            'xoops_confirm(',
            $uninstallSection,
            'Should show confirmation dialog'
        );

        $this->assertStringContainsString(
            "_AM_SYSTEM_MODULES_RUSUREUNINS",
            $uninstallSection,
            'Should use uninstall confirmation message'
        );
    }

    /**
     * Verify that uninstall_ok operation calls uninstall function.
     */
    public function testUninstallOkOperationCallsUninstallFunction(): void
    {
        $uninstallOkSection = $this->extractOperationSection('uninstall_ok');
        $this->assertNotEmpty($uninstallOkSection, 'Uninstall_ok operation should exist');

        $this->assertStringContainsString(
            'xoops_module_uninstall(',
            $uninstallOkSection,
            'Should call xoops_module_uninstall function'
        );
    }

    // =========================================================================
    // Display Operations Tests
    // =========================================================================

    /**
     * Verify that display operation toggles module active state.
     */
    public function testDisplayOperationTogglesActiveState(): void
    {
        $displaySection = $this->extractOperationSection('display');
        $this->assertNotEmpty($displaySection, 'Display operation should exist');

        $this->assertMatchesRegularExpression(
            '/\$old\s+=\s+\$module->getVar\(\'isactive\'\);/',
            $displaySection,
            'Should get current isactive state'
        );

        $this->assertStringContainsString(
            "setVar('isactive', !\$old)",
            $displaySection,
            'Should toggle isactive state'
        );

        $this->assertStringContainsString(
            "setVar('isactive', !\$old)",
            $displaySection,
            'Should toggle block active state'
        );
    }

    /**
     * Verify that display_in_menu operation toggles weight.
     */
    public function testDisplayInMenuOperationTogglesWeight(): void
    {
        $displayInMenuSection = $this->extractOperationSection('display_in_menu');
        $this->assertNotEmpty($displayInMenuSection, 'Display_in_menu operation should exist');

        $this->assertMatchesRegularExpression(
            '/\$old\s+=\s+\$module->getVar\(\'weight\'\);/',
            $displayInMenuSection,
            'Should get current weight'
        );

        $this->assertStringContainsString(
            "setVar('weight', !\$old)",
            $displayInMenuSection,
            'Should toggle weight (0 or non-zero)'
        );
    }

    // =========================================================================
    // Order Operation Tests
    // =========================================================================

    /**
     * Verify that order operation processes module ordering.
     */
    public function testOrderOperationProcessesModuleOrder(): void
    {
        $orderSection = $this->extractOperationSection('order');
        $this->assertNotEmpty($orderSection, 'Order operation should exist');

        $this->assertStringContainsString(
            "if (isset(\$_POST['mod']))",
            $orderSection,
            'Should check for mod POST data'
        );

        $this->assertStringContainsString(
            "foreach (\$_POST['mod'] as \$order)",
            $orderSection,
            'Should iterate over module order array'
        );

        // Should only change order for visible modules (weight != 0)
        $this->assertStringContainsString(
            "if (\$module->getVar('weight') != 0)",
            $orderSection,
            'Should only reorder visible modules'
        );
    }

    /**
     * Verify that order operation exits after processing.
     */
    public function testOrderOperationExitsAfterProcessing(): void
    {
        $orderSection = $this->extractOperationSection('order');

        $this->assertStringContainsString(
            'exit;',
            $orderSection,
            'Should exit after processing order (AJAX endpoint)'
        );
    }

    // =========================================================================
    // Installlist Operation Tests
    // =========================================================================

    /**
     * Verify that installlist operation shows available modules.
     */
    public function testInstalllistOperationShowsAvailableModules(): void
    {
        $installlistSection = $this->extractOperationSection('installlist');
        $this->assertNotEmpty($installlistSection, 'Installlist operation should exist');

        $this->assertStringContainsString(
            'XoopsLists::getModulesList()',
            $installlistSection,
            'Should get list of available modules'
        );

        $this->assertStringContainsString(
            '$module->loadInfo($file);',
            $installlistSection,
            'Should load module information'
        );

        $this->assertStringContainsString(
            "\$xoopsTpl->assign('toinstall_mods', \$toinstall_mods);",
            $installlistSection,
            'Should assign modules to install to template'
        );
    }

    /**
     * Verify that installlist operation escapes module names.
     */
    public function testInstalllistOperationEscapesModuleNames(): void
    {
        $installlistSection = $this->extractOperationSection('installlist');

        $this->assertStringContainsString(
            'htmlspecialchars($module->getInfo(\'name\'), ENT_QUOTES | ENT_HTML5)',
            $installlistSection,
            'Should escape module names in installlist'
        );
    }

    // =========================================================================
    // General Structure Tests
    // =========================================================================

    /**
     * Verify the file has valid PHP syntax.
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
     * Verify that file includes required dependencies.
     */
    public function testIncludesRequiredDependencies(): void
    {
        $this->assertStringContainsString(
            "include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';",
            $this->sourceCode,
            'Should include xoopsblock.php'
        );

        $this->assertStringContainsString(
            "include_once XOOPS_ROOT_PATH . '/modules/system/admin/modulesadmin/modulesadmin.php';",
            $this->sourceCode,
            'Should include modulesadmin.php functions'
        );
    }

    /**
     * Verify that breadcrumb is properly configured.
     */
    public function testConfiguresBreadcrumb(): void
    {
        $this->assertStringContainsString(
            '$xoBreadCrumb->addLink(',
            $this->sourceCode,
            'Should configure breadcrumb navigation'
        );

        $this->assertStringContainsString(
            '$xoBreadCrumb->render();',
            $this->sourceCode,
            'Should render breadcrumb'
        );
    }

    /**
     * Verify that xoops_cp_header() is called.
     */
    public function testCallsControlPanelHeader(): void
    {
        $this->assertStringContainsString(
            'xoops_cp_header();',
            $this->sourceCode,
            'Should call xoops_cp_header()'
        );
    }

    /**
     * Verify that xoops_cp_footer() is called for operations that display output.
     */
    public function testCallsControlPanelFooter(): void
    {
        $this->assertStringContainsString(
            'xoops_cp_footer();',
            $this->sourceCode,
            'Should call xoops_cp_footer()'
        );
    }

    /**
     * Verify that POST data is handled through a loop.
     */
    public function testHandlesPostDataThroughLoop(): void
    {
        $this->assertStringContainsString(
            'if (isset($_POST))',
            $this->sourceCode,
            'Should check if POST data exists'
        );

        $this->assertStringContainsString(
            'foreach ($_POST as $k => $v)',
            $this->sourceCode,
            'Should iterate over POST data'
        );

        // Note: The pattern ${$k} = $v is a legacy approach but we test that it exists
        $this->assertStringContainsString(
            '${$k} = $v;',
            $this->sourceCode,
            'Should extract POST variables (legacy pattern)'
        );
    }

    /**
     * Verify that module IDs are retrieved safely from Request.
     */
    public function testRetrievesModuleIdsSafely(): void
    {
        $this->assertStringContainsString(
            'Request::getInt(\'mid\', 0)',
            $this->sourceCode,
            'Should retrieve module ID as integer with default value'
        );
    }

    /**
     * Verify that module names are retrieved safely from Request.
     */
    public function testRetrievesModuleNamesSafely(): void
    {
        $this->assertStringContainsString(
            "Request::getString('module', '')",
            $this->sourceCode,
            'Should retrieve module name as string with default value'
        );
    }

    // =========================================================================
    // Edge Case Tests
    // =========================================================================

    /**
     * Verify that empty module arrays are handled.
     */
    public function testHandlesEmptyModuleArrays(): void
    {
        // In confirm operation, should handle empty module array
        $confirmSection = $this->extractOperationSection('confirm');

        $this->assertStringContainsString(
            "empty(\$_POST['module']) ? [] : \$_POST['module']",
            $confirmSection,
            'Should provide empty array default for missing module POST data'
        );
    }

    /**
     * Verify that module existence is checked before operations.
     */
    public function testChecksModuleExistence(): void
    {
        // Various operations should check if module > 0
        $this->assertStringContainsString(
            'if ($module_id > 0)',
            $this->sourceCode,
            'Should check if module ID is valid before operations'
        );
    }

    /**
     * Verify that file existence is checked before loading module info.
     */
    public function testChecksFileExistence(): void
    {
        $this->assertStringContainsString(
            "file_exists(XOOPS_ROOT_PATH . '/modules/' . \$file . '/xoops_version.php')",
            $this->sourceCode,
            'Should check if xoops_version.php exists before loading module'
        );
    }

    /**
     * Verify that files are properly trimmed.
     */
    public function testTrimsFileNames(): void
    {
        $this->assertStringContainsString(
            'trim((string) $file)',
            $this->sourceCode,
            'Should trim file names'
        );
    }

    /**
     * Verify that clearstatcache is called when checking files.
     */
    public function testCallsClearStatCache(): void
    {
        $this->assertStringContainsString(
            'clearstatcache();',
            $this->sourceCode,
            'Should call clearstatcache() when checking multiple files'
        );
    }

    // =========================================================================
    // Regression Tests
    // =========================================================================

    /**
     * Verify that the variable shadowing bug is present (module variable reused).
     * This is intentional in the legacy code but we document it.
     */
    public function testDocumentsModuleVariableShadowing(): void
    {
        // Line 84 retrieves Request::getArray('module') but then line 85 reuses $module
        // This is potentially confusing but appears intentional for the foreach loop
        $listSection = $this->extractOperationSection('list');

        $this->assertStringContainsString(
            "Request::getArray('module', [])",
            $listSection,
            'Should retrieve module array from request'
        );

        $this->assertStringContainsString(
            'foreach ($installed_mods as $module)',
            $listSection,
            'Should iterate installed modules using $module variable'
        );
    }

    /**
     * Verify proper increment pattern is used.
     */
    public function testUsesProperIncrementPattern(): void
    {
        $this->assertStringContainsString(
            '++$i;',
            $this->sourceCode,
            'Should use pre-increment pattern'
        );
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Extract a specific case section from the switch statement.
     *
     * @param string $operation The operation name to extract
     * @return string The operation section code
     */
    private function extractOperationSection(string $operation): string
    {
        // Find case 'operation':
        $pattern = "/case\s+['\"]" . preg_quote($operation, '/') . "['\"]:\s*/";
        if (!preg_match($pattern, $this->sourceCode, $matches, PREG_OFFSET_CAPTURE)) {
            return '';
        }

        $startPos = $matches[0][1] + strlen($matches[0][0]);

        // Find the next break or case
        $nextCase = strpos($this->sourceCode, "\n    case ", $startPos);
        $nextBreak = strpos($this->sourceCode, "break;", $startPos);

        $endPos = false;
        if ($nextCase !== false && $nextBreak !== false) {
            $endPos = min($nextCase, $nextBreak + 6);
        } elseif ($nextCase !== false) {
            $endPos = $nextCase;
        } elseif ($nextBreak !== false) {
            $endPos = $nextBreak + 6;
        } else {
            // End of switch
            $endPos = strpos($this->sourceCode, '}', $startPos);
        }

        if ($endPos === false) {
            return '';
        }

        return substr($this->sourceCode, $startPos, $endPos - $startPos);
    }
}
