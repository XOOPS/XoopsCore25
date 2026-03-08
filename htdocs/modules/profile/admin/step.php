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
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

use Xmf\Request;

include_once __DIR__ . '/admin_header.php';
xoops_cp_header();
$indexAdmin = new ModuleAdmin();

$indexAdmin->addItemButton(_ADD . ' ' . _PROFILE_AM_STEP, 'step.php?op=new', 'add', '');
echo $indexAdmin->addNavigation(basename(__FILE__));
echo $indexAdmin->renderButton('right', '');

$op = Request::hasVar('op', 'POST') ? Request::getCmd('op', 'list', 'POST') : Request::getCmd('op', (Request::hasVar('id', 'GET') || Request::hasVar('id', 'POST')) ? 'edit' : 'list', 'GET');

$handler = xoops_getModuleHandler('regstep');
switch ($op) {
    case 'list':
        $criteria = new CriteriaCompo();
        $criteria->setSort('step_order');
        $criteria->setOrder('ASC');
        $GLOBALS['xoopsTpl']->assign('steps', $handler->getObjects($criteria, true, false));
        $template_main = 'profile_admin_steplist.tpl';
        break;

    case 'new':
        $obj = $handler->create();
        include_once dirname(__DIR__) . '/include/forms.php';
        $form = profile_getStepForm($obj);
        $form->display();
        break;

    case 'edit':
        $obj = $handler->get(Request::getInt('id', 0, 'GET'));
        if (!is_object($obj)) {
            redirect_header('step.php', 3, _TAKINGBACK);
        }
        include_once dirname(__DIR__) . '/include/forms.php';
        $form = profile_getStepForm($obj);
        $form->display();
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('step.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (Request::hasVar('id', 'POST')) {
            $obj = $handler->get(Request::getInt('id', 0, 'POST'));
            if (!is_object($obj)) {
                redirect_header('step.php', 3, _TAKINGBACK);
            }
        } else {
            $obj = $handler->create();
        }
        $obj->setVar('step_name', Request::getString('step_name', '', 'POST'));
        $obj->setVar('step_order', Request::getInt('step_order', 0, 'POST'));
        $obj->setVar('step_desc', Request::getString('step_desc', '', 'POST'));
        $obj->setVar('step_save', Request::getInt('step_save', 0, 'POST'));
        if ($handler->insert($obj)) {
            redirect_header('step.php', 3, sprintf(_PROFILE_AM_SAVEDSUCCESS, _PROFILE_AM_STEP));
        }
        echo $obj->getHtmlErrors();
        $form = $obj->getForm();
        $form->display();
        break;

    case 'delete':
        $obj = $handler->get(Request::hasVar('id', 'POST') ? Request::getInt('id', 0, 'POST') : Request::getInt('id', 0, 'GET'));
        if (!is_object($obj)) {
            redirect_header('step.php', 3, _TAKINGBACK);
        }
        if (Request::getInt('ok', 0, 'POST') === 1) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('step.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($handler->delete($obj)) {
                redirect_header('step.php', 3, sprintf(_PROFILE_AM_DELETEDSUCCESS, _PROFILE_AM_STEP));
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                [
                    'ok' => 1,
                    'id' => $obj->getVar('step_id'),
                    'op' => 'delete',
                ],
                $_SERVER['REQUEST_URI'],
                sprintf(_PROFILE_AM_RUSUREDEL, $obj->getVar('step_name')),
            );
        }
        break;

    case 'toggle':
        if (Request::hasVar('step_id', 'GET')) {
            $step_id = Request::getInt('step_id', 0, 'GET');
            if (Request::hasVar('step_save', 'GET')) {
                $step_save = Request::getInt('step_save', 0, 'GET');
                profile_stepsave_toggle($step_id, $step_save);
            }
        }
        break;
}

if (!empty($template_main)) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}

/**
 * @param int $stepId
 * @param int $stepSave
 */
function profile_stepsave_toggle(int $stepId, int $stepSave): void
{
    $stepSave = ($stepSave === 1) ? 0 : 1;
    $handler  = xoops_getModuleHandler('regstep');
    $obj      = $handler->get($stepId);
    if (!is_object($obj)) {
        redirect_header('step.php', 1, _PROFILE_AM_SAVESTEP_TOGGLE_FAILED);
    }
    $obj->setVar('step_save', $stepSave);
    if ($handler->insert($obj, true)) {
        redirect_header('step.php', 1, _PROFILE_AM_SAVESTEP_TOGGLE_SUCCESS);
    } else {
        redirect_header('step.php', 1, _PROFILE_AM_SAVESTEP_TOGGLE_FAILED);
    }
}

include_once __DIR__ . '/admin_footer.php';
