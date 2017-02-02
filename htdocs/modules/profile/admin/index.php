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

/**
 * @copyright      {@link http://xoops.org/ XOOPS Project}
 * @license        {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author         XOOPS Development Team, Raul Recio (AKA UNFOR)
 */

include_once __DIR__ . '/admin_header.php';
xoops_cp_header();

$indexAdmin = new ModuleAdmin();
//-----------------------
// $xpPartnerHandler = xoops_getModuleHandler('partners', $xoopsModule->getVar('dirname'));

// $totalPartners = $xpPartnerHandler->getCount();
// $totalNonActivePartners = $xpPartnerHandler->getCount(new Criteria('status', 0, '='));
// $totalActivePartners = $totalPartners - $totalNonActivePartners;

// $indexAdmin->addInfoBox(_MD_XPARTNERS_DASHBOARD);

// $indexAdmin->addInfoBoxLine(_MD_XPARTNERS_DASHBOARD, "<infolabel>" ._MD_XPARTNERS_TOTALACTIVE. "</infolabel>", $totalActivePartners, 'Green');
// $indexAdmin->addInfoBoxLine(_MD_XPARTNERS_DASHBOARD,  "<infolabel>" ._MD_XPARTNERS_TOTALNONACTIVE. "</infolabel>", $totalNonActivePartners, 'Red');
// $indexAdmin->addInfoBoxLine(_MD_XPARTNERS_DASHBOARD,  "<infolabel>" ._MD_XPARTNERS_TOTALPARTNERS. "</infolabel><infotext>", $totalPartners."</infotext>");
//----------------------------

echo $indexAdmin->addNavigation(basename(__FILE__));
echo $indexAdmin->renderIndex();

include_once __DIR__ . '/admin_footer.php';
//xoops_cp_footer();
