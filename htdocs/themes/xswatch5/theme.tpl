<!doctype html>
<html lang="<{$xoops_langcode}>" dir="<{$xoops_text_direction|default:'ltr'}>">
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
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<{$xoops_url}>/favicon.ico" rel="shortcut icon">

	<{* Edit xswatch5.conf to pick the Bootswatch variant *}>
    <{config_load file="./xswatch5.conf"}>

    <{* Single CSS — Bootstrap 5 Color Modes handles light/dark via data-bs-theme attribute *}>
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}><{#xswatchCss#}>/xoops.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}><{#xswatchCss#}>/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}><{#xswatchCss#}>/cookieconsent.css">
    <{* Edit css/my_xoops.css to customize — use [data-bs-theme="dark"] selectors for dark overrides *}>
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/my_xoops.css">

    <{* Theme preference: localStorage > OS preference > light *}>
    <script>
    (function() {
        const stored = localStorage.getItem('xswatch-theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = stored || (prefersDark ? 'dark' : 'light');
        document.documentElement.setAttribute('data-bs-theme', theme);
    })();
    </script>

    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{xoImgUrl 'js/bootstrap.bundle.min.js'}>"></script>
    <{include file="$theme_name/tpl/cookieConsent.tpl"}>
    <{if !empty($xoops_isadmin)}>
    <script src="<{xoImgUrl}>js/js.cookie.min.js"></script>
    <{/if}>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl 'backend.php'}>">

    <title><{if isset($xoops_dirname) && $xoops_dirname == "system"}><{$xoops_sitename}><{if !empty($xoops_pagetitle)}> - <{$xoops_pagetitle}><{/if}><{else}><{if !empty($xoops_pagetitle)}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>

<{$xoops_module_header}>
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">
</head>

<body id="<{$xoops_dirname}>">

<{include file="$theme_name/tpl/nav-menu.tpl"}>

<{* un-comment to enable slider*}>
<{*<{if isset($xoops_page) && $xoops_page == "index"}>*}>
<{*    <{include file="$theme_name/tpl/slider.tpl"}>*}>
<{*<{/if}>*}>


<div class="container maincontainer">

<{if isset($xoops_page) && $xoops_page == "index"}>
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
            <{/if}>
        </div>
    </div>
    <!-- .bottom-blocks -->
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
    </div>
    <!-- .footer-blocks -->
<{/if}>

<footer class="footer">
    <h3>
        <{$xoops_footer}>
        <a href="https://xoops.org" title="Design by: XOOPS UI/UX Team" target="_blank" class="credits d-none d-sm-block">
            <img src="<{xoImgUrl}>images/favicon.png" alt="Design by: XOOPS UI/UX Team">
        </a>
    </h3>
    <a href="https://xoops.org" title="Design by: XOOPS UI/UX Team" target="_blank" class="credits text-center d-block d-sm-none">
        <img src="<{xoImgUrl}>images/favicon.png" alt="Design by: XOOPS UI/UX Team">
    </a>
</footer>
<{if !empty($xoops_isadmin)}><{include file="$theme_name/tpl/nav-admin.tpl"}><{/if}>
<!-- Inbox alert -->
<{if !empty($xoops_isuser)}><{include file="$theme_name/tpl/inboxAlert.tpl"}><{/if}>
</body>
</html>
