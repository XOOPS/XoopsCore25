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
 *
 * @category  XoopsCore
 * @package   Tests\Kernel
 * @author    XOOPS Development Team
 * @copyright XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
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

    /**
     * Assert that PHP source code contains no T_EVAL tokens.
     *
     * Uses token_get_all() for reliable detection that ignores comments and strings.
     */
    private function assertNoEvalTokens(string $phpSource, string $message): void
    {
        $tokens = token_get_all($phpSource);
        $evalFound = false;
        foreach ($tokens as $token) {
            if (is_array($token) && $token[0] === T_EVAL) {
                $evalFound = true;
                break;
            }
        }
        $this->assertFalse($evalFound, $message);
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

        // Use PHP's tokenizer to reliably detect eval() in executable code.
        // T_EVAL tokens only appear for actual eval() calls, not in comments or strings.
        $this->assertNoEvalTokens($content, 'kernel/block.php should not contain any executable eval() calls');
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

        $this->assertNoEvalTokens($content, 'system/class/block.php should not contain any eval() calls');
    }

    /**
     * Verify that the legacy block path returns empty content for raw PHP code
     * and emits an E_USER_DEPRECATED signal.
     */
    #[Test]
    public function legacyPhpBlockReturnsEmptyContent(): void
    {
        require_once XOOPS_ROOT_PATH . '/kernel/block.php';

        $deprecations = [];
        set_error_handler(function (int $errno, string $errstr) use (&$deprecations) {
            if ($errno === E_USER_DEPRECATED) {
                $deprecations[] = $errstr;
                return true;
            }
            return false;
        });
        try {
            $block = new XoopsBlock();
            $block->setVar('block_type', 'C');
            $block->setVar('c_type', 'P');
            $block->setVar('content', 'echo "This should never execute";');

            $result = $block->getContent('S', 'P');
        } finally {
            restore_error_handler();
        }

        $this->assertSame('', $result, 'Legacy PHP block content must return empty string');
        $this->assertNotEmpty($deprecations, 'Should emit E_USER_DEPRECATED for legacy eval content');
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

        $this->assertNoEvalTokens($content, 'class.zipfile.php should not contain eval()');
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

        $this->assertNoEvalTokens($content, 'art/functions.ini.php should not contain eval()');
    }

    // =========================================================================
    // M-11c: Protector lifecycle — eval() retained for D3 clone support
    // =========================================================================

    /**
     * Resolve a protector trust-path file across different path structures.
     */
    private function resolveProtectorFile(string $basename): string
    {
        $file = XOOPS_ROOT_PATH . '/../xoops_lib/modules/protector/' . $basename;
        if (!file_exists($file)) {
            $file = XOOPS_PATH . '/modules/protector/' . $basename;
        }
        if (!file_exists($file)) {
            $this->markTestSkipped("Protector trust-path file not found: {$basename}");
        }

        return $file;
    }

    /**
     * Protector lifecycle files intentionally retain eval() for D3-style cloned
     * installs where $mydirname varies at runtime.  These tests verify:
     * 1. eval() is guarded by function_exists() to prevent redefinition
     * 2. $mydirname is sourced from ProtectorRegistry (not user input)
     * 3. The base function is defined
     */
    #[Test]
    public function protectorOninstallUsesGuardedEvalAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('oninstall.php');
        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('function_exists(', $content,
            'protector/oninstall.php must guard eval() with function_exists()');
        $this->assertStringContainsString('protector_oninstall_base', $content,
            'protector/oninstall.php must define the base install function');
        $this->assertStringContainsString("getEntry('mydirname')", $content,
            'protector/oninstall.php must source $mydirname from ProtectorRegistry');
    }

    #[Test]
    public function protectorOnuninstallUsesGuardedEvalAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('onuninstall.php');
        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('function_exists(', $content,
            'protector/onuninstall.php must guard eval() with function_exists()');
        $this->assertStringContainsString('protector_onuninstall_base', $content,
            'protector/onuninstall.php must define the base uninstall function');
        $this->assertStringContainsString("getEntry('mydirname')", $content,
            'protector/onuninstall.php must source $mydirname from ProtectorRegistry');
    }

    #[Test]
    public function protectorOnupdateUsesGuardedEvalAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('onupdate.php');
        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('function_exists(', $content,
            'protector/onupdate.php must guard eval() with function_exists()');
        $this->assertStringContainsString('protector_onupdate_base', $content,
            'protector/onupdate.php must define the base update function');
        $this->assertStringContainsString("getEntry('mydirname')", $content,
            'protector/onupdate.php must source $mydirname from ProtectorRegistry');
    }

    #[Test]
    public function protectorNotificationUsesGuardedEvalAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('notification.php');
        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('function_exists(', $content,
            'protector/notification.php must guard eval() with function_exists()');
        $this->assertStringContainsString('protector_notify_base', $content,
            'protector/notification.php must define the base notify function');
        $this->assertStringContainsString("getEntry('mydirname')", $content,
            'protector/notification.php must source $mydirname from ProtectorRegistry');
    }
}
