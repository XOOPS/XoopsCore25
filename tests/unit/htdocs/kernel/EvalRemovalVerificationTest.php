<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsBlock;

/**
 * Verification tests for eval() removal and legacy cleanup (2.5.12 RC).
 *
 * These tests scan source files to confirm that eval() calls have been
 * removed from security-critical paths.
 */
#[CoversClass(XoopsBlock::class)]
class EvalRemovalVerificationTest extends TestCase
{
    /**
     * Pre-initialize XoopsLogger to prevent risky test detection.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
    }

    // =========================================================================
    // H-0: Block PHP eval() removal
    // =========================================================================

    /**
     * Verify that kernel/block.php contains no executable eval() calls.
     * Comments and string references are acceptable; actual eval() calls are not.
     */
    #[Test]
    public function kernelBlockPhpHasNoExecutableEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/kernel/block.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        // Remove all comments (single-line and multi-line) and strings
        // to check only actual code for eval()
        $lines = explode("\n", $content);
        $executableEvalFound = false;
        foreach ($lines as $lineNum => $line) {
            $trimmed = trim($line);
            // Skip comment-only lines
            if (str_starts_with($trimmed, '//') || str_starts_with($trimmed, '*') || str_starts_with($trimmed, '/*')) {
                continue;
            }
            // Check for eval( in non-comment parts of the line
            $codePart = $trimmed;
            // Strip inline comments
            $commentPos = strpos($codePart, '//');
            if ($commentPos !== false) {
                $codePart = substr($codePart, 0, $commentPos);
            }
            // Strip string literals (simple approach: check for eval( outside of quotes)
            if (preg_match('/\beval\s*\(/', $codePart)) {
                // Check it's not inside a string
                $beforeEval = substr($codePart, 0, strpos($codePart, 'eval'));
                $singleQuotes = substr_count($beforeEval, "'") - substr_count($beforeEval, "\\'");
                $doubleQuotes = substr_count($beforeEval, '"') - substr_count($beforeEval, '\\"');
                if ($singleQuotes % 2 === 0 && $doubleQuotes % 2 === 0) {
                    $executableEvalFound = true;
                    break;
                }
            }
        }

