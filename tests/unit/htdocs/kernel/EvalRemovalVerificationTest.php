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

        return $file;
    }

    /**
     * Verify protector lifecycle files use var_export() for safe eval
     * and define the expected base functions.
     */
    #[Test]
    public function protectorOninstallUsesVarExportAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('oninstall.php');
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('var_export($mydirname', $content,
            'protector/oninstall.php should use var_export() for safe dirname embedding');
        $this->assertStringContainsString('protector_oninstall_base', $content,
            'protector/oninstall.php should define the base install function');
    }

    #[Test]
    public function protectorOnuninstallUsesVarExportAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('onuninstall.php');
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('var_export($mydirname', $content,
            'protector/onuninstall.php should use var_export() for safe dirname embedding');
        $this->assertStringContainsString('protector_onuninstall_base', $content,
            'protector/onuninstall.php should define the base uninstall function');
    }

    #[Test]
    public function protectorOnupdateUsesVarExportAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('onupdate.php');
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('var_export($mydirname', $content,
            'protector/onupdate.php should use var_export() for safe dirname embedding');
        $this->assertStringContainsString('protector_onupdate_base', $content,
            'protector/onupdate.php should define the base update function');
    }

    #[Test]
    public function protectorNotificationUsesVarExportAndDefinesBase(): void
    {
        $file = $this->resolveProtectorFile('notification.php');
        $this->assertFileExists($file);

        $content = file_get_contents($file);
        $this->assertNotFalse($content);

        $this->assertStringContainsString('var_export($mydirname', $content,
            'protector/notification.php should use var_export() for safe dirname embedding');
        $this->assertStringContainsString('protector_notify_base', $content,
            'protector/notification.php should define the base notify function');
    }
}
