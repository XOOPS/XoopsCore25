<div class="text-center">
    <form role="search" action="<{xoAppUrl 'search.php'}>" method="get">
        <div class="input-group">
            <input class="form-control form-control-sm" type="text" name="query" placeholder="<{$smarty.const.THEME_SEARCH_TEXT}>">
            <input type="hidden" name="action" value="results">
            <button class="btn btn-primary btn-sm" type="submit">
                <span class="fa fa-search"></span>
            </button>
        </div>
    </form>

    <p class="text-end">
        <a href="<{xoAppUrl 'search.php'}>" title="<{$block.lang_advsearch}>" class="text-decoration-none">
            <{$block.lang_advsearch}>
        </a>
    </p>
</div>
