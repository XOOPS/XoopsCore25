<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <!-- title and metas -->
    <title><{if $xoops_pagetitle !=''}><{$xoops_pagetitle}> : <{/if}><{$xoops_sitename}></title>
    <meta http-equiv="content-type" content="text/html; charset=<{$xoops_charset}>"/>
    <meta name="robots" content="<{$xoops_meta_robots}>"/>
    <meta name="keywords" content="<{$xoops_meta_keywords}>"/>
    <meta name="description" content="<{$xoops_meta_description}>"/>
    <meta name="rating" content="<{$xoops_meta_rating}>"/>
    <meta name="author" content="<{$xoops_meta_author}>"/>
    <meta name="copyright" content="<{$xoops_meta_copyright}>"/>
    <meta name="generator" content="XOOPS"/>
    <{if $url}>
        <meta http-equiv="Refresh" content="<{$time}>; url=<{$url}>"/>
    <{/if}>

    <!-- Force MSIE without  javascript actived to take the default theme. not conforms to the standards but functional -->
    <{if $isMsie}>
        <noscript>
            <meta http-equiv="refresh" content="0; url=<{xoAppUrl . xoops_theme_select=default}>"/>
        </noscript>
    <{/if}>

    <!-- path favicon -->
    <link rel="shortcut icon" type="image/ico" href="<{xoImgUrl icons/favicon.ico}>"/>
    <link rel="icon" type="image/png" href="<{xoImgUrl icons/favicon.png}>"/>

    <!-- include xoops.js and others via header.php -->
    <{$xoops_module_header}>

    <!-- Xoops style sheet -->
    <link rel="stylesheet" type="text/css" media="screen" href="<{xoAppUrl xoops.css}>"/>

    <!-- Theme style sheets -->
    <link rel="stylesheet" type="text/css" media="screen" title="Color" href="<{xoImgUrl style.css}>"/>

</head>
<body id="xo-refresh">
<div id="xo-wrapper" class="container center">
    <div id="xo-redirect">
        <div class="message">
            <{$message}>
            <br>
            <img src="<{xoImgUrl icons/ajax_indicator_01.gif}>" alt="<{$message}>"/>
        </div>
        <div class="notreload">
            <{$lang_ifnotreload}>
        </div>
        <{if $xoops_logdump != ''}>
            <div><{$xoops_logdump}></div>
        <{/if}>
    </div>
</div>

</body>
</html>
