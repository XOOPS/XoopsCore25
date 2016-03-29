<?php
/**
 * xoopsCodeTarea function
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**#@+
 * @deprecated
 */

/**
 * Displayes xoopsCode buttons and target textarea to which xoopscodes are inserted
 *
 * @param   string $textarea_id a unique id of the target textarea
 * @param int      $cols
 * @param int      $rows
 * @param null     $suffix
 */
function xoopsCodeTarea($textarea_id, $cols = 60, $rows = 15, $suffix = null)
{
    xoops_load('XoopsFormDhtmlTextArea');
    $hiddenText              = isset($suffix) ? 'xoopsHiddenText' . trim($suffix) : 'xoopsHiddenText';
    $content                 = isset($GLOBALS[$textarea_id]) ? $GLOBALS[$textarea_id] : '';
    $text_editor             = new XoopsFormDhtmlTextArea('', $textarea_id, $content, $rows, $cols, $hiddenText);
    $text_editor->htmlEditor = null;
    $text_editor->smilies    = false;
    echo $text_editor->render();
}

/**
 * Displays smilie image buttons used to insert smilie codes to a target textarea in a form
 *
 * @param   string $textarea_id a unique id of the target textarea
 */
function xoopsSmilies($textarea_id)
{
    $myts   = MyTextSanitizer::getInstance();
    $smiles = $myts->getSmileys(false);
    $count  = count($smiles);
    for ($i = 0; $i < $count; ++$i) {
        echo "<img src='" . XOOPS_UPLOAD_URL . '/' . htmlspecialchars($smiles[$i]['smile_url'], ENT_QUOTES) . "' border='0' alt='' onclick='xoopsCodeSmilie(\"{$textarea_id}\", \" " . $smiles[$i]['code'] . " \");' onmouseover='style.cursor=\"hand\"' />";
    }
    echo "&nbsp;[<a href='#moresmiley' onmouseover='style.cursor=\"hand\"' alt='' onclick='openWithSelfMain(\"" . XOOPS_URL . "/misc.php?action=showpopups&amp;type=smilies&amp;target={$textarea_id}\",\"smilies\",300,475);'>" . _MORE . '</a>]';
}
/**#@-*/
