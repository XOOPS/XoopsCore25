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
    <meta name="copyright" content="<{$xoops_meta_copyright}>">
    <meta name="generator" content="XOOPS">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge"><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="<{$xoops_url}>/favicon.ico" rel="shortcut icon">

	<{* Edit tpl/xswatchCss.tpl to pick the css directory you want to use *}>
    <{include file="$theme_name/tpl/xswatchCss.tpl" assign="xswatchCss"}>
    <{* Edit tpl/xswatchDarkCss.tpl to pick the css directory you want to use for dark mode *}>
    <{include file="$theme_name/tpl/xswatchDarkCss.tpl" assign="xswatchDarkCss"}>
    <{* if xswatchDarkCss doesn't set a dark mode theme, just use one for all *}>
    <{if $xswatchDarkCss == ''}>
        <link rel="stylesheet" type="text/css" href="<{xoImgUrl}><{$xswatchCss}>/xoops.css">
        <link rel="stylesheet" type="text/css" href="<{xoImgUrl}><{$xswatchCss}>/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="<{xoImgUrl}><{$xswatchCss}>/cookieconsent.css">
    <{else}>
        <link rel="stylesheet" media="(prefers-color-scheme: light)" href="<{xoImgUrl}><{$xswatchCss}>/xoops.css">
        <link rel="stylesheet" media="(prefers-color-scheme: light)" href="<{xoImgUrl}><{$xswatchCss}>/bootstrap.min.css">
        <link rel="stylesheet" media="(prefers-color-scheme: light)" href="<{xoImgUrl}><{$xswatchCss}>/cookieconsent.css">
        <link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<{xoImgUrl}><{$xswatchDarkCss}>/xoops.css">
        <link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<{xoImgUrl}><{$xswatchDarkCss}>/bootstrap.min.css">
        <link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<{xoImgUrl}><{$xswatchDarkCss}>/cookieconsent.css">
    <{/if}>

    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{xoImgUrl}>js/bootstrap.bundle.min.js"></script>
    <{include file="$theme_name/tpl/cookieConsent.tpl"}>
    <{if $xoops_isadmin|default:false}>
    <script src="<{xoImgUrl}>js/js.cookie.min.js"></script>
    <{/if}>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl backend.php}>">

    <title><{if $xoops_dirname == "system"}><{$xoops_sitename}><{if $xoops_pagetitle !=''}> - <{$xoops_pagetitle}><{/if}><{else}><{if $xoops_pagetitle !=''}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>

<{$xoops_module_header}>
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">
</head>

<body id="<{$xoops_dirname}>">

<{include file="$theme_name/tpl/nav-menu.tpl"}>

<{* un-comment to enable slider
<{if $xoops_page == "index"}>
    <{include file="$theme_name/tpl/slider.tpl"}>
<{/if}>
*}>

<div class="container maincontainer">

<{if $xoops_page == "index"}>
    <{include file="$theme_name/tpl/jumbotron.tpl"}>
<{/if}>

<div class="row">
    <{include file="$theme_name/tpl/leftBlock.tpl"}>

    <{include file="$theme_name/tpl/content-zone.tpl"}>

    <{include file="$theme_name/tpl/rightBlock.tpl"}>
</div>

</div><!-- .maincontainer -->

<{if $xoBlocks.page_bottomcenter || $xoBlocks.page_bottomright || $xoBlocks.page_bottomleft}>
    <div class="bottom-blocks">
        <div class="container">
            <{if $xoBlocks.page_bottomcenter}>
            <div class="row">
                <{include file="$theme_name/tpl/centerBottom.tpl"}>
            </div>
            <{/if}>
            <{if $xoBlocks.page_bottomright || $xoBlocks.page_bottomleft}>
            <div class="row">
                <{include file="$theme_name/tpl/leftBottom.tpl"}>

                <{include file="$theme_name/tpl/rightBottom.tpl"}>
            </div>
        </div>
        <{/if}>
    </div><!-- .bottom-blocks -->
<{/if}>

<{if $xoBlocks.footer_center || $xoBlocks.footer_right || $xoBlocks.footer_left}>
    <div class="footer-blocks">
        <div class="container">
            <div class="row">
                <{include file="$theme_name/tpl/leftFooter.tpl"}>

                <{include file="$theme_name/tpl/centerFooter.tpl"}>

                <{include file="$theme_name/tpl/rightFooter.tpl"}>
            </div>
        </div>
    </div><!-- .footer-blocks -->
<{/if}>

<footer class="footer">
    <h3>
        <{$xoops_footer}>
        <a href="http://xoops.org" title="Design by: XOOPS UI/UX Team" target="_blank" class="credits d-none d-sm-block">
            <img src="<{xoImgUrl}>images/favicon.png" alt="Design by: XOOPS UI/UX Team">
        </a>
    </h3>
</footer>
<div class="aligncenter comments-nav d-block d-sm-none">
    <a href="http://xoops.org" title="Design by: XOOPS UI/UX Team" target="_blank">
        <img src="<{xoImgUrl}>images/favicon.png" alt="Design by: XOOPS UI/UX Team">
    </a>
</div>
<{if $xoops_isadmin|default:false}><{include file="$theme_name/tpl/nav-admin.tpl"}><{/if}>
<!-- Inbox alert -->
<{if $xoops_isuser|default:false}><{include file="$theme_name/tpl/inboxAlert.tpl"}><{/if}>
</body>
</html>
