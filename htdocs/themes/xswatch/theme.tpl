<!doctype html>
<html lang="<{$xoops_langcode}>">
<head>
<{assign var=theme_name value=$xoTheme->folderName}>
    <meta charset="<{$xoops_charset}>">
    <meta name="keywords" content="<{$xoops_meta_keywords}>">
    <meta name="description" content="<{$xoops_meta_description}>">
    <meta name="robots" content="<{$xoops_meta_robots}>">
    <meta name="rating" content="<{$xoops_meta_rating}>">
    <meta name="author" content="<{$xoops_meta_author}>">
    <meta name="generator" content="XOOPS">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Owl Carousel Assets -->
    <link href="<{xoImgUrl}>js/owl/assets/owl.carousel.css" rel="stylesheet">
    <link href="<{xoImgUrl}>js/owl/assets/owl.theme.default.css" rel="stylesheet">

    <link href="<{$xoops_url}>/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/reset.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/xoops.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/cookieconsent.css">
    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{xoImgUrl}>js/bootstrap.min.js"></script>
    <script src="<{xoImgUrl}>js/masonry.pkgd.min.js"></script>
    <script src="<{xoImgUrl}>js/imagesloaded.pkgd.min.js"></script>
    <script src="<{xoImgUrl}>js/js.js"></script>
    <!-- Begin Cookie Consent plugin by Silktide - https://silktide.com/tools/cookie-consent/docs/installation/ -->
    <script type="text/javascript">
        window.cookieconsent_options = {
            message: '<{$smarty.const.THEME_COOKIE_MESSAGE}>',
            dismiss: '<{$smarty.const.THEME_COOKIE_DISMISS}>',
            learnMore: '<{$smarty.const.THEME_COOKIE_LEARNMORE}>',
            link: null,
            container: null,
            theme: false,
        };
    </script>
    <script src="<{xoImgUrl}>js/cookieconsent.min.js"></script>
    <!-- End Cookie Consent plugin -->
    <{if $xoops_isadmin|default:false}>
    <script src="<{xoImgUrl}>js/js.cookie.min.js"></script>
    <{/if}>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl backend.php}>">

    <title><{if $xoops_dirname == "system"}><{$xoops_sitename}><{if $xoops_pagetitle !=''}> - <{$xoops_pagetitle}><{/if}><{else}><{if $xoops_pagetitle !=''}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>

<{$xoops_module_header}>
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">
</head>

<body id="<{$xoops_dirname}>">

<{includeq file="$theme_name/tpl/nav-menu.tpl"}>

<div class="container maincontainer">

<{if $xoops_page == "index"}>
    <div class="jumbotron">
        <div class="row clearfix">
            <div class="<{if $xoops_banner != ""}>col-md-6<{else}>col-md-12<{/if}>">
            <h2><{$smarty.const.THEME_ABOUTUS}></h2>
            <{if $xoops_banner != ""}></div><div class="col-md-6"><div class="xoops-banner"><{$xoops_banner}></div></div><{/if}>
        </div>
        <div class="row">
            <p class="lead"><{$xoops_meta_description}></p>

            <p><a href="<{$xoops_url}>/" class="btn btn-md btn-success"><{$smarty.const.THEME_LEARNMORE}></a></p>
        </div>

    </div>
<{/if}>

<div class="row">
    <{includeq file="$theme_name/tpl/leftBlock.tpl"}>

    <{includeq file="$theme_name/tpl/content-zone.tpl"}>

    <{includeq file="$theme_name/tpl/rightBlock.tpl"}>
</div>

</div><!-- .maincontainer -->

<{if $xoBlocks.page_bottomcenter || $xoBlocks.page_bottomright || $xoBlocks.page_bottomleft}>
    <div class="bottom-blocks">
        <div class="container">
            <{if $xoBlocks.page_bottomcenter}>
            <div class="row">
                <{includeq file="$theme_name/tpl/centerBottom.tpl"}>
            </div>
            <{/if}>
            <{if $xoBlocks.page_bottomright || $xoBlocks.page_bottomleft}>
            <div class="row">
                <{includeq file="$theme_name/tpl/leftBottom.tpl"}>

                <{includeq file="$theme_name/tpl/rightBottom.tpl"}>
            </div>
        </div>
        <{/if}>
    </div><!-- .bottom-blocks -->
<{/if}>

<{if $xoBlocks.footer_center || $xoBlocks.footer_right || $xoBlocks.footer_left}>
    <div class="footer-blocks">
        <div class="container">
            <div class="row">
                <{includeq file="$theme_name/tpl/leftFooter.tpl"}>

                <{includeq file="$theme_name/tpl/centerFooter.tpl"}>

                <{includeq file="$theme_name/tpl/rightFooter.tpl"}>
            </div>
        </div>
    </div><!-- .footer-blocks -->
<{/if}>

<footer class="footer">
    <h3>
        <{$xoops_footer}>
        <a href="http://xoops.org" title="Design by: XOOPS UI/UX Team" target="_blank" class="credits visible-md visible-sm visible-lg">
            <img src="<{xoImgUrl}>images/favicon.png" alt="Design by: XOOPS UI/UX Team">
        </a>
    </h3>
</footer>
<div class="aligncenter comments-nav visible-xs">
    <a href="http://xoops.org" title="Design by: XOOPS UI/UX Team" target="_blank">
        <img src="<{xoImgUrl}>images/favicon.png" alt="Design by: XOOPS UI/UX Team">
    </a>
</div>
<{if $xoops_isadmin|default:false}><{includeq file="$theme_name/tpl/nav-admin.tpl"}><{/if}>
</body>
</html>
