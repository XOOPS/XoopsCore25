<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use XoopsBlock;
use XoopsUser;

require_once XOOPS_ROOT_PATH . '/kernel/block.php';

/**
 * XoopsBlock subclass that redirects custom_blocks to a temp directory.
 *
 * Allows tests to write temporary block files without polluting the
 * real custom_blocks/ tree.
 */
class TestableXoopsBlock extends XoopsBlock
{
    /** @var string|null override directory for custom blocks */
    private static ?string $testBlocksDir = null;

    /**
     * Set the custom blocks directory for testing.
     *
     * @param string|null $dir absolute path or null to reset
     */
    public static function setTestBlocksDir(?string $dir): void
    {
        self::$testBlocksDir = $dir;
    }

    /**
     * {@inheritdoc}
     */
    protected static function getCustomBlocksDir(): string
    {
        return self::$testBlocksDir ?? parent::getCustomBlocksDir();
    }
}

/**
 * Tests for file-based PHP custom blocks (executePhpBlock).
 *
 * Tests the new file-based PHP block system where content field stores
 * "filename.php|function_name" instead of raw PHP code.
 */
#[CoversClass(XoopsBlock::class)]
class XoopsBlockPhpBlockTest extends KernelTestCase
{
    /** @var string Path to the real custom_blocks directory (for example file tests) */
    private string $customBlocksDir;

    /** @var string Path to a per-test temp directory for temp block files */
    private string $tempBlocksDir;

    /**
     * Pre-initialize XoopsLogger to prevent risky test detection.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // Pre-initialize XoopsLogger so its handler installation doesn't
        // happen mid-test and trigger PHPUnit's risky test detection.
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
    }

    /**
     * Set up isolated temp directory for block file tests.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->customBlocksDir = XOOPS_ROOT_PATH . '/custom_blocks';
        $this->tempBlocksDir = sys_get_temp_dir() . '/xoops_test_blocks_' . uniqid();
        if (!is_dir($this->tempBlocksDir) && !mkdir($this->tempBlocksDir, 0755, true)) {
            $this->markTestSkipped('Cannot create temp directory: ' . $this->tempBlocksDir);
        }
        TestableXoopsBlock::setTestBlocksDir($this->tempBlocksDir);
    }

    /**
     * Clean up temp directory and reset override.
     */
    protected function tearDown(): void
    {
        TestableXoopsBlock::setTestBlocksDir(null);
        // Remove all files in temp dir
        if (is_dir($this->tempBlocksDir)) {
            $files = glob($this->tempBlocksDir . '/*');
            if ($files !== false) {
                foreach ($files as $file) {
                    if (is_file($file) && is_writable($file)) {
                        unlink($file);
                    }
                }
            }
            if (is_writable($this->tempBlocksDir)) {
                rmdir($this->tempBlocksDir);
            }
        }
        parent::tearDown();
    }

    /**
     * Helper to create a custom block using the testable subclass.
     *
     * Uses TestableXoopsBlock so file-based blocks resolve against the temp directory.
     *
     * @param string $content the block content field value
     *
     * @return XoopsBlock configured PHP custom block
     */
    private function createPhpBlock(string $content): XoopsBlock
    {
        $block = new TestableXoopsBlock();
        $block->setVar('block_type', 'C');
        $block->setVar('c_type', 'P');
        $block->setVar('content', $content);
        return $block;
    }

    /**
     * Helper to create a temporary block file with a function.
     *
     * @param string $filename    the PHP filename to create
     * @param string $functionName the function name to define
     * @param string $returnValue  the value the function should return
     *
     * @return string the filename (without path)
     */
    private function createTempBlockFile(string $filename, string $functionName, string $returnValue): string
    {
        $filePath = $this->tempBlocksDir . '/' . $filename;
        $code = "<?php\n"
            . "defined('XOOPS_ROOT_PATH') || exit('Restricted access');\n"
            . "if (!function_exists('{$functionName}')) {\n"
            . "    function {$functionName}() {\n"
            . "        return " . var_export($returnValue, true) . ";\n"
            . "    }\n"
            . "}\n";
        $written = file_put_contents($filePath, $code);
        $this->assertNotFalse($written, "Failed to write temp block file: {$filePath}");

        return $filename;
    }

