<?php
/**
 * Private message
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             pm
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
use Xmf\Request;

include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$indexAdmin = new ModuleAdmin();
echo $indexAdmin->addNavigation(basename(__FILE__));

$op         = Request::hasVar('op', 'POST') ? Request::getCmd('op', 'form', 'POST') : Request::getCmd('op', 'form', 'GET');
/** @var \PmMessageHandler $pm_handler */
$pm_handler = xoops_getModuleHandler('message');

switch ($op) {
    default:
    case 'form':
        $form = $pm_handler->getPruneForm();
        $form->display();
        break;

    case 'prune':
        $criteria = new CriteriaCompo();
        $after  = Request::getArray('after', [], 'POST');
        $before = Request::getArray('before', [], 'POST');
        if (!empty($after['date']) && $after['date'] !== 'YYYY/MM/DD') {
            $afterTime = strtotime($after['date']);
            if (false !== $afterTime) {
                $criteria->add(new Criteria('msg_time', $afterTime + (int)($after['time'] ?? 0), '>'));
            }
        }
        if (!empty($before['date']) && $before['date'] !== 'YYYY/MM/DD') {
            $beforeTime = strtotime($before['date']);
            if (false !== $beforeTime) {
                $criteria->add(new Criteria('msg_time', $beforeTime + (int)($before['time'] ?? 0), '<'));
            }
        }
        if (Request::getInt('onlyread', 0, 'POST') === 1) {
            $criteria->add(new Criteria('read_msg', 1));
        }
        if (Request::getInt('includesave', 0, 'POST') === 0) {
            $savecriteria = new CriteriaCompo(new Criteria('to_save', 0));
            $savecriteria->add(new Criteria('from_save', 0));
            $criteria->add($savecriteria);
        }
        $notifyusers = Request::getInt('notifyusers', 0, 'POST') === 1;
        if ($notifyusers) {
            $notifycriteria = $criteria;
            $notifycriteria->add(new Criteria('to_delete', 0));
            $notifycriteria->setGroupBy('to_userid');
            // Get array of uid => number of deleted messages
            $uids = $pm_handler->getCount($notifycriteria);
        }
        $deletedrows = $pm_handler->deleteAll($criteria);
        if ($deletedrows === false) {
            redirect_header('prune.php', 2, _PM_AM_ERRORWHILEPRUNING);
        }
        if ($notifyusers) {
            $errors = false;
            foreach ($uids as $uid => $messagecount) {
                $pm = $pm_handler->create();
                $pm->setVar('subject', $GLOBALS['xoopsModuleConfig']['prunesubject']);
                $pm->setVar('msg_text', str_replace('{PM_COUNT}', $messagecount, $GLOBALS['xoopsModuleConfig']['prunemessage']));
                $pm->setVar('to_userid', $uid);
                $pm->setVar('from_userid', $GLOBALS['xoopsUser']->getVar('uid'));
                $pm->setVar('msg_time', time());
                if (!$pm_handler->insert($pm)) {
                    $errors     = true;
                    $errormsg[] = $pm->getHtmlErrors();
                }
                unset($pm);
            }
            if ($errors === true) {
                echo implode('<br>', $errormsg);
                xoops_cp_footer();
                exit();
            }
        }
        redirect_header('admin.php', 2, sprintf(_PM_AM_MESSAGESPRUNED, $deletedrows));
        break;
}
include_once __DIR__ . '/admin_footer.php';
//xoops_cp_footer();
