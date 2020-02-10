<div class="jumbotron<{if $xoops_banner != "&nbsp;"}> xo-jumbotron<{/if}>">
    <div class="row">
        <div class="col"><h2 class="display-4"><{$smarty.const.THEME_ABOUTUS}></h2></div>
    </div>
    <p class="lead"><{$xoops_meta_description}></p>
    <hr>
    <a class="btn btn-primary" href="<{$xoops_url}>/"><{$smarty.const.THEME_LEARNMORE}></a>
    <{if $xoops_banner != "&nbsp;"}>
    <div class="row d-none d-md-block mb-0">
        <hr>
        <div class="col"><div class="text-center xoops-banner"><{$xoops_banner}></div></div>
    </div>
    <{/if}>
</div>