        $this->assertFalse(
            $executableEvalFound,
            'kernel/block.php should not contain any executable eval() calls'
        );
    }

    /**
     * Verify that system/class/block.php contains no eval() calls.
     */
    #[Test]
    public function systemBlockPhpHasNoEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/modules/system/class/block.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/\beval\s*\(/',
            $content,
            'system/class/block.php should not contain any eval() calls'
        );
    }

    /**
     * Verify that the legacy block path returns empty content for raw PHP code.
     */
    #[Test]
    public function legacyPhpBlockReturnsEmptyContent(): void
    {
        require_once XOOPS_ROOT_PATH . '/kernel/block.php';

        // Ensure logger is initialized
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }

        $block = new XoopsBlock();
        $block->setVar('block_type', 'C');
        $block->setVar('c_type', 'P');
        $block->setVar('content', 'echo "This should never execute";');

        $result = $block->getContent('S', 'P');
        $this->assertSame('', $result, 'Legacy PHP block content must return empty string');
    }

    // =========================================================================
    // M-9: .html template registrations
    // =========================================================================

    /**
     * Verify that xoops_version.php does not register any .html templates.
     */
    #[Test]
    public function xoopsVersionPhpHasNoHtmlTemplateRegistrations(): void
    {
        $file = XOOPS_ROOT_PATH . '/modules/system/xoops_version.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/[\'"]file[\'"]\\s*=>\\s*[\'"][^\'"]*\\.html[\'"]/',
            $content,
            'xoops_version.php should not register any .html templates'
        );
    }

    // =========================================================================
    // M-10: Snoopy shell exec() — already replaced with PHP cURL
    // =========================================================================

    /**
     * Verify that snoopy.php does not use shell exec(), system(), or passthru().
     */
    #[Test]
    public function snoopyPhpHasNoShellExecution(): void
    {
        $file = XOOPS_ROOT_PATH . '/class/snoopy.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        // Check for shell execution functions (not curl_exec which is PHP cURL)
        $this->assertDoesNotMatchRegularExpression(
            '/\b(shell_exec|system|passthru)\s*\(/',
            $content,
            'snoopy.php should not use shell execution functions'
        );

        // Verify it uses PHP cURL extension
        $this->assertStringContainsString(
            'curl_init',
            $content,
            'snoopy.php should use PHP cURL extension'
        );
    }

    // =========================================================================
    // M-11a: class.zipfile.php eval() removal
    // =========================================================================

    /**
     * Verify that class.zipfile.php contains no eval() calls.
     */
    #[Test]
    public function zipfilePhpHasNoEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/class/class.zipfile.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/\beval\s*\(/',
            $content,
            'class.zipfile.php should not contain eval()'
        );
    }

    // =========================================================================
    // M-11b: art/functions.ini.php eval() removal
    // =========================================================================

    /**
     * Verify that art/functions.ini.php contains no eval() calls.
     */
    #[Test]
    public function artFunctionsIniPhpHasNoEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/Frameworks/art/functions.ini.php';
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/\beval\s*\(/',
            $content,
            'art/functions.ini.php should not contain eval()'
        );
    }

    // =========================================================================
    // M-11c: Protector lifecycle eval() removal
    // =========================================================================

    /**
     * Verify that protector/oninstall.php has no eval() and defines the callback literally.
     */
    #[Test]
    public function protectorOninstallHasNoEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/../xoops_lib/modules/protector/oninstall.php';
        // Normalize for different path structures
        if (!file_exists($file)) {
            $file = XOOPS_PATH . '/modules/protector/oninstall.php';
        }
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/\beval\s*\(/',
            $content,
            'protector/oninstall.php should not contain eval()'
        );
        $this->assertStringContainsString(
            'function xoops_module_install_protector',
            $content,
            'protector/oninstall.php should define the callback function literally'
        );
    }

    /**
     * Verify that protector/onuninstall.php has no eval() and defines the callback literally.
     */
    #[Test]
    public function protectorOnuninstallHasNoEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/../xoops_lib/modules/protector/onuninstall.php';
        if (!file_exists($file)) {
            $file = XOOPS_PATH . '/modules/protector/onuninstall.php';
        }
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/\beval\s*\(/',
            $content,
            'protector/onuninstall.php should not contain eval()'
        );
        $this->assertStringContainsString(
            'function xoops_module_uninstall_protector',
            $content,
            'protector/onuninstall.php should define the callback function literally'
        );
    }

    /**
     * Verify that protector/onupdate.php has no eval() and defines the callback literally.
     */
    #[Test]
    public function protectorOnupdateHasNoEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/../xoops_lib/modules/protector/onupdate.php';
        if (!file_exists($file)) {
            $file = XOOPS_PATH . '/modules/protector/onupdate.php';
        }
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/\beval\s*\(/',
            $content,
            'protector/onupdate.php should not contain eval()'
        );
        $this->assertStringContainsString(
            'function xoops_module_update_protector',
            $content,
            'protector/onupdate.php should define the callback function literally'
        );
    }

    /**
     * Verify that protector/notification.php has no eval() and defines the callback literally.
     */
    #[Test]
    public function protectorNotificationHasNoEval(): void
    {
        $file = XOOPS_ROOT_PATH . '/../xoops_lib/modules/protector/notification.php';
        if (!file_exists($file)) {
            $file = XOOPS_PATH . '/modules/protector/notification.php';
        }
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertDoesNotMatchRegularExpression(
            '/\beval\s*\(/',
            $content,
            'protector/notification.php should not contain eval()'
        );
        $this->assertStringContainsString(
            'function protector_notify_iteminfo',
            $content,
            'protector/notification.php should define the callback function literally'
        );
    }
}
