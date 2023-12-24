<div class="container py-2">
    <div class="p-1 mb-4 bg-light rounded-3 border rounded-3">
        <div class="container-fluid py-2">
            <h1 class="display-4"><{$smarty.const.THEME_ABOUTUS}></h1>
            <p class="fs-4"><{$xoops_meta_description}></p>
            <hr />
            <button class="btn btn-primary btn-lg mb-3" type="button"><{$smarty.const.THEME_LEARNMORE}></button>
            <{if $xoops_banner != "&nbsp;"}>
                <div class="row d-none d-md-block mb-0">
                    <div class="col"><div class="text-center xoops-banner"><{$xoops_banner}></div></div>
                </div>
            <{/if}>
        </div>
    </div>
</div>
