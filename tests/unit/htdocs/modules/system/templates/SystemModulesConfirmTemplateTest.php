<?php
/**
 * Unit tests for modules/system/templates/admin/system_modules_confirm.tpl
 *
 * Tests the module confirmation template structure, variable usage,
 * security token handling, and output logic.
 *
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      Tests\Unit\System\Templates
 */

declare(strict_types=1);

namespace Tests\Unit\System\Templates;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/SourceFileTestTrait.php';
use Tests\Unit\System\SourceFileTestTrait;

/**
 * Tests for system_modules_confirm.tpl template
 *
 * These tests verify the template structure, Smarty syntax, variable usage,
 * and security token handling in the module confirmation template.
 */
class SystemModulesConfirmTemplateTest extends TestCase
{
    use SourceFileTestTrait;

    /**
     * @var string Alias for sourceContent (kept for readability)
     */
    private string $templateContent;

    protected function setUp(): void
    {
        $this->loadSourceFile('htdocs/modules/system/templates/admin/system_modules_confirm.tpl');
        $this->templateContent = $this->sourceContent;
    }

    // =========================================================================
    // Template Structure Tests
    // =========================================================================

    /**
     * Verify that template includes system header.
     */
    public function testIncludesSystemHeader(): void
    {
        $this->assertStringContainsString(
            '{include file="db:system_header.tpl"}',
            $this->templateContent,
            'Template should include system_header.tpl'
        );
    }

    /**
     * Verify that template uses Smarty syntax correctly.
     */
    public function testUsesSmartyDelimiters(): void
    {
        $this->assertMatchesRegularExpression(
            '/<\{[^}]+\}>/',
            $this->templateContent,
            'Template should use Smarty delimiters <{ }>'
        );
    }

    /**
     * Verify that template has valid HTML structure.
     */
    public function testHasValidHtmlStructure(): void
    {
        // Should have opening and closing tags properly matched
        $this->assertStringContainsString('<form', $this->templateContent, 'Should have form tag');
        $this->assertStringContainsString('</form>', $this->templateContent, 'Should close form tag');
        $this->assertStringContainsString('<table', $this->templateContent, 'Should have table tag');
        $this->assertStringContainsString('</table>', $this->templateContent, 'Should close table tag');
    }

    // =========================================================================
    // Conditional Logic Tests
    // =========================================================================

    /**
     * Verify that template has two main conditional branches.
     */
    public function testHasTwoMainConditionalBranches(): void
    {
        $this->assertStringContainsString(
            '{if isset($modifs_mods)}',
            $this->templateContent,
            'Should check if modifs_mods is set (confirmation state)'
        );

        $this->assertStringContainsString(
            '{else}',
            $this->templateContent,
            'Should have else clause for result state'
        );

        $this->assertStringContainsString(
            '{/if}',
            $this->templateContent,
            'Should close if statement'
        );
    }

    /**
     * Verify that modifs_mods branch shows confirmation form.
     */
    public function testModifsModsbranchShowsConfirmationForm(): void
    {
        // Extract the modifs_mods branch
        $modifsStart = strpos($this->templateContent, '{if isset($modifs_mods)}');
        $elsePos = strpos($this->templateContent, '{else}', $modifsStart);
        $modifsBranch = substr($this->templateContent, $modifsStart, $elsePos - $modifsStart);

        $this->assertStringContainsString(
            '<form',
            $modifsBranch,
            'Confirmation branch should contain form'
        );

        $this->assertStringContainsString(
            'action="admin.php"',
            $modifsBranch,
            'Form should submit to admin.php'
        );

        $this->assertStringContainsString(
            'method="post"',
            $modifsBranch,
            'Form should use POST method'
        );
    }

    /**
     * Verify that else branch shows results.
     */
    public function testElseBranchShowsResults(): void
    {
        // Find the main {else} that separates confirmation form from results
        // Use the closing </form> tag to find the right else branch
        $formEnd = strpos($this->templateContent, '</form>');
        $this->assertNotFalse($formEnd, 'Should have closing form tag');
        $elsePos = strpos($this->templateContent, '{else}', $formEnd);
        $this->assertNotFalse($elsePos, 'Should have else branch after form');
        $endifPos = strpos($this->templateContent, '{/if}', $elsePos);
        $elseBranch = substr($this->templateContent, $elsePos, $endifPos - $elsePos);

        $this->assertStringContainsString(
            '{if isset($result)}',
            $elseBranch,
            'Result branch should check if result is set'
        );

        $this->assertStringContainsString(
            'xo-module-log',
            $elseBranch,
            'Result branch should have module log div'
        );
    }

    // =========================================================================
    // Confirmation Form Tests
    // =========================================================================

