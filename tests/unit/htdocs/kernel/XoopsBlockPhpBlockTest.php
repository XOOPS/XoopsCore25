<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use XoopsBlock;

require_once XOOPS_ROOT_PATH . '/kernel/block.php';

/**
 * Tests for file-based PHP custom blocks (executePhpBlock).
 *
 * Tests the new file-based PHP block system where content field stores
 * "filename.php|function_name" instead of raw PHP code.
 */
#[CoversClass(XoopsBlock::class)]
class XoopsBlockPhpBlockTest extends KernelTestCase
{
    private string $customBlocksDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customBlocksDir = XOOPS_ROOT_PATH . '/custom_blocks';

        // Ensure the custom_blocks directory exists for testing
        if (!is_dir($this->customBlocksDir)) {
            mkdir($this->customBlocksDir, 0755, true);
        }
    }

    /**
     * Helper to create a custom block with c_type = 'P' (PHP) and block_type = 'C' (custom).
     */
    private function createPhpBlock(string $content): XoopsBlock
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'C');
        $block->setVar('c_type', 'P');
        $block->setVar('content', $content);
        return $block;
    }

    /**
     * Helper to create a temporary block file with a function.
     *
     * @return string The filename (without path)
     */
    private function createTempBlockFile(string $filename, string $functionName, string $returnValue): string
    {
        $filePath = $this->customBlocksDir . '/' . $filename;
        $code = "<?php\n"
            . "defined('XOOPS_ROOT_PATH') || exit('Restricted access');\n"
            . "if (!function_exists('{$functionName}')) {\n"
            . "    function {$functionName}() {\n"
            . "        return " . var_export($returnValue, true) . ";\n"
            . "    }\n"
            . "}\n";
        file_put_contents($filePath, $code);

        return $filename;
    }

    /**
     * Helper to remove a temporary block file.
     */
    private function removeTempBlockFile(string $filename): void
    {
        $filePath = $this->customBlocksDir . '/' . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // =========================================================================
    // File-based PHP block — valid format parsing
    // =========================================================================

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

    #[Test]
    public function fileBasedBlockHandlesNullReturn(): void
    {
        $filename = 'test_null_' . uniqid() . '.php';
        $funcName = 'b_custom_null_' . str_replace('.', '', uniqid()) . '_show';
        // Create a block file that returns null
        $filePath = $this->customBlocksDir . '/' . $filename;
        $code = "<?php\n"
            . "defined('XOOPS_ROOT_PATH') || exit('Restricted access');\n"
            . "if (!function_exists('{$funcName}')) {\n"
            . "    function {$funcName}() { return null; }\n"
            . "}\n";
        file_put_contents($filePath, $code);

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

    #[Test]
    public function fileBasedBlockReturnsEmptyWhenFileNotFound(): void
    {
        // Save and restore error/exception handlers to avoid PHPUnit "risky" warning
        // (XoopsLogger::getInstance() installs its own handlers internally)
        $block = $this->createPhpBlock('nonexistent_block.php|b_custom_nonexistent_show');
        $result = $block->getContent('S', 'P');

        restore_error_handler();
        restore_exception_handler();

        $this->assertSame('', $result);
    }

    // =========================================================================
    // File-based PHP block — function not found
    // =========================================================================

    #[Test]
    public function fileBasedBlockReturnsEmptyWhenFunctionNotFound(): void
    {
        $filename = 'test_nofunc_' . uniqid() . '.php';
        // Create a file that does NOT define the expected function
        $filePath = $this->customBlocksDir . '/' . $filename;
        $code = "<?php\n"
            . "defined('XOOPS_ROOT_PATH') || exit('Restricted access');\n"
            . "// This file intentionally has no function\n";
        file_put_contents($filePath, $code);

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

    #[Test]
    #[DataProvider('validContentFormatProvider')]
    public function contentFormatIsRecognizedAsFileBased(string $content): void
    {
        // These formats should match the file-based regex, even if the file
        // doesn't exist — they should return '' (file not found) not trigger
        // the legacy eval path.
        $block = $this->createPhpBlock($content);
        $result = $block->getContent('S', 'P');

        // Should return empty string (file not found) — NOT trigger eval
        $this->assertSame('', $result);
    }

    /**
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

    #[Test]
    #[DataProvider('invalidContentFormatProvider')]
    public function contentFormatIsNotRecognizedAsFileBased(string $content): void
    {
        // These formats should NOT match the file-based regex, so they fall
        // through to the legacy eval path. Since XOOPS_ALLOW_PHP_BLOCKS is
        // not defined (or false), they should return empty string with warning.
        $block = $this->createPhpBlock($content);
        $result = $block->getContent('S', 'P');

        // Should return empty (legacy eval blocked)
        $this->assertSame('', $result);
    }

    /**
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

    #[Test]
    public function pathTraversalInFilenameIsRejected(): void
    {
        $block = $this->createPhpBlock('../../etc/passwd.php|evil_func');
        $result = $block->getContent('S', 'P');

        // The regex rejects slashes, so this falls to legacy path (blocked)
        $this->assertSame('', $result);
    }

    #[Test]
    public function absolutePathInFilenameIsRejected(): void
    {
        $block = $this->createPhpBlock('/etc/passwd.php|evil_func');
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);
    }

    #[Test]
    public function windowsPathInFilenameIsRejected(): void
    {
        $block = $this->createPhpBlock('C:\\windows\\system32\\evil.php|evil_func');
        $result = $block->getContent('S', 'P');

        $this->assertSame('', $result);
    }

    // =========================================================================
    // Legacy eval path — backward compatibility
    // =========================================================================

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

    #[Test]
    public function exampleWelcomeBlockFileExists(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/example_welcome.php');
    }

    #[Test]
    public function exampleRecentMembersBlockFileExists(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/example_recent_members.php');
    }

    #[Test]
    public function exampleSiteStatsBlockFileExists(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/example_site_stats.php');
    }

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

    #[Test]
    public function exampleWelcomeBlockReturnsHtmlForGuest(): void
    {
        // Ensure no user is logged in
        $GLOBALS['xoopsUser'] = null;
        $GLOBALS['xoopsConfig'] = ['sitename' => 'Test Site'];

        include_once $this->customBlocksDir . '/example_welcome.php';
        $result = b_custom_welcome_show();

        $this->assertIsString($result);
        $this->assertStringContainsString('Test Site', $result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function exampleWelcomeBlockReturnsHtmlForLoggedInUser(): void
    {
        // Create a mock user object
        require_once XOOPS_ROOT_PATH . '/kernel/user.php';
        $user = new \XoopsUser();
        $user->setVar('uname', 'TestUser');
        $user->setVar('uid', 1);
        $GLOBALS['xoopsUser'] = $user;

        include_once $this->customBlocksDir . '/example_welcome.php';
        $result = b_custom_welcome_show();

        $this->assertIsString($result);
        $this->assertStringContainsString('TestUser', $result);
        $this->assertNotEmpty($result);

        // Clean up
        $GLOBALS['xoopsUser'] = null;
    }

    // =========================================================================
    // executePhpBlock — edge cases
    // =========================================================================

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

    #[Test]
    public function indexPhpGuardFileExistsInCustomBlocksDir(): void
    {
        $this->assertFileExists($this->customBlocksDir . '/index.php');
    }
}
