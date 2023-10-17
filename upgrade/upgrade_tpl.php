<?php
defined('XOOPS_ROOT_PATH') or die();

global $upgradeControl;

?><body>
<!doctype html>
<html lang="<?php echo _LANGCODE; ?>">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?php echo XOOPS_VERSION . ' : ' . _XOOPS_UPGRADE; ?></title>

    <link rel="icon" type="image/png" href="<?php echo XOOPS_URL ?>/upgrade/assets/img/favicon.png"/>
    <!-- Bootstrap Core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../media/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <?php
    if (!empty($extraSources)) {
        echo $extraSources;
    }
    ?>
    <?php
    if (file_exists('language/' . $upgradeControl->upgradeLanguage . '/style.css')) {
        echo '<link rel="stylesheet" type="text/css" media="all" href="language/'
            . $upgradeControl->upgradeLanguage . '/style.css" />';
    }
    ?>

</head>

<body>
<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand"><img src="assets/img/logo_small.png"></div>
        </div>
        <!-- Top Menu Items -->
        <ul class="nav navbar-right top-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?php echo _LANGUAGE; ?>"><i class="fa fa-lg fa-language"></i> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <?php
                    $languages = $upgradeControl->availableLanguages();
                    foreach ($languages as $lang) {
                        $upgradeControl->loadLanguage('support', $lang);
                        echo '<li><a href="?lang=' . $lang . '">' . $lang . '</a></li>';
                    }
                    ?>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-book"></i> <?php echo _SUPPORT; ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <?php
                    foreach ($upgradeControl->supportSites as $lang => $support) {
                        echo '<li><a href="' . $support['url'] . '" target="_blank">' . $support['title'] . '</a></li>';
                    }
                    ?>
                </ul>
            </li>
            <li>
                <a href="https://github.com/XOOPS/XoopsCore25" target="_blank" title="<?php echo _XOOPS_SOURCE_CODE; ?>"><i class="fa fa-lg fa-github"></i></a>
            </li>
        </ul>
        <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav side-nav">
                <?php
                $firstNeeded = true;
                foreach ($upgradeControl->upgradeQueue as $stepName => $info) {
                    if (!$info->applied && $firstNeeded) {
                        echo'<li class="active"><a><span class="fa fa-exclamation-triangle"></span> '
                            . $stepName . '</a></li>';
                        $firstNeeded = false;
                    } elseif (!$info->applied) {
                        echo'<li><a><span class="fa fa-exclamation-triangle text-warning"></span> '
                            . $stepName . '</a></li>';
                    } else {
                        echo'<li><a><span class="fa fa-check text-success"></span> '
                            . $stepName . '</a></li>';
                    }
                }
                ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>

    <div id="page-wrapper">

        <div class="container-fluid">
            <div class="row">
                <?php if (!isset($_SESSION['preflight']) ||  $_SESSION['preflight'] != 'complete') { ?>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <span class="fa fa fa-hand-paper-o fa-5x"></span>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge">Smarty3</div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-primary">
                            <?php echo _XOOPS_SMARTY3_MIGRATION; ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php if (!empty($error)) { ?>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-red">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <span class="fa fa-hand-stop-o fa-5x"></span>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><span class="fa fa-ban"></span></div>
                                    <div><?php echo XOOPS_ERROR_ENCOUNTERED; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <?php echo XOOPS_ERROR_SEE_BELOW; ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <span class="fa fa-dashboard fa-5x"></span>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $upgradeControl->countUpgradeQueue(); ?></div>
                                    <div><?php echo _PATCH_COUNT; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-primary">
                            <?php echo _XOOPS_UPGRADE; ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <?php
                $versionParts=array();
                $versionResult = preg_match ('/(^[a-z\s]*)([0-9\.]*)/i', XOOPS_VERSION, $versionParts);
                ?>

                <div class="col-lg-3 col-md-6">
                    <div class="panel panel-green">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-3">
                                    <i class="fa fa-tag fa-5x"></i>
                                </div>
                                <div class="col-xs-9 text-right">
                                    <div class="huge"><?php echo $versionParts[2]; ?></div>
                                    <div>Version</div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-success">
                            <?php echo XOOPS_VERSION; ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
            </div>

<!--div id="xo-banner">
    <img src="img/logo.png" alt="XOOPS" />
</div-->
            <div id="wizard" class="row">

                <?php echo $content; ?>

            </div>
        <!-- /.container-fluid -->
        </div>
    <!-- /#page-wrapper -->
    </div>
<!-- /#wrapper -->
</div>

<!-- jQuery -->
<script src="assets/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="assets/js/bootstrap.min.js"></script>

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