    /**
     * Verify that confirmation form iterates over modifs_mods.
     */
    public function testFormIteratesOverModifications(): void
    {
        $this->assertStringContainsString(
            '{foreach item=row from=$modifs_mods|default:null}',
            $this->templateContent,
            'Should iterate over modifs_mods array with default'
        );

        $this->assertStringContainsString(
            '{/foreach}',
            $this->templateContent,
            'Should close foreach loop'
        );
    }

    /**
     * Verify that form displays old and new module names.
     */
    public function testFormDisplaysOldAndNewNames(): void
    {
        $this->assertStringContainsString(
            '{$row.oldname}',
            $this->templateContent,
            'Should display old module name'
        );

        $this->assertStringContainsString(
            '{$row.newname}',
            $this->templateContent,
            'Should display new module name'
        );
    }

    /**
     * Verify that form highlights changed names.
     */
    public function testFormHighlightsChangedNames(): void
    {
        $this->assertStringContainsString(
            '{if $row.oldname != $row.newname}',
            $this->templateContent,
            'Should check if names are different'
        );

        $this->assertStringContainsString(
            'class="bold red"',
            $this->templateContent,
            'Should highlight changed names with bold red class'
        );

        $this->assertStringContainsString(
            '&raquo;',
            $this->templateContent,
            'Should use arrow symbol to indicate change'
        );
    }

    /**
     * Verify that form includes hidden fields for module data.
     */
    public function testFormIncludesHiddenFields(): void
    {
        $this->assertStringContainsString(
            '<input type="hidden" name="module[]"',
            $this->templateContent,
            'Should include hidden module array field'
        );

        $this->assertStringContainsString(
            'name="oldname[<{$row.mid}>]"',
            $this->templateContent,
            'Should include hidden oldname field with module ID index'
        );

        $this->assertStringContainsString(
            'name="newname[<{$row.mid}>]"',
            $this->templateContent,
            'Should include hidden newname field with module ID index'
        );

        $this->assertStringContainsString(
            'value="<{$row.mid}>"',
            $this->templateContent,
            'Should include module ID value'
        );
    }

    /**
     * Verify that form includes operation hidden field.
     */
    public function testFormIncludesOperationField(): void
    {
        $this->assertStringContainsString(
            '<input type="hidden" name="op" value="submit"',
            $this->templateContent,
            'Should set operation to submit'
        );

        $this->assertStringContainsString(
            '<input type="hidden" name="fct" value="modulesadmin"',
            $this->templateContent,
            'Should set fct to modulesadmin'
        );
    }

    /**
     * Verify that form includes security token.
     */
    public function testFormIncludesSecurityToken(): void
    {
        $this->assertStringContainsString(
            '{$input_security}',
            $this->templateContent,
            'Should include security token from controller'
        );
    }

    /**
     * Verify that form has submit and cancel buttons.
     */
    public function testFormHasSubmitAndCancelButtons(): void
    {
        $this->assertStringContainsString(
            'type="submit"',
            $this->templateContent,
            'Should have submit button'
        );

        $this->assertStringContainsString(
            '{$smarty.const._AM_SYSTEM_MODULES_SUBMIT}',
            $this->templateContent,
            'Should use submit constant for button label'
        );

        $this->assertStringContainsString(
            'type="button"',
            $this->templateContent,
            'Should have cancel button'
        );

        $this->assertStringContainsString(
            '{$smarty.const._AM_SYSTEM_MODULES_CANCEL}',
            $this->templateContent,
            'Should use cancel constant for button label'
        );

        $this->assertStringContainsString(
            "onclick=\"location='admin.php?fct=modulesadmin'\"",
            $this->templateContent,
            'Cancel button should redirect to module admin'
        );
    }

    // =========================================================================
    // Result Display Tests
    // =========================================================================

    /**
     * Verify that result section iterates over result array.
     */
    public function testResultSectionIteratesOverResults(): void
    {
        $this->assertStringContainsString(
            '{foreach item=row from=$result|default:null}',
            $this->templateContent,
            'Should iterate over result array with default'
        );
    }

    /**
     * Verify that result section displays each result row.
     */
    public function testResultSectionDisplaysRows(): void
    {
        $this->assertStringContainsString(
            '<div class="spacer"><{$row}></div>',
            $this->templateContent,
            'Should display each result row in spacer div'
        );
    }

    /**
     * Verify that result section has logger wrapper.
     */
    public function testResultSectionHasLoggerWrapper(): void
    {
        $this->assertStringContainsString(
            '<div class="logger">',
            $this->templateContent,
            'Should wrap results in logger div'
        );
    }

    /**
     * Verify that result section has back link.
     */
    public function testResultSectionHasBackLink(): void
    {
        $this->assertStringContainsString(
            '<a href="admin.php?fct=modulesadmin">',
            $this->templateContent,
            'Should have link back to module admin'
        );

        $this->assertStringContainsString(
            '{$smarty.const._AM_SYSTEM_MODULES_BTOMADMIN}',
            $this->templateContent,
            'Should use back to admin constant for link text'
        );
    }

