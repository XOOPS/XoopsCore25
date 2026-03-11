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
        // Match any foreach opening tag — order-independent, allows extra attrs and modifiers
        $pattern = '/<\{\s*foreach\b([^}]*)\}>/';
        $searchOffset = 0;

        while (preg_match($pattern, $content, $match, PREG_OFFSET_CAPTURE, $searchOffset)) {
            $fullTag = $match[0][0];
            $tagAttrs = $match[1][0];
            $foreachTagStart = $match[0][1];
            $foreachTagEnd = $foreachTagStart + strlen($fullTag);

            // Extract item= and from= values from attributes (order-independent)
            if (!preg_match('/\bitem=([a-zA-Z0-9_]+)/', $tagAttrs, $itemMatch)
                || !preg_match('/\bfrom=\$([a-zA-Z0-9_]+)(?:\|[^\s}>]+)?/', $tagAttrs, $fromMatch)
            ) {
                $searchOffset = $foreachTagEnd;
                continue;
            }

            $varName = $itemMatch[1];
            $fromVar = $fromMatch[1];

            // Only fix when item name equals the from variable base name
            if ($varName !== $fromVar) {
                $searchOffset = $foreachTagEnd;
                continue;
            }

            $newItem = $varName . '_item';

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
                // Could not find matching close tag — skip this one, try the next
                $searchOffset = $foreachTagEnd;
                continue;
            }

            // Extract the block between opening and closing foreach tags
            $blockContent = substr($content, $foreachTagEnd, $closingTagStart - $foreachTagEnd);

            // Replace $varName references, but skip nested foreach blocks that
            // redefine the same item name (they have their own scope)
            $fixedBlock = $this->replaceVarInBlock($varName, $newItem, $blockContent);

            // Build new opening tag: replace only item=X, preserve all other attributes
            $newTagAttrs = preg_replace('/\bitem=' . preg_quote($varName, '/') . '\b/', 'item=' . $newItem, $tagAttrs);
            $newTag = '<{foreach' . $newTagAttrs . '}>';

            // Rebuild content: before + new tag + fixed block + from closing tag onward
            $content = substr($content, 0, $foreachTagStart)
                     . $newTag
                     . $fixedBlock
                     . substr($content, $closingTagStart);

            // Advance past the newly written tag to keep scan linear
            $searchOffset = $foreachTagStart + strlen($newTag);

            $count++;
        }

        return $content;
    }

    /**
     * Replace $varName with $newItem in a foreach block, respecting nested scope.
     *
     * Skips content inside nested foreach blocks that redefine the same item name,
     * since those have their own scope for that variable.
     *
     * @param string $varName  original variable name
     * @param string $newItem  replacement variable name
     * @param string $block    block content between foreach tags
     *
     * @return string block with replacements applied
     */
    protected function replaceVarInBlock(string $varName, string $newItem, string $block): string
    {
        $replacePattern = '/\$' . preg_quote($varName, '/') . '(?![a-zA-Z0-9_])/';

        // Find nested foreach blocks that redefine item=$varName (order-independent)
        $nestedPattern = '/<\{\s*foreach\b(?=[^}]*\bitem=' . preg_quote($varName, '/') . '\b)[^}]*\}>/';
        if (!preg_match($nestedPattern, $block)) {
            // No nested scope conflict — safe to replace the entire block
            return preg_replace($replacePattern, '$' . $newItem, $block);
        }

        // Process segments between nested foreach blocks that shadow $varName
        $result = '';
        $pos = 0;
        while (preg_match($nestedPattern, $block, $nestedMatch, PREG_OFFSET_CAPTURE, $pos)) {
            $nestedStart = $nestedMatch[0][1];
            // Replace in the segment before the nested foreach
            $segment = substr($block, $pos, $nestedStart - $pos);
            $result .= preg_replace($replacePattern, '$' . $newItem, $segment);

            // Find the matching closing tag for this nested foreach
            $depth = 1;
            $scanPos = strpos($block, '}>', $nestedStart);
            if ($scanPos === false) {
                // Malformed — append rest unchanged
                $result .= substr($block, $nestedStart);
                return $result;
            }
            $scanPos += 2;

            while ($depth > 0) {
                if (!preg_match('/<\{\s*(\/?)foreach\b/', $block, $inner, PREG_OFFSET_CAPTURE, $scanPos)) {
                    break;
                }
                $tagEnd = strpos($block, '}>', $inner[0][1]);
                if ($tagEnd === false) {
                    break;
                }
                $tagEnd += 2;
                if ($inner[1][0] === '/') {
                    $depth--;
                } else {
                    $depth++;
                }
                $scanPos = $tagEnd;
            }

            // Append the entire nested block unchanged (it has its own scope)
            $result .= substr($block, $nestedStart, $scanPos - $nestedStart);
            $pos = $scanPos;
        }

        // Replace in the remaining segment after the last nested block
        $result .= preg_replace($replacePattern, '$' . $newItem, substr($block, $pos));

        return $result;
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
            trigger_error(sprintf('NULL return processing: %s', $filename), E_USER_WARNING);
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
            if ($result === false) {
                trigger_error(sprintf('Error writing file: %s', $filename), E_USER_WARNING);
            }
            $output->outputIssue($output->makeOutputIssue($filename, $count));
        }
    }
}
