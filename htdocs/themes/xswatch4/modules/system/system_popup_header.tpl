<!doctype html>
<html lang="<{$xoops_langcode}>">
<head>
    <meta charset="<{$xoops_charset}>">
    <meta name="robots" content="noindex, nofollow" />
    <title><{$xoops_sitename|escape:'html':'UTF-8'}></title>
    <{section name=item loop=$headItems}>
    <{$headItems[item]}>
    <{/section}>
    <link rel="stylesheet" type="text/css" href="<{$themeUrl}>css/xoops.css">
    <link rel="stylesheet" type="text/css" href="<{$themeUrl}>css/bootstrap.min.css">
    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{$themeUrl}>js/bootstrap.bundle.min.js"></script>

    <{if $closeHead|default:false}>
</head>
<body id="xswatch4-popup-body">
<{/if}>
