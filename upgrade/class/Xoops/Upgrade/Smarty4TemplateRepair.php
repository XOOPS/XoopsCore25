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

namespace Xoops\Upgrade;

use SplFileInfo;

/**
 * XOOPS Upgrade Smarty4TemplateRepair
 *
 * Scanner process to look for and repair BC issues in existing templates when used with Smarty4
 *
 * @category  Xoops\Upgrade
 * @package   Xoops
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2023 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
class Smarty4TemplateRepair extends ScannerProcess
{
    /**
     * @var int count of changes made
     */
    protected $count = 0;

    /**
     * @var array regex patterns
     */
    protected $patterns = [];

    /**
     * @var array replacement patterns
     */
    protected $replacements = [];

    /**
     * @var ScannerOutput
     */
    private $output;

    /**
     * @param ScannerOutput $output
     */
    public function __construct(ScannerOutput $output)
    {
        $this->output = $output;
        $this->loadPatterns();
    }

    protected function loadPatterns()
    {
        $this->patterns[] = '/(<{includeq[[:space:]]+)/';
        $this->replacements[] = '<{include ';

        $this->patterns[] = '/(<{foreachq[[:space:]]+)/';
        $this->replacements[] = '<{foreach ';

        // For double quotes
        $this->patterns[] = '/("<{xo[a-zA-Z\d]*\b[^}>]*?)\s*([^\'"}=]+(?:=[^\'"}=]*)*)\s?}>/';
        $this->replacements[] = "$1 '$2'}>";

        // For single quotes
        $this->patterns[] = "/(\'<{xo[a-zA-Z\d]*\b[^}>]*?)\s*([^\'\"=]+(?:=[^\'\"=]*)*)\s?}>/";
        $this->replacements[] = '$1 "$2"}>';

        // For no surrounding quotes
        $this->patterns[] = '/(<{xo[a-zA-Z\d]*\b[^}>]*?)\s+([^\s\'\"=]+)(\s*}>)/';
        $this->replacements[] = '$1 \'$2\'$3';
    }

    /**
     * @param SplFileInfo $fileInfo
     * @return void
     */
    public function inspectFile(SplFileInfo $fileInfo)
    {
        $output = $this->output;
        if (false === $fileInfo->isWritable()) {
            return;
        }

        /** get and sanitize $filename used for error messages */
        $filename = str_replace(XOOPS_ROOT_PATH, '', $fileInfo->getPathname());

        $length = $fileInfo->getSize();
        $file = $fileInfo->openFile('r+');
        $lines = $file->fread($length);

        $count = 0;
        $updatedLines = preg_replace(
            $this->patterns,
            $this->replacements,
            $lines,
            -1,
            $count,
        );
        if ($updatedLines === null) {
            trigger_error(sprintf('NULL return processing: %s', $filename), E_WARNING);
        }

        /* rewrite if changes were made */
        if ($count !== 0) {
            $file->fseek(0);
            $file->ftruncate(0);
            $result = $file->fwrite($updatedLines);
            if ($result == false) {
                trigger_error(sprintf('Error writing file: %s', $filename), E_WARNING);
            }
            $output->outputIssue($output->makeOutputIssue($filename, $count));
        }
    }
}
