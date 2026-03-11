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
 * @copyright 2000-2026 XOOPS Project (https://xoops.org)
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
     * Fix foreach varname conflicts where item name equals array name.
     *
     * In Smarty 4, <{foreach item=X from=$X}> is not allowed because the item
     * name must differ from the array name. This method renames item=X to
     * item=X_item and updates all $X.property references within the foreach
     * block to $X_item.property.
     *
     * @param string $content file contents
     * @param int    &$count  incremented for each foreach block fixed
     *
     * @return string updated content
     */
    protected function fixForeachVarnames(string $content, int &$count): string
    {
        // Match foreach where item name is identical to the from variable name
        $pattern = '/<\{\s*foreach\s+item=([a-zA-Z0-9_]+)\s+from=\$\1\s*\}>/';

        while (preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE)) {
            $varName = $match[1][0];
            $newItem = $varName . '_item';
            $foreachTagStart = $match[0][1];
            $foreachTagEnd = $foreachTagStart + strlen($match[0][0]);

            // Find the matching <{/foreach}> handling nesting
            $depth = 1;
            $pos = $foreachTagEnd;
            $closingTagStart = false;

            while ($depth > 0) {
                if (!preg_match('/<\{\s*(\/?)foreach\b/', $content, $inner, PREG_OFFSET_CAPTURE, $pos)) {
                    break;
                }
                $tagPos = $inner[0][1];
                $tagEndPos = strpos($content, '}>', $tagPos);
                if ($tagEndPos === false) {
                    break;
                }
                $tagEndPos += 2; // past the }>

                if ($inner[1][0] === '/') {
                    $depth--;
                    if ($depth === 0) {
                        $closingTagStart = $tagPos;
                    }
                } else {
                    $depth++;
                }
                $pos = $tagEndPos;
            }

            if ($closingTagStart === false) {
                // Could not find matching close tag — skip entirely, leave for manual review
                break;
            }

            // Extract the block between opening and closing foreach tags
            $blockContent = substr($content, $foreachTagEnd, $closingTagStart - $foreachTagEnd);

            // Replace all Smarty references to $varName inside the block:
            // $varName.property, $varName|modifier, $varName[key], $varName}, bare $varName
            $fixedBlock = preg_replace(
                '/\$' . preg_quote($varName, '/') . '(?=[.\|\[\}\)\s]|$)/',
                '$' . $newItem,
                $blockContent
            );

            // Build new opening tag
            $newTag = '<{foreach item=' . $newItem . ' from=$' . $varName . '}>';

            // Rebuild content: before + new tag + fixed block + from closing tag onward
            $content = substr($content, 0, $foreachTagStart)
                     . $newTag
                     . $fixedBlock
                     . substr($content, $closingTagStart);

            $count++;
        }

        return $content;
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
            $updatedLines = $lines;
        }

        // Fix foreach varname conflicts (item=X from=$X where both names match)
        $varnameCount = 0;
        $updatedLines = $this->fixForeachVarnames($updatedLines, $varnameCount);
        $count += $varnameCount;

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