    // =========================================================================
    // Table Structure Tests
    // =========================================================================

    /**
     * Verify that table has proper structure with thead, tbody, tfoot.
     */
    public function testTableHasProperStructure(): void
    {
        $this->assertStringContainsString(
            '<thead>',
            $this->templateContent,
            'Table should have thead section'
        );

        $this->assertStringContainsString(
            '</thead>',
            $this->templateContent,
            'Table should close thead section'
        );

        $this->assertStringContainsString(
            '<tbody>',
            $this->templateContent,
            'Table should have tbody section'
        );

        $this->assertStringContainsString(
            '</tbody>',
            $this->templateContent,
            'Table should close tbody section'
        );

        $this->assertStringContainsString(
            '<tfoot>',
            $this->templateContent,
            'Table should have tfoot section'
        );

        $this->assertStringContainsString(
            '</tfoot>',
            $this->templateContent,
            'Table should close tfoot section'
        );
    }

    /**
     * Verify that table uses outer class for styling.
     */
    public function testTableUsesOuterClass(): void
    {
        $this->assertStringContainsString(
            'class="outer"',
            $this->templateContent,
            'Table should use outer class'
        );
    }

    /**
     * Verify that table header displays module label.
     */
    public function testTableHeaderDisplaysModuleLabel(): void
    {
        $this->assertStringContainsString(
            '{$smarty.const._AM_SYSTEM_MODULES_MODULE}',
            $this->templateContent,
            'Table header should display module constant'
        );
    }

    /**
     * Verify that table rows use cycle for alternating styles.
     */
    public function testTableRowsUseCycleForAlternating(): void
    {
        $this->assertStringContainsString(
            "{cycle values='odd, even'}",
            $this->templateContent,
            'Table rows should cycle between odd and even classes'
        );
    }

    /**
     * Verify that table has centered text classes.
     */
    public function testTableHasCenteredTextClasses(): void
    {
        $this->assertStringContainsString(
            'class="txtcenter',
            $this->templateContent,
            'Table should use txtcenter class for centered text'
        );
    }

    // =========================================================================
    // Smarty Constants Tests
    // =========================================================================

    /**
     * Verify that template uses Smarty constants for localization.
     */
    public function testUsesSmartyConstants(): void
    {
        $expectedConstants = [
            '_AM_SYSTEM_MODULES_MODULE',
            '_AM_SYSTEM_MODULES_SUBMIT',
            '_AM_SYSTEM_MODULES_CANCEL',
            '_AM_SYSTEM_MODULES_BTOMADMIN',
        ];

        foreach ($expectedConstants as $constant) {
            $this->assertStringContainsString(
                '{$smarty.const.' . $constant . '}',
                $this->templateContent,
                "Template should use constant: {$constant}"
            );
        }
    }

    // =========================================================================
    // Security Tests
    // =========================================================================

    /**
     * Verify that template does not contain hardcoded sensitive data.
     */
    public function testDoesNotContainHardcodedSensitiveData(): void
    {
        $this->assertStringNotContainsString(
            'password',
            strtolower($this->templateContent),
            'Template should not contain hardcoded passwords'
        );
    }

    /**
     * Verify that form uses POST method for security.
     */
    public function testFormUsesPostMethod(): void
    {
        $this->assertMatchesRegularExpression(
            '/method\s*=\s*["\']post["\']/i',
            $this->templateContent,
            'Form should use POST method'
        );
    }

    // =========================================================================
    // Accessibility Tests
    // =========================================================================

    /**
     * Verify that form buttons have proper classes.
     */
    public function testFormButtonsHaveProperClasses(): void
    {
        $this->assertStringContainsString(
            'class="formButton"',
            $this->templateContent,
            'Form buttons should have formButton class'
        );
    }

    /**
     * Verify that table has cellspacing attribute.
     */
    public function testTableHasCellspacing(): void
    {
        $this->assertStringContainsString(
            'cellspacing="1"',
            $this->templateContent,
            'Table should have cellspacing for visual separation'
        );
    }

    // =========================================================================
    // Edge Case Tests
    // =========================================================================

    /**
     * Verify that template uses default filter for null values.
     */
    public function testUsesDefaultFilterForNullValues(): void
    {
        $this->assertStringContainsString(
            '|default:null',
            $this->templateContent,
            'Template should use default:null filter for safe iteration'
        );
    }

