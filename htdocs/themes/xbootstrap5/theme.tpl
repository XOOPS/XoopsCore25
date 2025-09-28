<!-- theme.tpl (XOOPS + Bootstrap 5 layout example) -->

<!DOCTYPE html>
<html lang="<{$xoops_langcode}>" data-theme="light">
<head>
    <{assign var=theme_name value=$xoTheme->folderName}>
    <meta charset="<{$xoops_charset}>">
    <meta name="keywords" content="<{$xoops_meta_keywords}>">
    <meta name="description" content="<{$xoops_meta_description}>">
    <meta name="robots" content="<{$xoops_meta_robots}>">
    <meta name="rating" content="<{$xoops_meta_rating}>">
    <meta name="author" content="<{$xoops_meta_author}>">
    <meta name="generator" content="XOOPS">
    <title><{$xoops_sitename}> - <{$xoops_pagetitle}></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Owl Carousel Assets -->
    <link href="<{xoImgUrl}>js/owl/assets/owl.carousel.css" rel="stylesheet">
    <link href="<{xoImgUrl}>js/owl/assets/owl.theme.default.css" rel="stylesheet">

    <link href="<{$xoops_url}>/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/xoops.css">
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/reset.css">

<{*    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/style.css" >*}>
<{*    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/modules/_contact.css">*}>
<{*    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/modules/_downloads.css">*}>
<{*    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/modules/_gallery.css">*}>

    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">

    <link rel="stylesheet" type="text/css" media="screen" href="<{xoImgUrl}>css/scrollup.css">
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoImgUrl}>css/headhesive.css">
    <!-- Multi-level Menu -->
    <link rel="stylesheet" type="text/css" href="<{xoImgUrl}>css/multilevelmenu.css">

    <{if isset($xoops_dirname) && $xoops_dirname == 'newbb'}>
        <link rel="stylesheet" type="text/css" media="screen" href="<{xoImgUrl}>css/forums.css">
    <{/if}>

    <link rel="stylesheet" type="text/css" href="<{xoAppUrl 'media/font-awesome6/css/fontawesome.min.css'}>">
    <link rel="stylesheet" type="text/css" href="<{xoAppUrl 'media/font-awesome6/css/solid.min.css'}>">
    <link rel="stylesheet" type="text/css" href="<{xoAppUrl 'media/font-awesome6/css/brands.min.css'}>">
    <link rel="stylesheet" type="text/css" href="<{xoAppUrl 'media/font-awesome6/css/v4-shims.min.css'}>">


    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>

    <script src="<{xoImgUrl}>js/bootstrap.min.js"></script>
    <script src="<{xoImgUrl}>js/masonry.pkgd.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>

    <script src="<{xoImgUrl}>js/headhesive.min.js"></script>
    <{*<script src="<{xoImgUrl}>js/headhesive.js"></script>*}>
    <script src="<{xoImgUrl}>js/jquery.scrollUp.min.js"></script>
    <script src="<{xoImgUrl}>js/imagesloaded.pkgd.min.js"></script>


    <script src="<{xoImgUrl}>js/theme-toggle.js"></script>

<{*    <script src="<{$xoImgUrl}>/js/theme-toggle.js" defer></script>*}>
    <script src="<{xoImgUrl}>js/js.js"></script>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl 'backend.php'}>">

    <title><{if isset($xoops_dirname) && $xoops_dirname == "system"}><{$xoops_sitename}><{if !empty($xoops_pagetitle)}> - <{$xoops_pagetitle}><{/if}><{else}><{if !empty($xoops_pagetitle)}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>

    <{include file="$theme_name/tpl/shareaholic-script.tpl"}>

    <{$xoops_module_header}>


</head>
<body>

<body id="<{$xoops_dirname}>">

<{include file="$theme_name/tpl/nav-menu.tpl"}>
<{if isset($xoops_page) && $xoops_page == "index"}>
    <{include file="$theme_name/tpl/slider.tpl"}>
<{/if}>

<main class="container maincontainer pt-4">
<{*<main class="container maincontainer">*}>

    <button id="theme-toggle" class="btn btn-sm btn-outline-light position-fixed top-0 end-0 m-2 z-3">🌙</button>

    <div class="row">
        <aside class="col-md-3 xoops-side-blocks">
            <{foreach item=block from=$xoops_lblocks}>
                <div class="mb-4">
                    <h4 class="block-title"><{$block.title}></h4>
                    <div class="block-content"><{$block.content}></div>
                </div>
            <{/foreach}>
        </aside>

        <section class="col-md-6">
            <{$xoops_contents}>
        </section>

        <aside class="col-md-3 xoops-side-blocks">
            <{foreach item=block from=$xoops_rblocks}>
                <div class="mb-4">
                    <h4 class="block-title"><{$block.title}></h4>
                    <div class="block-content"><{$block.content}></div>
                </div>
            <{/foreach}>
        </aside>
    </div>
</main>

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

<footer class="footer mt-4">
    <h3>
        <{$xoops_slogan}>
        <a class="credits" href="https://xoops.org">XOOPS CMS</a>
    </h3>
</footer>

</body>
</html>