    /**
     * Helper to remove a temporary block file.
     *
     * @param string $filename the filename to remove from custom_blocks/
     */
    private function removeTempBlockFile(string $filename): void
    {
        $filePath = $this->tempBlocksDir . '/' . $filename;
        if (is_file($filePath) && is_writable($filePath)) {
            unlink($filePath);
        }
    }

    // =========================================================================
    // File-based PHP block — valid format parsing
    // =========================================================================

    /**
     * Verify that a file-based block executes the referenced function and returns its content.
     */
    #[Test]
    public function fileBasedBlockExecutesFunctionAndReturnsContent(): void
    {
        $filename = 'test_block_' . uniqid() . '.php';
        $funcName = 'b_custom_test_' . str_replace('.', '', uniqid()) . '_show';
        $this->createTempBlockFile($filename, $funcName, '<p>Hello from test block</p>');

        try {
            $block = $this->createPhpBlock($filename . '|' . $funcName);
            $result = $block->getContent('S', 'P');

            $this->assertEquals('<p>Hello from test block</p>', $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    /**
     * Verify that {X_SITEURL} placeholder is replaced in file-based block output.
     */
    #[Test]
    public function fileBasedBlockReplacesXSiteurlPlaceholder(): void
    {
        $filename = 'test_siteurl_' . uniqid() . '.php';
        $funcName = 'b_custom_siteurl_' . str_replace('.', '', uniqid()) . '_show';
        $this->createTempBlockFile($filename, $funcName, '<a href="{X_SITEURL}page.php">Link</a>');

        try {
            $block = $this->createPhpBlock($filename . '|' . $funcName);
            $result = $block->getContent('S', 'P');

            $expected = '<a href="' . XOOPS_URL . '/page.php">Link</a>';
            $this->assertEquals($expected, $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    /**
     * Verify that leading/trailing whitespace in content is trimmed before regex matching.
     */
    #[Test]
    public function fileBasedBlockTrimsWhitespace(): void
    {
        $filename = 'test_trim_' . uniqid() . '.php';
        $funcName = 'b_custom_trim_' . str_replace('.', '', uniqid()) . '_show';
        $this->createTempBlockFile($filename, $funcName, '<p>Trimmed</p>');

        try {
            // Content with leading/trailing whitespace
            $block = $this->createPhpBlock("  {$filename}|{$funcName}  ");
            $result = $block->getContent('S', 'P');

            $this->assertEquals('<p>Trimmed</p>', $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    /**
     * Verify that a function returning null produces an empty string.
     */
    #[Test]
    public function fileBasedBlockHandlesNullReturn(): void
    {
        $filename = 'test_null_' . uniqid() . '.php';
        $funcName = 'b_custom_null_' . str_replace('.', '', uniqid()) . '_show';
        // Create a block file that returns null
        $filePath = $this->tempBlocksDir . '/' . $filename;
        $code = "<?php\n"
            . "defined('XOOPS_ROOT_PATH') || exit('Restricted access');\n"
            . "if (!function_exists('{$funcName}')) {\n"
            . "    function {$funcName}() { return null; }\n"
            . "}\n";
        $this->assertNotFalse(file_put_contents($filePath, $code), "Failed to write: {$filePath}");

        try {
            $block = $this->createPhpBlock($filename . '|' . $funcName);
            $result = $block->getContent('S', 'P');

            // null is cast to '' by (string)
            $this->assertEquals('', $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    // =========================================================================
    // File-based PHP block — file not found
    // =========================================================================

    /**
     * Verify that a missing block file returns empty string without error.
     */
    #[Test]
    public function fileBasedBlockReturnsEmptyWhenFileNotFound(): void
    {
        $block = $this->createPhpBlock('nonexistent_block.php|b_custom_nonexistent_show');
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);
    }

    // =========================================================================
    // File-based PHP block — function not found
    // =========================================================================

    /**
     * Verify that a file without the expected function returns empty string.
     */
    #[Test]
    public function fileBasedBlockReturnsEmptyWhenFunctionNotFound(): void
    {
        $filename = 'test_nofunc_' . uniqid() . '.php';
        // Create a file that does NOT define the expected function
        $filePath = $this->tempBlocksDir . '/' . $filename;
        $code = "<?php\n"
            . "defined('XOOPS_ROOT_PATH') || exit('Restricted access');\n"
            . "// This file intentionally has no function\n";
        $this->assertNotFalse(file_put_contents($filePath, $code), "Failed to write: {$filePath}");

        try {
            $block = $this->createPhpBlock($filename . '|b_custom_missing_function_show');
            $result = $block->getContent('S', 'P');

            $this->assertSame('', $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    // =========================================================================
    // Content format validation — regex matching
    // =========================================================================

    /**
     * Verify that valid file-based content formats are recognized by the regex
     * and routed to the file-based branch (not the legacy eval branch).
     *
     * @param string $content the content field value to test
     */
    #[Test]
    #[DataProvider('validContentFormatProvider')]
    public function contentFormatIsRecognizedAsFileBased(string $content): void
    {
        $logger = \XoopsLogger::getInstance();
        $extraBefore = count($logger->extra);

        $block = $this->createPhpBlock($content);
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);

        // Verify we hit the file-based branch: logger should have a "file not found" warning
        $newExtras = array_slice($logger->extra, $extraBefore);
        $messages = array_column($newExtras, 'msg');
        $this->assertNotEmpty($newExtras, 'Should have logged a file-based branch warning');
        $hasFileWarning = false;
        foreach ($messages as $msg) {
            if (str_contains($msg, 'block file not found') || str_contains($msg, 'resolves outside')) {
                $hasFileWarning = true;
                break;
            }
        }
        $this->assertTrue($hasFileWarning, 'Should hit file-based branch, not legacy eval');
    }

    /**
     * Provides valid file-based content format strings.
     *
     * @return array<string, array{string}>
     */
    public static function validContentFormatProvider(): array
    {
        return [
            'simple filename and function'   => ['my_block.php|b_custom_show'],
            'with hyphens'                   => ['my-block.php|b_custom_show'],
            'with numbers'                   => ['block123.php|b_custom_123_show'],
            'underscore heavy'               => ['my_block_file.php|b_custom_my_block_file_show'],
            'mixed case'                     => ['MyBlock.php|b_Custom_Show'],
        ];
    }

    /**
     * Verify that invalid content formats fall through to the legacy path (blocked)
     * and produce legacy-branch log messages, not file-based-branch messages.
     *
     * @param string $content the content field value to test
     */
    #[Test]
    #[DataProvider('invalidContentFormatProvider')]
    public function contentFormatIsNotRecognizedAsFileBased(string $content): void
    {
        $logger = \XoopsLogger::getInstance();
        $extraBefore = count($logger->extra);

        $block = $this->createPhpBlock($content);
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);

        // Verify we did NOT hit the file-based branch
        $newExtras = array_slice($logger->extra, $extraBefore);
        $messages = array_column($newExtras, 'msg');
        foreach ($messages as $msg) {
            $this->assertStringNotContainsString(
                'block file not found',
                $msg,
                'Invalid format should not reach the file-based branch'
            );
        }
    }

    /**
     * Provides invalid content format strings that should not match the file-based regex.
     *
     * @return array<string, array{string}>
     */
    public static function invalidContentFormatProvider(): array
    {
        return [
            'raw PHP code'                  => ['echo "hello";'],
            'missing pipe separator'        => ['my_block.php b_custom_show'],
            'missing function name'         => ['my_block.php|'],
            'missing filename'              => ['|b_custom_show'],
            'path traversal attempt'        => ['../../../etc/passwd.php|evil_func'],
            'subdirectory attempt'          => ['subdir/block.php|b_show'],
            'double extension'              => ['block.php.bak|b_show'],
            'no extension'                  => ['block|b_show'],
            'wrong extension'               => ['block.inc|b_show'],
            'empty content'                 => [''],
            'spaces in filename'            => ['my block.php|b_show'],
            'special chars in filename'     => ['block!.php|b_show'],
            'special chars in function'     => ['block.php|b_show!'],
            'function with dots'            => ['block.php|b.show'],
            'function with hyphens'         => ['block.php|b-show'],
        ];
    }

    // =========================================================================
    // Security: path traversal prevention
    // =========================================================================

    /**
     * Verify that path traversal sequences in filenames are rejected by the regex.
     */
    #[Test]
    public function pathTraversalInFilenameIsRejected(): void
    {
        $block = $this->createPhpBlock('../../etc/passwd.php|evil_func');
        $result = $block->getContent('S', 'P');

        // The regex rejects slashes, so this falls to legacy path (blocked)
        $this->assertSame('', $result);
    }

    /**
     * Verify that absolute paths in filenames are rejected.
     */
    #[Test]
    public function absolutePathInFilenameIsRejected(): void
    {
        $block = $this->createPhpBlock('/etc/passwd.php|evil_func');
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);
    }

    /**
     * Verify that Windows-style absolute paths in filenames are rejected.
     */
    #[Test]
    public function windowsPathInFilenameIsRejected(): void
    {
        $block = $this->createPhpBlock('C:\\windows\\system32\\evil.php|evil_func');
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);
    }

    /**
     * Verify that a pre-existing global function cannot be invoked via file-based
     * block format if it wasn't defined in the referenced file.
     *
     * This tests the ReflectionFunction origin check that prevents calling
     * arbitrary global functions like phpinfo().
     */
    #[Test]
    public function functionDefinedOutsideBlockFileIsRejected(): void
    {
        // Create a block file that defines a DIFFERENT function than the one we'll reference
        $filename = 'test_origin_' . uniqid() . '.php';
        $realFunc = 'b_custom_origin_' . str_replace('.', '', uniqid()) . '_show';
        $this->createTempBlockFile($filename, $realFunc, '<p>Real function</p>');

        // Define a spoof function in a separate temp file (not the block file)
        $spoofFunc = 'b_custom_spoof_' . str_replace('.', '', uniqid()) . '_show';
        $spoofFile = 'test_spoof_' . uniqid() . '.php';
        $spoofPath = $this->tempBlocksDir . '/' . $spoofFile;
        $written = file_put_contents($spoofPath, "<?php\nfunction {$spoofFunc}() { return '<p>SPOOFED</p>'; }\n");
        $this->assertNotFalse($written, "Failed to write spoof file: {$spoofPath}");
        include_once $spoofPath;

        try {
            // Reference the block file but with the spoofed function name
            $block = $this->createPhpBlock($filename . '|' . $spoofFunc);
            $result = $block->getContent('S', 'P');

            // Should be rejected — function exists but wasn't defined in the block file
            $this->assertSame('', $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    // =========================================================================
    // Legacy eval path — backward compatibility
    // =========================================================================

    /**
     * Verify that legacy eval blocks return empty when XOOPS_ALLOW_PHP_BLOCKS is not defined.
     */
    #[Test]
    public function legacyBlockReturnsEmptyWhenConstantNotDefined(): void
    {
        // Raw PHP code in content — should be blocked since
        // XOOPS_ALLOW_PHP_BLOCKS is not defined
        $block = $this->createPhpBlock('echo "Hello from eval";');
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);
    }

    // =========================================================================
    // getContent routing — c_type dispatch
    // =========================================================================

    /**
     * Verify that HTML c_type returns content without modification.
     */
    #[Test]
    public function getContentWithHtmlCTypeReturnsHtmlContent(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'C');
        $block->setVar('c_type', 'H');
        $block->setVar('content', '<p>HTML content</p>');

        $result = $block->getContent('S', 'H');

        $this->assertEquals('<p>HTML content</p>', $result);
    }

    /**
     * Verify that {X_SITEURL} is replaced in HTML content type blocks.
     */
    #[Test]
    public function getContentWithHtmlReplacesXSiteurl(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'C');
        $block->setVar('c_type', 'H');
        $block->setVar('content', '<a href="{X_SITEURL}">Home</a>');

        $result = $block->getContent('S', 'H');

        $this->assertEquals('<a href="' . XOOPS_URL . '/">Home</a>', $result);
    }

    // =========================================================================
    // Example block files — integration tests
    // =========================================================================

    /**
     * Verify that the example welcome block file exists.
     */
    #[Test]
    public function exampleWelcomeBlockFileExists(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/example_welcome.php');
    }

    /**
     * Verify that the example recent members block file exists.
     */
    #[Test]
    public function exampleRecentMembersBlockFileExists(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/example_recent_members.php');
    }

    /**
     * Verify that the example site stats block file exists.
     */
    #[Test]
    public function exampleSiteStatsBlockFileExists(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/example_site_stats.php');
    }

    /**
     * Verify that all example block files define their expected functions.
     */
    #[Test]
    public function exampleBlockFilesDefineExpectedFunctions(): void
    {
        // Include the example files
        include_once $this->customBlocksDir . '/example_welcome.php';
        include_once $this->customBlocksDir . '/example_recent_members.php';
        include_once $this->customBlocksDir . '/example_site_stats.php';

        $this->assertTrue(function_exists('b_custom_welcome_show'));
        $this->assertTrue(function_exists('b_custom_recent_members_show'));
        $this->assertTrue(function_exists('b_custom_site_stats_show'));
    }

    /**
     * Verify that the welcome block returns HTML with site name for guest users.
     */
    #[Test]
    public function exampleWelcomeBlockReturnsHtmlForGuest(): void
    {
        $originalUser   = $GLOBALS['xoopsUser'] ?? null;
        $originalConfig = $GLOBALS['xoopsConfig'] ?? null;

        try {
            $GLOBALS['xoopsUser']   = null;
            $GLOBALS['xoopsConfig'] = ['sitename' => 'Test Site'];

            include_once $this->customBlocksDir . '/example_welcome.php';
            $result = b_custom_welcome_show();

            $this->assertIsString($result);
            $this->assertStringContainsString('Test Site', $result);
            $this->assertNotEmpty($result);
        } finally {
            $GLOBALS['xoopsUser']   = $originalUser;
            $GLOBALS['xoopsConfig'] = $originalConfig;
        }
    }

    /**
     * Verify that the welcome block returns HTML with username for logged-in users.
     */
    #[Test]
    public function exampleWelcomeBlockReturnsHtmlForLoggedInUser(): void
    {
        $originalUser   = $GLOBALS['xoopsUser'] ?? null;
        $originalConfig = $GLOBALS['xoopsConfig'] ?? null;

        try {
            require_once XOOPS_ROOT_PATH . '/kernel/user.php';
            $user = new XoopsUser();
            $user->setVar('uname', 'TestUser');
            $user->setVar('uid', 1);
            $GLOBALS['xoopsUser']   = $user;
            $GLOBALS['xoopsConfig'] = $originalConfig ?? [];

            include_once $this->customBlocksDir . '/example_welcome.php';
            $result = b_custom_welcome_show();

            $this->assertIsString($result);
            $this->assertStringContainsString('TestUser', $result);
            $this->assertNotEmpty($result);
        } finally {
            $GLOBALS['xoopsUser']   = $originalUser;
            $GLOBALS['xoopsConfig'] = $originalConfig;
        }
    }

    // =========================================================================
    // executePhpBlock — edge cases
    // =========================================================================

    /**
     * Verify that multiple calls to the same block file use include_once without errors.
     */
    #[Test]
    public function multipleCallsToSameBlockFileUseIncludeOnce(): void
    {
        $filename = 'test_includeonce_' . uniqid() . '.php';
        $funcName = 'b_custom_includeonce_' . str_replace('.', '', uniqid()) . '_show';
        $this->createTempBlockFile($filename, $funcName, '<p>Include once test</p>');

        try {
            // Call twice — should not cause "function already defined" errors
            $block1 = $this->createPhpBlock($filename . '|' . $funcName);
            $result1 = $block1->getContent('S', 'P');

            $block2 = $this->createPhpBlock($filename . '|' . $funcName);
            $result2 = $block2->getContent('S', 'P');

            $this->assertEquals('<p>Include once test</p>', $result1);
            $this->assertEquals('<p>Include once test</p>', $result2);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    /**
     * Verify that echoed output from block functions is captured along with returned content.
     */
    #[Test]
    public function fileBasedBlockCapturesEchoedOutput(): void
    {
        $filename = 'test_echo_' . uniqid() . '.php';
        $funcName = 'b_custom_echo_' . str_replace('.', '', uniqid()) . '_show';
        // Create a block file that echoes instead of returning
        $filePath = $this->tempBlocksDir . '/' . $filename;
        $code = "<?php\n"
            . "defined('XOOPS_ROOT_PATH') || exit('Restricted access');\n"
            . "if (!function_exists('{$funcName}')) {\n"
            . "    function {$funcName}() {\n"
            . "        echo '<p>Echoed output</p>';\n"
            . "        return '<p>Returned output</p>';\n"
            . "    }\n"
            . "}\n";
        $this->assertNotFalse(file_put_contents($filePath, $code), "Failed to write: {$filePath}");

        try {
            $block = $this->createPhpBlock($filename . '|' . $funcName);
            $result = $block->getContent('S', 'P');

            // Should contain both echoed and returned content
            $this->assertStringContainsString('<p>Echoed output</p>', $result);
            $this->assertStringContainsString('<p>Returned output</p>', $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    /**
     * Verify that a function returning an empty string produces empty block output.
     */
    #[Test]
    public function blockWithEmptyStringReturnedFromFunction(): void
    {
        $filename = 'test_empty_' . uniqid() . '.php';
        $funcName = 'b_custom_empty_' . str_replace('.', '', uniqid()) . '_show';
        $this->createTempBlockFile($filename, $funcName, '');

        try {
            $block = $this->createPhpBlock($filename . '|' . $funcName);
            $result = $block->getContent('S', 'P');

            $this->assertSame('', $result);
        } finally {
            $this->removeTempBlockFile($filename);
        }
    }

    /**
     * Verify that the index.php guard file exists in the custom_blocks directory.
     */
    #[Test]
    public function indexPhpGuardFileExistsInCustomBlocksDir(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/index.php');
    }

    /**
     * Verify that content containing a pipe but not matching file-based format
     * is blocked with a pipe-specific warning when legacy mode is disabled.
     */
    #[Test]
    public function malformedPipeContentIsBlockedWithSpecificWarning(): void
    {
        $logger = \XoopsLogger::getInstance();
        $extraBefore = count($logger->extra);

        $block = $this->createPhpBlock('file.php|func-name');
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);

        // Should have the pipe-specific warning (not generic legacy deprecation)
        $newExtras = array_slice($logger->extra, $extraBefore);
        $messages = array_column($newExtras, 'msg');
        $hasPipeWarning = false;
        foreach ($messages as $msg) {
            if (str_contains($msg, 'contains ".php|"')) {
                $hasPipeWarning = true;
                break;
            }
        }
        $this->assertTrue($hasPipeWarning, 'Should warn about malformed pipe content');
    }

    /**
     * Verify that the pipe guard only applies when legacy mode is disabled.
     *
     * Content with "|" gets a pipe-specific extra warning.
     * Content without "|" does NOT get a pipe warning (different branch).
     * This proves the two code paths are distinct.
     */
    #[Test]
    public function pipeGuardAndLegacyPathAreSeparateBranches(): void
    {
        $logger = \XoopsLogger::getInstance();

        // Content WITH pipe — should get pipe-specific extra warning
        $extraBefore = count($logger->extra);
        $block = $this->createPhpBlock('file.php|func-name');
        $block->getContent('S', 'P');

        $newExtras = array_slice($logger->extra, $extraBefore);
        $pipeMessages = array_column($newExtras, 'msg');
        $this->assertNotEmpty(array_filter($pipeMessages, fn($m) => str_contains($m, 'contains ".php|"')),
            'Pipe content should produce pipe-specific warning');

        // Content WITHOUT pipe — should NOT produce a pipe-specific warning
        $extraBefore2 = count($logger->extra);
        $block2 = $this->createPhpBlock('echo "hello";');
        $result2 = $block2->getContent('S', 'P');

        $this->assertSame('', $result2);

        $newExtras2 = array_slice($logger->extra, $extraBefore2);
        $legacyPipeWarnings = array_filter(array_column($newExtras2, 'msg'), fn($m) => str_contains($m, 'contains ".php|"'));
        $this->assertEmpty($legacyPipeWarnings, 'Non-pipe content should not trigger pipe warning');
    }
}
