<?php declare(strict_types=1);
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Installer template file
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright   XOOPS Project (https://xoops.org)
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since       2.3.0
 * @author      Haruki Setoyama  <haruki@planewave.org>
 * @author      Kazumi Ono <webmaster@myweb.ne.jp>
 * @author      Skalpa Keo <skalpa@xoops.org>
 * @author      Taiwen Jiang <phppp@users.sourceforge.net>
 * @author      Kris <kris@frxoops.org>
 * @author      DuGris (aka L. JEN) <dugris@frxoops.org>
 **/
defined('XOOPS_INSTALL') || exit('XOOPS Installation wizard die');

require_once \dirname(__DIR__, 3) . '/language/' . $wizard->language . '/global.php';
?>
<!DOCTYPE html>
<html xml:lang="<?php echo _LANGCODE; ?>" lang="<?php echo _LANGCODE; ?>">

<head>
    <title>
        <?php echo XOOPS_VERSION . ' : ' . XOOPS_INSTALL_WIZARD; ?>
        (<?php echo ($wizard->pageIndex + 1) . '/' . (is_countable($wizard->pages) ? count($wizard->pages) : 0); ?>)
    </title>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo _INSTALL_CHARSET ?>">
    <link rel="shortcut icon" type="image/ico" href="../favicon.ico">
    <link charset="UTF-8" rel="stylesheet" type="text/css" media="all" href="assets/css/style.css">
    <?php
    if (file_exists('language/' . $wizard->language . '/style.css')) {
        echo '<link charset="UTF-8" rel="stylesheet" type="text/css" media="all" href="language/' . $wizard->language . '/style.css">';
    } else {
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/assets/css/style.css');
    }
    ?>

    <script type="text/javascript" src="./../assets/js/prototype.js"></script>
    <script type="text/javascript" src="./../assets/js/xo-installer.js"></script>
</head>

<body>

<div id="xo-content">

    <form id='<?php echo $wizard->pages[$wizard->currentPage]['name']; ?>' action='<?php echo $_SERVER['SCRIPT_NAME']; ?>'
          method='post'>

        <?php echo $content; ?>

        <div id="buttons">
            <?php if (@$pageHasForm) {
            ?>
            <button type="submit">
                <?php
                } else {
                ?>
                <button type="button" accesskey="n" onclick="location.href='<?php echo 'index.php'; ?>'">
                    <?php
                    } ?>
                    <?php echo BUTTON_NEXT; ?>
                </button>
        </div>
    </form>

</div>
</body>
</html>
