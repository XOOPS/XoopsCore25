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
 * XOOPS Upgrade Smarty3TemplateChecks
 *
 * Scanner process to look for BC issues in existing templates when used with Smarty3
 *
 * @category  Xoops\Upgrade
 * @package   Xoops
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2023 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
class Smarty3TemplateChecks extends ScannerProcess
{
    protected $patterns = array(
        'varname' => '/<{foreach[[:space:]]+item=([a-zA-Z0-9\-_.]+)[[:space:]]from=\$([a-zA-Z0-9\-_.]+) *}>/',
        'noquotes' =>'/(<{xo[a-zA-Z\d]*\b[^}>]*?)\s*([^\'"}]+)(}>)/',
        'includeq' => '/(<{includeq[[:space:]]+[ -=\.\/_\'\"\$a-zA-Z0-9]+}>)/',
        'foreachq' => '/(<{foreachq[[:space:]]+[ -=\.\/_\'\"\$a-zA-Z0-9]+}>)/',
    );

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
    }

    /**
     * @param SplFileInfo $fileInfo
     * @return void
     */
    public function inspectFile(SplFileInfo $fileInfo)
    {
        $output = $this->output;
        $writable = $fileInfo->isWritable();
        $length = $fileInfo->getSize();
        $file = $fileInfo->openFile();
        $contents = $file->fread($length);

        // variable names in Smarty 3 foreach item and from must be unique
        $rule = 'varname';
        $pattern = $this->patterns[$rule];
        $results = preg_match_all($pattern, $contents, $matches, PREG_PATTERN_ORDER, 0);
        if ((0 < (int)$results) && isset($matches[0][0]) && is_string($matches[0][0])) {
            for ($i = 0; $i < (int)$results; $i++) {
                if ($matches[1][$i] == $matches[2][$i]) {
                    $file = str_replace(XOOPS_ROOT_PATH, '', $fileInfo->getPathname());
                    $match = $matches[0][$i];
                    $output->outputIssue($output->makeOutputIssue($rule, $file, $match, $writable));
                }
            }
        }
        unset($matches);

        // plugin function arguments must be quoted
        $rule = 'noquotes';
        $pattern = $this->patterns[$rule];
        $results = preg_match_all($pattern, $contents, $matches, PREG_PATTERN_ORDER, 0);
        if (0 < (int)$results) {
            for ($i = 0; $i < (int)$results; $i++) {
                $match = isset($matches[0][$i]) ? $matches[0][$i] : null;
                if (null !== $match && '<{if false}>' !== $match) { // oddball case
                    $file = str_replace(XOOPS_ROOT_PATH, '', $fileInfo->getPathname());
                    $output->outputIssue($output->makeOutputIssue($rule, $file, $match, $writable));
                }
            }
        }
        unset($matches);

        // includeq was removed, use include instead
        $rule = 'includeq';
        $pattern = $this->patterns[$rule];
        $results = preg_match_all($pattern, $contents, $matches, PREG_PATTERN_ORDER, 0);
        if (0 < (int)$results) {
            $match = isset($matches[0][0]) ? $matches[0][0] : null;
            if (null !== $match) {
                $file = str_replace(XOOPS_ROOT_PATH, '', $fileInfo->getPathname());
                $output->outputIssue($output->makeOutputIssue($rule, $file, $match, $writable));
            }
        }
        unset($matches);

        // foreachq was removed, use foreach instead
        $rule = 'foreachq';
        $pattern = $this->patterns[$rule];
        $results = preg_match_all($pattern, $contents, $matches, PREG_PATTERN_ORDER, 0);
        if (0 < (int)$results) {
            $match = isset($matches[0][0]) ? $matches[0][0] : null;
            if (null !== $match) {
                $file = str_replace(XOOPS_ROOT_PATH, '', $fileInfo->getPathname());
                $output->outputIssue($output->makeOutputIssue($rule, $file, $match, $writable));
            }
        }
        unset($matches);
    }
}
