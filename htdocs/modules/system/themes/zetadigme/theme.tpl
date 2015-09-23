<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <{includeq file="$theme_tpl/xo_metas.tpl"}>
    <{includeq file="$theme_tpl/xo_scripts.tpl"}>
    <{includeq file="$theme_tpl/xo_parameters.tpl"}>
</head>
<body id="<{$xoops_dirname}>" class="<{$xoops_langcode}>">
<div id="xo-wrapper" class="<{$xoops_dirname}>">
    <div id="xo-bgstatic" class="<{$xoops_dirname}>"></div>
    <div id="xo-canvas">
        <!-- zone HEADER -->
        <div id="xo-header" class="<{$xoops_dirname}>">
            <{if $theme_view_header}><{includeq file="$theme_tpl/xo_banner.tpl"}><{/if}>
            <{includeq file="$theme_tpl/xo_navbar.tpl"}>
        </div>

        <!-- zone CONTENT -->
        <div id="xo-canvas-content">
            <{if $modules}>
                <table class="bnone" style="width:300px;">
                    <tr>
                        <td rowspan="2">&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <table id="xo-index">
                    <tr>
                        <td rowspan="2" id="xo-menusystem" class="CPindexOptions"><{includeq file="$theme_tpl/xo_menusystem.tpl"}></td>
                        <td id="xo-modules" class="CPindexOptions"><{includeq file="$theme_tpl/xo_modules.tpl"}></td>
                    </tr>
                    <tr>
                        <td id="xo-modules" class="CPindexOptions"><{if $theme_view_accordion}><{includeq file="$theme_tpl/xo_accordion.tpl"}><{/if}>
                        </td>
                    </tr>
                    <{if $theme_view_blocksys}><{includeq file="$theme_tpl/xo_block.tpl"}><{/if}>
                </table>
            <{/if}>

            <{if $xoops_contents}>
                <div id="xo-content">
                    <{if $xo_system_menu}><{$xo_system_menu}><{/if}>
                    <{$xoops_contents}>
                </div>
            <{/if}>
        </div>
    </div>
    <!-- zone FOOTER -->
    <{if $theme_view_footersys}><{includeq file="$theme_tpl/xo_footer.tpl"}><{/if}>
</div>
<{if $theme_view_baradmin}><{includeq file="$theme_tpl/xo_footerstatic.tpl"}><{/if}>
</body>
</html>
