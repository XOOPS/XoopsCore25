<style>
    .publisher-search-block select {
        width: 100% !important;
    }
</style>
<form class="publisher-search-block" name="search" action="<{$block.publisher_url}>/search.php" method="post">
    <div class="input-group input-group-sm col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:8px;">
        <input type="text" class="form-control" placeholder="<{$smarty.const._SR_KEYWORDS}>" name="term" value="<{$block.search_term}>">
    </div>
    <div style="margin-top:8px;">
        <{$block.category_select}>
    </div>
    <div class="input-group input-group-sm col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top:8px;">
        <input type="text" class="form-control" placeholder="<{$smarty.const._CO_PUBLISHER_UID}>" name="uname" value="<{$block.search_user}>">
    </div>
    <{if $block.search_rule}>
        <div style="margin-top:8px;">
            <strong><{$smarty.const._SR_SEARCHRULE}></strong>&nbsp;
            <{$block.search_rule}>
        </div>
    <{/if}>
    <button type="submit" class="btn btn-primary btn-sm" name="submit" value="<{$smarty.const._SEARCH}>" style="margin-top:8px;">
        <{$smarty.const._SEARCH}>
    </button>
</form>
