<?php
/**
 * Extended User Profile
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
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();
$indexAdmin = new ModuleAdmin();

$indexAdmin->addItemButton(_ADD . ' ' . _PROFILE_AM_STEP, 'step.php?op=new', 'add', '');
echo $indexAdmin->addNavigation(basename(__FILE__));
echo $indexAdmin->renderButton('right', '');

$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : (isset($_REQUEST['id']) ? 'edit' : 'list');

$handler = xoops_getModuleHandler('regstep');
switch ($op) {
    case 'list':
        $GLOBALS['xoopsTpl']->assign('steps', $handler->getObjects(null, true, false));
        $template_main = 'profile_admin_steplist.tpl';
        break;

    case 'new':
        $obj = $handler->create();
        include_once dirname(__DIR__) . '/include/forms.php';
        $form = profile_getStepForm($obj);
        $form->display();
        break;

    case 'edit':
        $obj = $handler->get($_REQUEST['id']);
        include_once dirname(__DIR__) . '/include/forms.php';
        $form = profile_getStepForm($obj);
        $form->display();
        break;

    case 'save':
        if (isset($_REQUEST['id'])) {
            $obj = $handler->get($_REQUEST['id']);
        } else {
            $obj = $handler->create();
        }
        $obj->setVar('step_name', $_REQUEST['step_name']);
        $obj->setVar('step_order', $_REQUEST['step_order']);
        $obj->setVar('step_desc', $_REQUEST['step_desc']);
        $obj->setVar('step_save', $_REQUEST['step_save']);
        if ($handler->insert($obj)) {
            redirect_header('step.php', 3, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_STEP));
        }
        echo $obj->getHtmlErrors();
        $form = $obj->getForm();
        $form->display();
        break;

    case 'delete':
        $obj = $handler->get($_REQUEST['id']);
        if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == 1) {
            if ($handler->delete($obj)) {
                redirect_header('step.php', 3, sprintf(_PROFILE_AM_DELETEDSUCCESS, _PROFILE_AM_STEP));
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(array(
                              'ok' => 1,
                              'id' => $_REQUEST['id'],
                              'op' => 'delete'), $_SERVER['REQUEST_URI'], sprintf(_PROFILE_AM_RUSUREDEL, $obj->getVar('step_name')));
        }
        break;

    case 'toggle':
        if (isset($_GET['step_id'])) {
            $field_id = (int)$_GET['step_id'];
            if (isset($_GET['step_save'])) {
                $step_save = (int)$_GET['step_save'];
                profile_stepsave_toggle($step_id, $step_save);
            }
        }
        break;
}

if (!empty($template_main)) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}

/**
 * @param $step_d
 * @param $step_save
 */
function profile_stepsave_toggle($step_d, $step_save)
{
    $step_save = ($step_save == 1) ? 0 : 1;
    $handler   = xoops_getModuleHandler('regstep');
    $obj       = $handler->get($_REQUEST['step_id']);
    $obj->setVar('step_save', $step_save);
    if ($handler->insert($obj, true)) {
        redirect_header('step.php', 1, _PROFILE_AM_SAVESTEP_TOGGLE_SUCCESS);
    } else {
        redirect_header('step.php', 1, _PROFILE_AM_SAVESTEP_TOGGLE_FAILED);
    }
}

include_once __DIR__ . '/admin_footer.php';
