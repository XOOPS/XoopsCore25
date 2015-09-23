<div id="xo-searchbar">
    <form name="search" method="get" action="<{xoAppUrl /search.php}>">
        <input type="text" id="query" name="query" class="keyword" value="" maxlength="255" tabindex="1" title="<{$smarty.const.THEME_KEYWORDS}>"/>
        <input type="hidden" name="action" id="action" value="results"/>
        <input type="submit" value="<{$smarty.const.THEME_SEARCH}>" class="button" tabindex="2" title="<{$smarty.const.THEME_DESC_SEARCH}>"/>
    </form>
</div>


