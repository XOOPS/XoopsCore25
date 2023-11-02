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
 * Xoopsemoticons plugin for tinymce v5
 *
 * @copyright      XOOPS Project  (https://xoops.org)
 * @license        GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author         ForMuss
 */

$current_path = __DIR__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(DIRECTORY_SEPARATOR, '/', $current_path);
}
$xoops_root_path = substr($current_path, 0, strpos(strtolower($current_path), '/class/xoopseditor/tinymce5/'));
include_once $xoops_root_path . '/mainfile.php';
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

// get current filename
$current_file = basename(__FILE__);

// load language definitions
xoops_loadLanguage('admin', 'system');
xoops_loadLanguage('admin/smilies', 'system');
xoops_loadLanguage('misc');

// include system category definitions - start
include_once XOOPS_ROOT_PATH . '/modules/system/constants.php';

// check user/group
$groups        = is_object($GLOBALS['xoopsUser']) ? $GLOBALS['xoopsUser']->getGroups() : array(XOOPS_GROUP_ANONYMOUS);
$gperm_handler = xoops_getHandler('groupperm');
$admin         = $gperm_handler->checkRight('system_admin', XOOPS_SYSTEM_SMILE, $groups);

$op = '';
if (!empty($_GET['op'])) {
    $op = trim($_GET['op']);
} elseif (!empty($_POST['op'])) {
    $op = trim($_POST['op']);
}

$myts = \MyTextSanitizer::getInstance();
$time = time();
if (!isset($_SESSION['XoopsEmotions']) && (isset($_SESSION['XoopsEmotions_expire']) && $_SESSION['XoopsEmotions_expire'] < $time)) {
    $_SESSION['XoopsEmotions']        = $myts->getSmileys();
    $_SESSION['XoopsEmotions_expire'] = $time + 300;
}
$GLOBALS['xoopsLogger']->activated = false;

echo '<!doctype html>';
echo '<html lang="' . _LANGCODE . '">';
echo '<head>';
echo '<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />';
echo '<meta http-equiv="content-language" content="' . _LANGCODE . '" />';
echo '<link href="css/bootstrap.min.css" rel="stylesheet" type="text/css"/>';
echo '<link href="' . xoops_getcss($xoopsConfig['theme_set']) . '" rel="stylesheet" type="text/css"/>';
echo '</head>';
echo '<body>';
echo '<div class="container-fluid">';
if ($smiles = $_SESSION['XoopsEmotions']) {
    echo '<div class="row"><div class="col"><h6>' . _MSC_CLICKASMILIE . '</h6></div></div>';
    $count = count($smiles);

    for ($i = 0; $i < $count; ++$i) {
        if ($op == '') {
            if ($smiles[$i]['display']) {
                echo '<img class="xoopsemot p-1" style="cursor: pointer;" src="' . XOOPS_UPLOAD_URL . '/' . $smiles[$i]['smile_url'] . '" alt="' . $myts->htmlSpecialChars($smiles[$i]['emotion']) . '" title="' . $myts->htmlSpecialChars($smiles[$i]['emotion']) . '" />';
            }
        } else {
            echo '<img class="xoopsemot p-1" style="cursor: pointer;" src="' . XOOPS_UPLOAD_URL . '/' . $smiles[$i]['smile_url'] . '" alt="' . $myts->htmlSpecialChars($smiles[$i]['emotion']) . '" title="' . $myts->htmlSpecialChars($smiles[$i]['emotion']) . '" />';
        }
    }
    if ($op == '') {
        echo '<div class="row">';
        echo '<div class="col">';
        echo '<a class="btn btn-sm btn-secondary float-right" href="' . $current_file . '?op=' . _MORE . '">' . _MORE . '</a>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo 'error';
    
}
echo '</div>';
echo '</body>';
?>
<script type="text/javascript">
    var elements = document.getElementsByClassName('xoopsemot');

    var myFunction = function() {
        var emot = this.src;
        var title = this.title;

        window.parent.postMessage({
            mceAction: 'insertEmot',
            data: {
                src: emot,
                title: title
            }
        }, origin);
/*
        window.parent.postMessage({
            mceAction: 'close'
        }, origin);*/
    };

    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', myFunction, false);
    }
        //event.preventDefault();
//alert( 'TEst' + this.src );
        // send the "execCommand" mceAction to call the "iframeCommand" command
        /*window.parent.postMessage({
            mceAction: 'execCommand',
            cmd: 'insertEmot',
            value: document.getElementById('dialog-input').value
        }, origin);*/
    //});
</script>
<?
xoops_footer();