    /**
     * Verify that template properly closes all Smarty blocks.
     */
    public function testProperlyClosesAllSmartyBlocks(): void
    {
        // Count opening and closing foreach tags
        $foreachCount = substr_count($this->templateContent, '{foreach');
        $endForeachCount = substr_count($this->templateContent, '{/foreach}');

        $this->assertEquals(
            $foreachCount,
            $endForeachCount,
            'All {foreach} blocks should be closed with {/foreach}'
        );

        // Count opening and closing if tags
        $ifCount = substr_count($this->templateContent, '{if ');
        $endIfCount = substr_count($this->templateContent, '{/if}');

        $this->assertEquals(
            $ifCount,
            $endIfCount,
            'All {if} blocks should be closed with {/if}'
        );
    }

    /**
     * Verify that template does not have PHP code.
     */
    public function testDoesNotContainPhpCode(): void
    {
        $this->assertStringNotContainsString(
            '<?php',
            $this->templateContent,
            'Template should not contain PHP code'
        );

        $this->assertStringNotContainsString(
            '<?=',
            $this->templateContent,
            'Template should not contain short PHP tags'
        );
    }

    // =========================================================================
    // Regression Tests
    // =========================================================================

    /**
     * Verify that footer td uses colspan matching the number of table columns.
     */
    public function testFooterUsesProperColspan(): void
    {
        // Count the number of <th> elements in thead to determine column count
        preg_match_all('/<th>/', $this->templateContent, $thMatches);
        $columnCount = count($thMatches[0]);

        if ($columnCount > 1) {
            $this->assertStringContainsString(
                'colspan="' . $columnCount . '"',
                $this->templateContent,
                "Footer should use colspan=\"{$columnCount}\" to span all columns"
            );
        } else {
            // Single column — colspan is unnecessary
            $this->assertStringNotContainsString(
                'colspan="3"',
                $this->templateContent,
                'Footer should not use stale colspan="3" for single-column table'
            );
        }
    }

    /**
     * Verify that template uses proper CSS classes.
     */
    public function testUsesProperCssClasses(): void
    {
        $expectedClasses = [
            'outer',
            'txtcenter',
            'bold',
            'red',
            'spacer',
            'logger',
            'formButton',
            'foot',
        ];

        foreach ($expectedClasses as $class) {
            $this->assertStringContainsString(
                $class,
                $this->templateContent,
                "Template should use class: {$class}"
            );
        }
    }

    /**
     * Verify that JavaScript onclick is properly quoted.
     */
    public function testJavascriptOnclickIsProperlyQuoted(): void
    {
        $this->assertMatchesRegularExpression(
            '/onclick\s*=\s*"[^"]*location\s*=\s*\'[^\']+\'[^"]*"/',
            $this->templateContent,
            'JavaScript onclick should use proper quote nesting'
        );
    }

    // =========================================================================
    // Layout Consistency Tests
    // =========================================================================

    /**
     * Verify that template maintains consistent indentation.
     */
    public function testMaintainsConsistentIndentation(): void
    {
        $lines = explode("\n", $this->templateContent);
        $indentedLines = 0;

        foreach ($lines as $line) {
            if (preg_match('/^\s{4,}/', $line)) {
                $indentedLines++;
            }
        }

        $this->assertGreaterThan(
            0,
            $indentedLines,
            'Template should use indentation for readability'
        );
    }

    /**
     * Verify that template does not have trailing whitespace issues.
     */
    public function testDoesNotHaveTrailingWhitespaceIssues(): void
    {
        // Template should end with a newline
        $this->assertMatchesRegularExpression(
            '/\n$/',
            $this->templateContent,
            'Template should end with a newline'
        );
    }

    // =========================================================================
    // Additional Coverage Tests
    // =========================================================================

    /**
     * Verify that hidden fields use proper value escaping.
     */
    public function testHiddenFieldsUseProperValueEscaping(): void
    {
        $this->assertStringContainsString(
            'value="<{$row.oldname}>"',
            $this->templateContent,
            'Should escape oldname in hidden field value'
        );

        $this->assertStringContainsString(
            'value="<{$row.newname}>"',
            $this->templateContent,
            'Should escape newname in hidden field value'
        );
    }

    /**
     * Verify that module log has proper ID for styling.
     */
    public function testModuleLogHasProperId(): void
    {
        $this->assertStringContainsString(
            'id="xo-module-log"',
            $this->templateContent,
            'Module log should have xo-module-log ID'
        );
    }

    /**
     * Verify that result display wraps HTML safely.
     */
    public function testResultDisplayWrapsHtmlSafely(): void
    {
        // Result rows display $row directly, which should contain pre-formatted HTML
        // from the controller
        $this->assertStringContainsString(
            '{$row}',
            $this->templateContent,
            'Should display result row content (HTML from controller)'
        );
    }

    /**
     * Test that template handles empty result arrays gracefully.
     */
    public function testHandlesEmptyResultArrays(): void
    {
        // The {if isset($result)} check ensures empty arrays are handled
        $this->assertStringContainsString(
            '{if isset($result)}',
            $this->templateContent,
            'Should check if result exists before displaying'
        );
    }
}
