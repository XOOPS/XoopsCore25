<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Database character set configuration page
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.3.0
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 */

if (!defined('XOOPS_ROOT_PATH')) {
    die('Bad installation: please add this folder to the XOOPS install you want to upgrade');
}

$vars =& $_SESSION['settings'];

/**
 * @return array
 */
function getDbCharsets()
{
    $charsets = array();

    $charsets['utf8'] = array();
    $ut8_available    = false;
    $sql              = 'SHOW CHARSET';
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    if ($GLOBALS['xoopsDB']->isResultSet($result)) {
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $charsets[$row['Charset']]['desc'] = $row['Description'];
            if ($row['Charset'] === 'utf8') {
                $ut8_available = true;
            }
        }
    }

    if (!$ut8_available) {
        unset($charsets['utf8']);
    }

    return $charsets;
}

/**
 * @return array
 */
function getDbCollations()
{
    $collations = array();
    $charsets   = getDbCharsets();

    $sql    = 'SHOW COLLATION';
    $result = $GLOBALS['xoopsDB']->queryF($sql);
    if ($GLOBALS['xoopsDB']->isResultSet($result)) {
        while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
            $charsets[$row['Charset']]['collation'][] = $row['Collation'];
        }
    }

    return $charsets;
}

/**
 * @param        $name
 * @param        $value
 * @param        $label
 * @param string $help
 *
 * @return string
 */
function xoFormFieldCollation($name, $value, $label, $help = '')
{
    $collations = getDbCollations();

    $myts  = \MyTextSanitizer::getInstance();
    $label = $myts->htmlSpecialChars($label, ENT_QUOTES, _UPGRADE_CHARSET, false);
    $name  = $myts->htmlSpecialChars($name, ENT_QUOTES, _UPGRADE_CHARSET, false);
    $value = $myts->htmlSpecialChars($value, ENT_QUOTES);

    $field = "<label for='$name'>$label</label>\n";
    if ($help) {
        $field .= '<div class="xoform-help">' . $help . "</div>\n";
    }
    $field .= "<select name='$name' id='$name'\">";
    $field .= "<option value=''>" . DB_COLLATION_NOCHANGE . '</option>';

    $collation_default = '';
    $options           = '';
    foreach ($collations as $key => $charset) {
        $field .= "<optgroup label='{$key} - ({$charset['desc']})'>";
        foreach ($charset['collation'] as $collation) {
            $field .= "<option value='{$collation}'" . (($value == $collation) ? " selected='selected'" : '') . ">{$collation}</option>";
        }
        $field .= '</optgroup>';
    }
    $field .= '</select>';

    return $field;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && @$_POST['task'] === 'db') {
    $params = array('DB_COLLATION');
    foreach ($params as $name) {
        $vars[$name] = isset($_POST[$name]) ? $_POST[$name] : '';
    }

    return $vars;
}

if (!isset($vars['DB_COLLATION'])) {
    $vars['DB_COLLATION'] = '';
}

?>
<?php if (!empty($error)) {
    echo '<div class="x2-note error">' . $error . "</div>\n";
} ?>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method='post'>
    <fieldset>
        <legend><?php echo LEGEND_DATABASE; ?></legend>
        <?php echo xoFormFieldCollation('DB_COLLATION', $vars['DB_COLLATION'], DB_COLLATION_LABEL, DB_COLLATION_HELP); ?>

    </fieldset>
    <input type="hidden" name="action" value="next"/>
    <input type="hidden" name="task" value="db"/>

    <div class="xo-formbuttons">
        <button type="submit"><?php echo _SUBMIT; ?></button>
    </div>
