<!doctype html>
<html lang="<{$xoops_langcode}>">
<head>
    <meta charset="<{$xoops_charset}>">
    <meta name="robots" content="noindex, nofollow" />
    <title><{$xoops_sitename|escape:'html':'UTF-8'}></title>
    <{section name=item loop=$headItems}>
    <{$headItems[item]}>
    <{/section}>

    <link href="<{$xoops_url}>/favicon.ico" rel="shortcut icon">

    <{include file="$themePath/tpl/xswatchCss.tpl" assign="xswatchCss"}>
    <{include file="$themePath/tpl/xswatchDarkCss.tpl" assign="xswatchDarkCss"}>
    <{if isset($xswatchDarkCss) && $xswatchDarkCss == ''}>
        <link rel="stylesheet" type="text/css" href="<{$themeUrl}><{$xswatchCss}>/xoops.css">
        <link rel="stylesheet" type="text/css" href="<{$themeUrl}><{$xswatchCss}>/bootstrap.min.css">
    <{else}>
        <link rel="stylesheet" media="(prefers-color-scheme: light)" href="<{$themeUrl}><{$xswatchCss}>/xoops.css">
        <link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<{$themeUrl}><{$xswatchDarkCss}>/xoops.css">
        <link rel="stylesheet" media="(prefers-color-scheme: light)" href="<{$themeUrl}><{$xswatchCss}>/bootstrap.min.css">
        <link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<{$themeUrl}><{$xswatchDarkCss}>/bootstrap.min.css">
    <{/if}>
    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{$themeUrl}>js/bootstrap.bundle.min.js"></script>

    <{if !empty($closeHead)}>
</head>
<body id="xswatch-popup-body">
<{/if}>
