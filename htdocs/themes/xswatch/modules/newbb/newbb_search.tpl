<div class="newbb">
    <ol class="breadcrumb">
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$forumindex}></a></li>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const._SR_SEARCH}></a></li>
    </ol>
</div>

<{if $search_info}>
    <{includeq file="db:newbb_searchresults.tpl" results=$results}>
<{/if}>
<form name="Search" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get">
    <div class="form-group">
        <label for="andor"><{$smarty.const._SR_KEYWORDS}></label>
        <input class="form-control" type="text" name="term" id="term" value="<{$search_term}>" />
    </div>
    <div class="form-group">
        <label for="andor"><{$smarty.const._SR_TYPE}></label>
        <{$andor_selection_box}>
    </div>
    <div class="form-group">
        <label for="forum"><{$smarty.const._MD_FORUMC}></label>
        <{$forum_selection_box}>
    </div>
    <div class="form-group">
        <label><{$smarty.const._SR_SEARCHIN}></label>
        <{$searchin_radio}>
    </div>
    <div class="form-group">
        <label for="uname"><{$smarty.const._MD_AUTHOR}></label>
        <input class="form-control" type="text" name="uname" id="uname" value="<{$author_select}>"/>
    </div>
    <div class="form-group">
        <label for="sortby"><{$smarty.const._MD_SORTBY}></label>
        <{$sortby_selection_box}>
    </div>
    <div class="form-group">
        <label for="since"><{$smarty.const._MD_SINCE}></label>
        <{$since_selection_box}>
    </div>
    <div class="form-group">
        <label for="selectlength"><{$smarty.const._MD_SELECT_LENGTH}></label>
        <input class="form-control" type="text" name="selectlength" id="selectlength" value="<{$selectlength_select}>"/>
    </div>
    <div class="form-group">
        <label for="selectlength"><{$smarty.const._MD_SHOWSEARCH}></label>
        <{$show_search_radio}>
    </div>

    <div class="form-group">
        <label><{$smarty.const._SR_SEARCHRULE}></label>
        <p class="help-block"><{$search_rule}></p>
    </div>

    <button type="submit" class="btn btn-default"><{$smarty.const._MD_SEARCH}></button>
</form>
