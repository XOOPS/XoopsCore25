<?php
/**
 *  Xoopsemotions plugin for tinymce
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class / xoopseditor
 * @subpackage          tinymce / xoops plugins
 * @since               2.3.0
 * @author              ralf57
 * @author              luciorota <lucio.rota@gmail.com>
 * @author              Laurent JEN <dugris@frxoops.org>
 */

// load mainfile.php - start
$current_path = __DIR__;
if (DIRECTORY_SEPARATOR !== '/') {
    $current_path = str_replace(DIRECTORY_SEPARATOR, '/', $current_path);
}
$xoops_root_path = substr($current_path, 0, strpos(strtolower($current_path), '/class/xoopseditor/tinymce/'));
include_once $xoops_root_path . '/mainfile.php';
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
// load mainfile.php - end

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

$myts = MyTextSanitizer::getInstance();

if ($admin && $op === 'SmilesAdd') {
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header($current_file, 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    include_once XOOPS_ROOT_PATH . '/class/uploader.php';
    $uploader = new XoopsMediaUploader(XOOPS_UPLOAD_PATH, array(
        'image/gif',
        'image/jpeg',
        'image/pjpeg',
        'image/x-png',
        'image/png'), 100000, 120, 120);
    $uploader->setPrefix('smil');
    if ($uploader->fetchMedia($_POST['xoops_upload_file'][0])) {
        if (!$uploader->upload()) {
            $err = $uploader->getErrors();
        } else {
            $smile_url     = $uploader->getSavedFileName();
            $smile_code    = $myts->stripSlashesGPC($_POST['smile_code']);
            $smile_desc    = $myts->stripSlashesGPC($_POST['smile_desc']);
            $smile_display = (int)$_POST['smile_display'] > 0 ? 1 : 0;
            $newid         = $db->genId($db->prefix('smilies') . '_id_seq');
            $sql           = sprintf('INSERT INTO %s (id, code, smile_url, emotion, display) VALUES (%d, %s, %s, %s, %d)', $db->prefix('smiles'), $newid, $db->quoteString($smile_code), $db->quoteString($smile_url), $db->quoteString($smile_desc), $smile_display);
            if (!$db->query($sql)) {
                $err = 'Failed storing smiley data into the database';
            }
        }
    } else {
        $err = $uploader->getErrors();
    }
    if (!isset($err)) {
        unset($_SESSION['XoopsEmotions']);
        unset($_SESSION['XoopsEmotions_expire']);
        redirect_header($current_file, 2, _AM_DBUPDATED);
    } else {
        redirect_header($current_file, 3, xoops_error($err));
    }
}

$time = time();
if (!isset($_SESSION['XoopsEmotions']) && @$_SESSION['XoopsEmotions_expire'] < $time) {
    $_SESSION['XoopsEmotions']        = $myts->getSmileys();
    $_SESSION['XoopsEmotions_expire'] = $time + 300;
}

//xoops_header(false);
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . _LANGCODE . '" lang="' . _LANGCODE . '">';
echo '<head>';
echo '<meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />';
echo '<meta http-equiv="content-language" content="' . _LANGCODE . '" />';
?>
<head>
    <title>{#xoopsemotions_dlg.title}</title>
    <script type="text/javascript" src="../../tiny_mce_popup.js"></script>
    <script type="text/javascript" src="../../utils/mctabs.js"></script>
    <script type="text/javascript" src="../../utils/form_utils.js"></script>
    <script type="text/javascript" src="../../utils/validate.js"></script>
    <script type="text/javascript" src="js/xoopsemotions.js"></script>
    <link href="<?php echo xoops_getcss($xoopsConfig['theme_set']); ?>" rel="stylesheet" type="text/css"/>
    <link href="css/xoopsemotions.css" rel="stylesheet" type="text/css"/>
    <base target="_self"/>
</head>
<body>

<?php
if (!$_SESSION['XoopsEmotions'] && !$admin) {
    echo "<div class='xoopsEmotions'>";
    echo '<div>{#xoopsemotions_dlg.error_noemotions}</div>';
    echo '</div>';
    echo "<div class='mceActionPanel floatright'>";
    echo "<input type='button' id='cancel' name='cancel' value='{#cancel}' onclick='tinyMCEPopup.close();' />";
    echo '</div>';
    xoops_footer();
    exit();
}
?>

<div class="tabs">
    <ul>
        <li id="tab_emotionsbrowser" class="current"><span><a href="javascript:mcTabs.displayTab('tab_emotionsbrowser','emotionsbrowser_panel');"
                                                              onmousedown="return false;">{#xoopsemotions_dlg.tab_emotionsbrowser}</a></span></li>
        <?php
        if ($admin) {
            echo '<li id="tab_emotionsadmin"><span><a href="javascript:mcTabs.displayTab(\'tab_emotionsadmin\',\'emotionsadmin_panel\');" onmousedown="return false;">{#xoopsemotions_dlg.tab_emotionsadmin}</a></span></li>';
        }
        ?>
    </ul>
</div>

<div class="panel_wrapper">
    <div id="emotionsbrowser_panel" class="panel current" style="overflow:auto;">
        <?php
        if ($smiles = $_SESSION['XoopsEmotions']) {
            echo '<div><strong>' . _MSC_CLICKASMILIE . '</strong></div>';
            echo "<div class='xoopsEmotions'>";
            $count = count($smiles);

            for ($i = 0; $i < $count; ++$i) {
                if ($op == '') {
                    if ($smiles[$i]['display']) {
                        echo '<img class="xoopsEmotions" onclick="XoopsemotionsDialog.insert(this);" src="' . XOOPS_UPLOAD_URL . '/' . $smiles[$i]['smile_url'] . '" alt="' . $myts->htmlspecialchars($smiles[$i]['emotion']) . '" title="' . $myts->htmlspecialchars($smiles[$i]['emotion']) . '" />';
                    }
                } else {
                    echo '<img class="xoopsEmotions" onclick="XoopsemotionsDialog.insert(this);" src="' . XOOPS_UPLOAD_URL . '/' . $smiles[$i]['smile_url'] . '" alt="' . $myts->htmlspecialchars($smiles[$i]['emotion']) . '" title="' . $myts->htmlspecialchars($smiles[$i]['emotion']) . '" />';
                }
            }
            if ($op == '') {
                echo '<div class="xoopsEmotions">';
                echo '<a class="xoopsEmotions" href="' . $current_file . '?op=' . _MORE . '">' . _MORE . '</a>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<div>{#xoopsemotions_dlg.error_noemotions}</div>';
        }
        ?>
        <div class="mceActionPanel floatright">
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();"/>
        </div>
    </div>

    <div id="emotionsadmin_panel" class="panel" style="overflow:auto;">
        <?php
        if ($admin) {
            include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

            $smile_form = new XoopsThemeForm(_AM_ADDSMILE, 'smileform', $current_file, 'post', true);
            $smile_form->setExtra('enctype="multipart/form-data"');
            $smile_form->addElement(new XoopsFormText(_AM_SMILECODE, 'smile_code', 26, 25, ''), true);
            $smile_form->addElement(new XoopsFormText(_AM_SMILEEMOTION, 'smile_desc', 26, 25, ''), true);
            $smile_select = new XoopsFormFile('', 'smile_url', 5000000);
            $smile_label  = new XoopsFormLabel('', '<img src="' . XOOPS_UPLOAD_URL . '/blank.gif" alt="" />');
            $smile_tray   = new XoopsFormElementTray(_IMAGEFILE . ':', '&nbsp;');
            $smile_tray->addElement($smile_select);
            $smile_tray->addElement($smile_label);
            $smile_form->addElement($smile_tray);
            $smile_form->addElement(new XoopsFormRadioYN(_AM_DISPLAYF, 'smile_display', 1));
            $smile_form->addElement(new XoopsFormHidden('id', ''));
            $smile_form->addElement(new XoopsFormHidden('op', 'SmilesAdd'));
            $smile_form->addElement(new XoopsFormHidden('fct', 'smilies'));
            $smile_form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

            $smile_form->display();
        }
        ?>
        <div class="mceActionPanel floatright">
            <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();"/>
        </div>
    </div>

</div>
<?php xoops_footer(); ?>
