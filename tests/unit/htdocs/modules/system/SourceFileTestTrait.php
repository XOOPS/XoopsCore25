<?php
/**
 * Shared trait for static source file analysis tests.
 *
 * Provides file resolution and content loading for tests that
 * read source files as strings rather than executing them.
 *
 * @copyright    2000-2026 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package      Tests\Unit\System
 */

declare(strict_types=1);

namespace Tests\Unit\System;

trait SourceFileTestTrait
{
    /**
     * @var string The source content of the file under test
     */
    protected string $sourceContent = '';

    /**
     * @var string Resolved path to the file under test
     */
    protected string $filePath = '';

    /**
     * Resolve a source file from multiple candidate paths and load its content.
     *
     * Searches through candidate paths derived from dirname() at various
     * levels to handle different project layouts (repo root, webroot, etc.).
     *
     * @param string $relativePath Path relative to project root, e.g.
     *                             'htdocs/modules/system/admin/modulesadmin/main.php'
     * @param string $skipMessage  Message if the file cannot be found
     */
    protected function loadSourceFile(string $relativePath, string $skipMessage = ''): void
    {
        if ($skipMessage === '') {
            $skipMessage = basename($relativePath) . ' not found in expected locations';
        }

        // Locate the repository root by looking for the .git directory,
        // then build candidate paths only within that boundary.
        $repoRoot = null;
        for ($level = 3; $level <= 8; ++$level) {
            $base = dirname(__DIR__, $level);
            if (is_dir($base . '/.git')) {
                $repoRoot = realpath($base);
                break;
            }
        }

        if ($repoRoot === null || $repoRoot === false) {
            $this->markTestSkipped('Could not locate repository root (.git)');
        }

        // Build candidate paths within the repo root.
        $candidates = [
            $repoRoot . '/' . $relativePath,
        ];
        // Also try without leading 'htdocs/' for webroot-is-htdocs layouts
        if (str_starts_with($relativePath, 'htdocs/')) {
            $candidates[] = $repoRoot . '/' . substr($relativePath, 6);
        }

        $this->filePath = '';
        foreach ($candidates as $path) {
            $real = realpath($path);
            if ($real !== false && str_starts_with($real, $repoRoot)) {
                $this->filePath = $real;
                break;
            }
        }

        if ($this->filePath === '') {
            $this->markTestSkipped($skipMessage);
        }

        $this->sourceContent = file_get_contents($this->filePath);
        $this->assertNotEmpty($this->sourceContent, 'Source file should not be empty');
    }
}
