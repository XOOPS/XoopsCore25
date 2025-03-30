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
 * Installer template file
 *
 * This template was derived in part from sb-admin, a free, open source, Bootstrap
 * admin theme created by Start Bootstrap, made available under an MIT license.
 * See: https://github.com/BlackrockDigital/startbootstrap-sb-admin
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           Kris <kris@frxoops.org>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 * @author           Mamba
 **/

defined('XOOPS_INSTALL') || die('XOOPS Installation wizard die');

include_once __DIR__ . '/../../language/' . $wizard->language . '/global.php';

$versionParts = [];
preg_match('/(^[a-z\s]*)([0-9\.]*)/i', XOOPS_VERSION, $versionParts);
?>

<!doctype html>
<html lang="<?php echo _LANGCODE; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo XOOPS_VERSION . ' : ' . XOOPS_INSTALL_WIZARD; ?> (<?php echo ($wizard->pageIndex + 1) . '/' . count($wizard->pages); ?>)</title>

    <!-- Bootstrap CSS -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="../media/font-awesome6/css/fontawesome.min.css" rel="stylesheet" as="font" crossorigin="anonymous">
    <link href="../media/font-awesome6/css/solid.min.css" rel="stylesheet" as="font" crossorigin="anonymous">
    <link href="../media/font-awesome6/css/brands.min.css" rel="stylesheet" as="font" crossorigin="anonymous">
    <link href="../media/font-awesome6/css/v4-shims.min.css" rel="stylesheet" as="font" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <?php if (file_exists('language/' . $wizard->language . '/style.css')): ?>
        <link rel="stylesheet" href="language/<?php echo $wizard->language; ?>/style.css">
    <?php endif; ?>
</head>

<body>
<div class="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="assets/img/logo_small.png" alt="XOOPS"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Left sidebar -->
                <ul class="navbar-nav flex-column side-nav me-auto">
                    <?php foreach (array_keys($wizard->pages) as $k => $page): ?>
                        <li class="nav-item">
                            <?php if ($k == $wizard->pageIndex): ?>
                                <a class="nav-link active"><i class="<?php echo str_replace('fa fa-', 'fa-solid fa-', $wizard->pages[$page]['icon']); ?>"></i> <?php echo $wizard->pages[$page]['name']; ?></a>
                            <?php elseif ($k > $wizard->pageIndex): ?>
                                <a class="nav-link disabled"><i class="<?php echo str_replace('fa fa-', 'fa-solid fa-', $wizard->pages[$page]['icon']); ?>"></i> <?php echo $wizard->pages[$page]['name']; ?></a>
                            <?php else: ?>
                                <a class="nav-link" href="<?php echo $wizard->pageURI($page); ?>"><i class="<?php echo str_replace('fa fa-', 'fa-solid fa-', $wizard->pages[$page]['icon']); ?> text-success"></i> <?php echo $wizard->pages[$page]['name']; ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Right menu -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#"><i class="fa-solid fa-book"></i> <?php echo SUPPORT; ?></a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php
                            $supportFile = __DIR__ . '/../language/' . $wizard->language . '/support.php';
                            if (file_exists($supportFile)) {
                                $supports =  include $supportFile;
                            }
                            if (isset($supports) && is_array($supports)) { foreach ($supports as $support): ?>
                                <li><a class="dropdown-item" href="<?php echo $support['url']; ?>" target="_blank"><?php echo $support['title']; ?></a></li>
                            <?php endforeach; } ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/XOOPS/XoopsCore25" target="_blank" title="<?php echo XOOPS_SOURCE_CODE; ?>">
                            <i class="fa-brands fa-github fa-lg"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-wrapper">
        <div class="container-fluid">
            <!-- Status Cards -->
            <div class="row g-4 mb-4">
                <?php if (!empty($error)): ?>
                    <div class="col-lg-4">
                        <div class="card border-danger">
                            <div class="card-body bg-danger text-white">
                                <div class="row">
                                    <div class="col-3">
                                        <i class="<?php echo str_replace('fa fa-', 'fa-solid fa-', $wizard->pages[$wizard->currentPage]['icon']); ?> fa-3x"></i>
                                    </div>
                                    <div class="col-9 text-end">
                                        <div class="display-6"><i class="fa-solid fa-ban"></i></div>
                                        <div><?php echo XOOPS_ERROR_ENCOUNTERED; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Progress Card -->
                <div class="col-lg-4">
                    <div class="card border-primary">
                        <div class="card-body bg-primary text-white">
                            <div class="row">
                                <div class="col-3">
                                    <i class="<?php echo str_replace('fa fa-', 'fa-solid fa-', $wizard->pages[$wizard->currentPage]['icon']); ?> fa-3x"></i>
                                </div>
                                <div class="col-9 text-end">
                                    <div class="display-6"><?php echo ($wizard->pageIndex + 1) . '/' . count($wizard->pages); ?></div>
                                    <div><?php echo XOOPS_INSTALLING; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-primary">
                            <?php echo $wizard->pages[$wizard->currentPage]['name']; ?>
                        </div>
                    </div>
                </div>

                <!-- Version Card -->
                <div class="col-lg-4">
                    <div class="card border-success">
                        <div class="card-body bg-success text-white">
                            <div class="row">
                                <div class="col-3">
                                    <i class="fa-solid fa-tag fa-3x"></i>
                                </div>
                                <div class="col-9 text-end">
                                    <div class="display-6"><?php echo $versionParts[2]; ?></div>
                                    <div>Version</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-success">
                            <?php echo XOOPS_VERSION; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wizard Form -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="<?php echo $wizard->pages[$wizard->currentPage]['name']; ?>" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                <h2><?php echo $wizard->pages[$wizard->currentPage]['title']; ?></h2>
                                <?php echo $content; ?>

                                <div class="text-end mt-4">
                                    <button class="btn btn-lg btn-success" type="<?php echo !empty($pageHasForm) ? 'submit' : 'button'; ?>"
                                            <?php if (empty($pageHasForm)): ?>onclick="location.href='<?php echo $wizard->pageURI('+1'); ?>'"<?php endif; ?>>
                                        <?php echo BUTTON_NEXT; ?> <i class="fa-solid fa-caret-right"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<!-- jQuery -->
<!--<script src="assets/js/bootstrap.bundle.min.js"></script>-->

<!-- Bootstrap Core JavaScript -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/xo-installer.js"></script>
</body>
</html>
<script type="text/javascript">
    $(document).ready(function () {
        $(".xoform-help").hide();
        /**
         * Check the url to see if we reached 'page_end.php' and if so, launch the cleanup via ajax.
         **/
        if ('page_end.php' == location.pathname.substring(location.pathname.lastIndexOf('/') + 1)) {
            $.post( "cleanup.php", { instsuffix: <?php echo isset($install_rename_suffix) ? "'" . $install_rename_suffix . "'" : "''"; ?> } );
        };
    });
</script>
