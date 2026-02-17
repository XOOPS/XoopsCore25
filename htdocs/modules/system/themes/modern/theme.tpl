<!DOCTYPE html>
<html lang="<{$xoops_langcode}>">
<head>
    <{include file="$theme_tpl/xo_metas.tpl"}>
    <{include file="$theme_tpl/xo_scripts.tpl"}>
</head>
<body id="<{$xoops_dirname}>" class="<{$xoops_langcode}> <{if $dark_mode == '1'}>dark-mode<{/if}>">
    <{include file="$theme_tpl/xo_head.tpl"}>
    <div class="modern-layout">
        <{include file="$theme_tpl/xo_sidebar.tpl"}>
        <div class="modern-main">
            <{include file="$theme_tpl/xo_toolbar.tpl"}>
            <{include file="$theme_tpl/xo_dashboard.tpl"}>
            <{include file="$theme_tpl/xo_page.tpl"}>
        </div>
    </div>
    <{include file="$theme_tpl/xo_footer.tpl"}>
    <{include file="$theme_tpl/xo_customizer.tpl"}>
</body>
</html>
