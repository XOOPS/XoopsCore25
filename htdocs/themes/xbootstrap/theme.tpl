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
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Owl Carousel Assets -->
    <link href="<{xoImgUrl}>js/owl/assets/owl.carousel.css" rel="stylesheet">
    <link href="<{xoImgUrl}>js/owl/assets/owl.theme.default.css" rel="stylesheet">

    <link href="<{$xoops_url}>/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/xoops.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/reset.css">
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">

    <link rel="stylesheet" type="text/css" media="screen" href="<{xoImgUrl}>css/scrollup.css"/>
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoImgUrl}>css/headhesive.css"/>
    <!-- Multi-level Menu -->
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/multilevelmenu.css">

    <{if $xoops_dirname=='newbb'}>
        <link rel="stylesheet" type="text/css" media="screen" href="<{xoImgUrl}>css/forums.css"/>
    <{/if}>

    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>

    <script src="<{xoImgUrl}>js/bootstrap.min.js"></script>
    <script src="<{xoImgUrl}>js/masonry.pkgd.min.js"></script>

    <script src="<{xoImgUrl}>js/headhesive.min.js"></script>
    <{*<script src="<{xoImgUrl}>js/headhesive.js"></script>*}>
    <script src="<{xoImgUrl}>js/jquery.scrollUp.min.js"></script>
    <script src="<{xoImgUrl}>js/imagesloaded.pkgd.min.js"></script>

<!--[if lt IE 9]>
    <script src="http://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="http://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <script src="<{xoImgUrl}>js/selectivizr-min.js"></script>
<![endif]-->
    <script src="<{xoImgUrl}>js/js.js"></script>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl backend.php}>">

    <title><{if $xoops_dirname == "system"}><{$xoops_sitename}><{if $xoops_pagetitle !=''}> - <{$xoops_pagetitle}><{/if}><{else}><{if $xoops_pagetitle !=''}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>

<{includeq file="$theme_name/tpl/shareaholic-script.tpl"}>

<{$xoops_module_header}>

</head>

<body id="<{$xoops_dirname}>">

<{includeq file="$theme_name/tpl/nav-menu.tpl"}>

<{includeq file="$theme_name/tpl/slider.tpl"}>
<div class="container maincontainer">
<a id="stickyMenuHere"></a>
<{if $xoops_page == "index"}>
    <div class="aligncenter home-message row">
    <div class="<{if $xoops_banner != ""}>col-md-6<{else}>col-md-12<{/if}>">
        <h2><{$smarty.const.THEME_ABOUTUS}></h2>

        <p class="lead"><{$xoops_meta_description}></p>

        <p><a href="javascript:;" class="btn btn-md btn-success"><{$smarty.const.THEME_LEARNINGMORE}></a></p>
    </div>

    <{if $xoops_banner != ""}><div class="col-md-6"><div class="xoops-banner pull-right"><{$xoops_banner}></div></div><{/if}>

    </div><!-- .home-message -->
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
            <div class="row">
                <{includeq file="$theme_name/tpl/leftBottom.tpl"}>

                <{includeq file="$theme_name/tpl/centerBottom.tpl"}>

                <{includeq file="$theme_name/tpl/rightBottom.tpl"}>
            </div>
        </div>
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
<!-- end of new footer blocks  -->

<script>
    // Set options
    var options = {
        offset: '#stickyMenuHere',
        classes: {
            clone: 'adhesiveHeader--clone',
            stick: 'adhesiveHeader--stick',
            unstick: 'adhesiveHeader--unstick'
        }
    };
    // Initialise with options
    var adhesiveHeader = new Headhesive('.adhesiveHeader', options);
    // Headhesive destroy
    // adhesiveHeader.destroy();
</script>

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
</body>
</html>
