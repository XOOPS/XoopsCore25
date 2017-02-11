<?php
/**
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 **/

$xoopsOption['checkadmin'] = true;
$xoopsOption['hascommon']  = true;
require_once './include/common.inc.php';
defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    /* @var $config_handler XoopsConfigHandler  */
    $config_handler = xoops_getHandler('config');
    if (array_key_exists('conf_ids', $_REQUEST)) {
        foreach ($_REQUEST['conf_ids'] as $key => $conf_id) {
            $config    = $config_handler->getConfig($conf_id);
            $new_value = $_REQUEST[$config->getVar('conf_name')];
            $config->setConfValueForInput($new_value);
            $config_handler->insertConfig($config);
        }
    }
    $wizard->redirectToPage('+1');
}

$pageHasForm = true;
$pageHasHelp = true;

if (!@include_once "../modules/system/language/{$wizard->language}/admin.php") {
    include_once '../modules/system/language/english/admin.php';
}

if (!@include_once "../modules/system/language/{$wizard->language}/admin/preferences.php") {
    include_once '../modules/system/language/english/admin/preferences.php';
}
/* @var $config_handler XoopsConfigHandler  */
$config_handler = xoops_getHandler('config');
$criteria       = new CriteriaCompo();
$criteria->add(new Criteria('conf_modid', 0));

$criteria2 = new CriteriaCompo();
foreach ($wizard->configs['conf_names'] as $conf_name) {
    $criteria2->add(new Criteria('conf_name', $conf_name), 'OR');
}
$criteria->add($criteria2);
$criteria->setSort('conf_catid ASC, conf_order ASC');
$configs = $config_handler->getConfigs($criteria);

include './include/createconfigform.php';
$wizard->form = createConfigform($configs);
$content      = $wizard->CreateForm();
include './include/install_tpl.php';
