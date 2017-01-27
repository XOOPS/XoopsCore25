<!doctype html>
<html lang="<{$xoops_langcode}>">
<head>
    <meta charset="<{$xoops_charset}>">
    <meta name="robots" content="noindex, nofollow" />
    <title><{$xoops_sitename|escape:'html':'UTF-8'}></title>
    <{section name=item loop=$headItems}>
    <{$headItems[item]}>
    <{/section}>
<{if $closeHead|default:false}>
</head>
<body>
<{/if}>
