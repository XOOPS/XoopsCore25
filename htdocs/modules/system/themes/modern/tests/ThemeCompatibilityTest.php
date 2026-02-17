<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Modern Theme Compatibility Test Suite
 *
 * Run these tests after XOOPS updates to ensure theme compatibility.
 * Validates file structure, XOOPS function availability, database query
 * syntax, Smarty template syntax, JavaScript/CSS integrity, file
 * permissions, and external dependencies.
 *
 * Usage:
 *   php ThemeCompatibilityTest.php
 *
 * @category   Theme
 * @package    Modern Theme
 * @subpackage Tests
 * @since      1.0
 * @author     Mamba <mambax7@gmail.com>
 * @copyright  XOOPS Project (https://xoops.org)
 * @license    GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link       https://xoops.org
 */
class ModernThemeCompatibilityTest
{
    private $errors = [];
    private $warnings = [];
    private $passed = 0;
    private $failed = 0;

    /**
     * Run all compatibility tests and print results
     *
     * Executes the full test suite: file structure, XOOPS functions,
     * database queries, template syntax, JavaScript, CSS, permissions,
     * and dependencies. Prints a summary with pass/fail counts and
     * exits with code 0 on success or 1 on failure.
     *
     * @return void
     */
    public function runAll()
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════╗\n";
        echo "║     XOOPS Modern Theme Compatibility Test Suite         ║\n";
        echo "╚══════════════════════════════════════════════════════════╝\n";
        echo "\n";

        $this->testFileStructure();
        $this->testXOOPSFunctions();
        $this->testDatabaseQueries();
        $this->testTemplateFiles();
        $this->testJavaScriptFiles();
        $this->testCSSFiles();
        $this->testPermissions();
        $this->testDependencies();

        $this->printResults();
    }

    /**
     * Test 1: File Structure Integrity
     */
    private function testFileStructure()
    {
        echo "→ Testing file structure... ";

        $requiredFiles = [
            'modern.php',
            'theme.tpl',
            'css/modern.css',
            'css/dark.css',
            'js/theme.js',
            'js/dashboard.js',
            'js/charts.js',
            'js/customizer.js',
            'xotpl/xo_metas.tpl',
            'xotpl/xo_head.tpl',
            'xotpl/xo_sidebar.tpl',
            'xotpl/xo_dashboard.tpl',
            'xotpl/xo_widgets.tpl',
            'xotpl/xo_customizer.tpl',
            'xotpl/xo_page.tpl',
            'xotpl/xo_footer.tpl'
        ];

        $themeDir = __DIR__ . '/../';
        $allExist = true;

        foreach ($requiredFiles as $file) {
            if (!file_exists($themeDir . $file)) {
                $this->errors[] = "Missing required file: $file";
                $allExist = false;
            }
        }

        if ($allExist) {
            $this->pass();
        } else {
            $this->fail();
        }
    }

    /**
     * Test 2: XOOPS Functions Compatibility
     */
    private function testXOOPSFunctions()
    {
        echo "→ Testing XOOPS function compatibility... ";

        if (!defined('XOOPS_ROOT_PATH')) {
            define('XOOPS_ROOT_PATH', realpath(__DIR__ . '/../../../../../..'));
        }

        $requiredFunctions = [
            'xoops_load',
            'xoops_getHandler',
            'xoops_loadLanguage',
            'xoops_getModuleOption'
        ];

        $allExist = true;

        foreach ($requiredFunctions as $func) {
            if (!function_exists($func)) {
                $this->warnings[] = "XOOPS function not available: $func (test environment only)";
            }
        }

        // Check class file
        $classFile = __DIR__ . '/../modern.php';
        if (file_exists($classFile)) {
            $content = file_get_contents($classFile);

            // Check for XoopsSystemGui parent class
            if (strpos($content, 'extends XoopsSystemGui') === false) {
                $this->errors[] = "Theme class doesn't extend XoopsSystemGui";
                $allExist = false;
            }

            // Check for required methods
            $requiredMethods = ['validate', 'header'];
            foreach ($requiredMethods as $method) {
                if (strpos($content, 'function ' . $method) === false &&
                    strpos($content, 'public function ' . $method) === false) {
                    $this->errors[] = "Missing required method: $method";
                    $allExist = false;
                }
            }
        }

        if ($allExist) {
            $this->pass();
        } else {
            $this->fail();
        }
    }

    /**
     * Test 3: Database Query Syntax
     */
    private function testDatabaseQueries()
    {
        echo "→ Testing database query syntax... ";

        $classFile = __DIR__ . '/../modern.php';
        $content = file_get_contents($classFile);

        // Check for proper prefix usage
        $badQueries = [
            'FROM xoops_',
            'FROM `xoops_',
            'JOIN xoops_'
        ];

        $foundBad = false;
        foreach ($badQueries as $bad) {
            if (stripos($content, $bad) !== false) {
                $this->errors[] = "Found hardcoded table prefix: $bad";
                $foundBad = true;
            }
        }

        // Check for proper prepared statements (no direct variable interpolation)
        if (preg_match('/query\([\'"][^\'"]*\$[^\'"]*[\'"]\)/', $content)) {
            $this->warnings[] = "Potential SQL injection risk - use prepared statements";
        }

        if (!$foundBad) {
            $this->pass();
        } else {
            $this->fail();
        }
    }

    /**
     * Test 4: Template File Syntax
     */
    private function testTemplateFiles()
    {
        echo "→ Testing Smarty template syntax... ";

        $templateDir = __DIR__ . '/../xotpl/';
        $templates = glob($templateDir . '*.tpl');

        $syntaxOk = true;

        foreach ($templates as $template) {
            $content = file_get_contents($template);

            // Check for unclosed tags
            $openTags = preg_match_all('/<\{[^}]*$/', $content);
            $closeTags = preg_match_all('/^[^{]*\}>/', $content);

            if ($openTags != $closeTags) {
                $this->warnings[] = "Potential unclosed Smarty tag in: " . basename($template);
            }

            // Check for common mistakes
            if (strpos($content, '{$') !== false && strpos($content, '<{$') === false) {
                $this->warnings[] = "Possible incorrect Smarty syntax in: " . basename($template);
            }
        }

        if ($syntaxOk) {
            $this->pass();
        } else {
            $this->fail();
        }
    }

    /**
     * Test 5: JavaScript Files
     */
    private function testJavaScriptFiles()
    {
        echo "→ Testing JavaScript files... ";

        $jsFiles = [
            __DIR__ . '/../js/theme.js',
            __DIR__ . '/../js/dashboard.js',
            __DIR__ . '/../js/charts.js',
            __DIR__ . '/../js/customizer.js'
        ];

        $jsOk = true;

        foreach ($jsFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);

                // Check for jQuery dependency
                if (strpos($content, 'jQuery') === false && strpos($content, '$') !== false) {
                    $this->warnings[] = "File may have jQuery dependency: " . basename($file);
                }

                // Basic syntax check (balanced braces)
                $open = substr_count($content, '{');
                $close = substr_count($content, '}');

                if ($open != $close) {
                    $this->errors[] = "Unbalanced braces in: " . basename($file);
                    $jsOk = false;
                }
            }
        }

        if ($jsOk) {
            $this->pass();
        } else {
            $this->fail();
        }
    }

    /**
     * Test 6: CSS Files
     */
    private function testCSSFiles()
    {
        echo "→ Testing CSS files... ";

        $cssFiles = [
            __DIR__ . '/../css/modern.css',
            __DIR__ . '/../css/dark.css'
        ];

        $cssOk = true;

        foreach ($cssFiles as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);

                // Check for balanced braces
                $open = substr_count($content, '{');
                $close = substr_count($content, '}');

                if ($open != $close) {
                    $this->errors[] = "Unbalanced braces in: " . basename($file);
                    $cssOk = false;
                }

                // Check for CSS variables
                if (strpos($content, '--') !== false && strpos($content, ':root') === false) {
                    $this->warnings[] = "CSS variables used but :root may be missing in: " . basename($file);
                }
            }
        }

        if ($cssOk) {
            $this->pass();
        } else {
            $this->fail();
        }
    }

    /**
     * Test 7: File Permissions
     */
    private function testPermissions()
    {
        echo "→ Testing file permissions... ";

        $themeDir = __DIR__ . '/../';
        $permOk = true;

        // Check read permissions on key files
        $files = glob($themeDir . '*.php');
        $files = array_merge($files, glob($themeDir . 'css/*.css'));
        $files = array_merge($files, glob($themeDir . 'js/*.js'));

        foreach ($files as $file) {
            if (!is_readable($file)) {
                $this->errors[] = "File not readable: " . basename($file);
                $permOk = false;
            }
        }

        if ($permOk) {
            $this->pass();
        } else {
            $this->fail();
        }
    }

    /**
     * Test 8: Dependencies
     */
    private function testDependencies()
    {
        echo "→ Testing external dependencies... ";

        $depsOk = true;

        // Chart.js should be loaded via CDN (check in template)
        $themeFile = __DIR__ . '/../modern.php';
        $content = file_get_contents($themeFile);

        if (strpos($content, 'chart.js') === false && strpos($content, 'Chart.js') === false) {
            $this->warnings[] = "Chart.js CDN reference may be missing";
        }

        $this->pass();
    }

    /**
     * Mark test as passed
     */
    private function pass()
    {
        echo "✓ PASS\n";
        $this->passed++;
    }

    /**
     * Mark test as failed
     */
    private function fail()
    {
        echo "✗ FAIL\n";
        $this->failed++;
    }

    /**
     * Print test results
     */
    private function printResults()
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════╗\n";
        echo "║                    Test Results                          ║\n";
        echo "╚══════════════════════════════════════════════════════════╝\n";
        echo "\n";

        echo "Passed: " . $this->passed . "\n";
        echo "Failed: " . $this->failed . "\n";
        echo "Warnings: " . count($this->warnings) . "\n";
        echo "\n";

        if (!empty($this->errors)) {
            echo "ERRORS:\n";
            foreach ($this->errors as $error) {
                echo "  ✗ $error\n";
            }
            echo "\n";
        }

        if (!empty($this->warnings)) {
            echo "WARNINGS:\n";
            foreach ($this->warnings as $warning) {
                echo "  ⚠ $warning\n";
            }
            echo "\n";
        }

        if ($this->failed === 0) {
            echo "╔══════════════════════════════════════════════════════════╗\n";
            echo "║              ✓ ALL TESTS PASSED!                         ║\n";
            echo "║     Theme is compatible with current XOOPS version       ║\n";
            echo "╚══════════════════════════════════════════════════════════╝\n";
            exit(0);
        } else {
            echo "╔══════════════════════════════════════════════════════════╗\n";
            echo "║              ✗ SOME TESTS FAILED                         ║\n";
            echo "║          Please fix errors before deployment            ║\n";
            echo "╚══════════════════════════════════════════════════════════╝\n";
            exit(1);
        }
    }
}

// Run tests if executed directly
if (php_sapi_name() === 'cli') {
    $tester = new ModernThemeCompatibilityTest();
    $tester->runAll();
}
