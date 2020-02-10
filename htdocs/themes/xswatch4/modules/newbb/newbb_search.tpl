<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$forumindex}></a></li>
    <li class="breadcrumb-item active"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php"><{$smarty.const._SR_SEARCH}></a></li>
</ol>
<{if $search_info}>
    <{include file="db:newbb_searchresults.tpl" results=$results}>
<{/if}>

<form name="Search" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/search.php" method="get">
    <table class="table" border="0" cellpadding="1" cellspacing="0" align="center" width="95%">
        <tr>
            <!-- irmtfan hardcode removed align="right" -->
            <td class="head" width="10%" id="align_right"><strong><{$smarty.const._SR_KEYWORDS}></strong>&nbsp;</td>
            <!-- irmtfan add  value="$search_term" -->
            <td class="even"><input class="form-control" type="text" name="term" value="<{$search_term}>"></td>
        </tr>
        <tr>
            <!-- irmtfan hardcode removed align="right" add $andor_selection_box -->
            <td class="head" id="align_right"><strong><{$smarty.const._SR_TYPE}></strong>&nbsp;</td>
            <td class="even"><{$andor_selection_box}></td>
        </tr>
        <tr>
            <!-- irmtfan hardcode removed align="right" -->
            <td class="head" id="align_right"><strong><{$smarty.const._MD_NEWBB_FORUMC}></strong>&nbsp;</td>
            <td class="even"><{$forum_selection_box}></td>
        </tr>
        <tr>
            <!-- irmtfan hardcode removed align="right" add $searchin_radio -->
            <td class="head" id="align_right"><strong><{$smarty.const._SR_SEARCHIN}></strong>&nbsp;</td>
            <td class="even">
                <{*$searchin_radio*}>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="searchin" id="searchin1" value="title">
                    <label class="form-check-label" for="searchin1"><{$smarty.const._MD_NEWBB_SUBJECT}></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="searchin" id="searchin2" value="text">
                    <label class="form-check-label" for="searchin2"><{$smarty.const._MD_NEWBB_BODY}></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="searchin" id="searchin3" value="both" checked>
                    <label class="form-check-label" for="searchi3"><{$smarty.const._MD_NEWBB_SUBJECT}> &amp; <{$smarty.const._MD_NEWBB_BODY}></label>
                </div>
            </td>
        </tr>
        <tr>
            <!-- irmtfan hardcode removed align="right" add value="$author_select"-->
            <td class="head" id="align_right"><strong><{$smarty.const._MD_NEWBB_AUTHOR}></strong>&nbsp;</td>
            <td class="even"><input class="form-control" type="text" name="uname" value="<{$author_select}>"></td>
        </tr>
        <tr>
            <!-- irmtfan hardcode removed align="right" add $sortby_selection_box -->
            <td class="head" id="align_right"><strong><{$smarty.const._MD_NEWBB_SORTBY}></strong>&nbsp;</td>
            <td class="even"><{$sortby_selection_box}></td>
        </tr>
        <tr>
            <!-- irmtfan hardcode removed align="right" -->
            <td class="head" id="align_right"><strong><{$smarty.const._MD_NEWBB_SINCE}></strong>&nbsp;</td>
            <td class="even"><{$since_selection_box}></td>
        </tr>
        <!-- START irmtfan add select text options -->
        <tr>
            <td class="head" id="align_right" title="<{$smarty.const._MD_NEWBB_SELECT_STARTLAG_DESC}>">
                <strong><{$smarty.const._MD_NEWBB_SELECT_STARTLAG}></strong>&nbsp;
            </td>
            <td class="even" title="<{$smarty.const._MD_NEWBB_SELECT_STARTLAG_DESC}>">
                <input class="form-control" type="text" name="selectstartlag" value="<{$selectstartlag_select}>">
            </td>
        </tr>
        <tr>
            <td class="head" id="align_right"><strong><{$smarty.const._MD_NEWBB_SELECT_LENGTH}></strong>&nbsp; </td>
            <td class="even"><input class="form-control" type="text" name="selectlength" value="<{$selectlength_select}>"></td>
        </tr>
        <!-- END irmtfan add select text options -->
        <!-- START irmtfan add show search -->
        <tr>
            <td class="head" id="align_right"><strong><{$smarty.const._MD_NEWBB_SHOWSEARCH}></strong>&nbsp;</td>
            <td class="even">
                <{*$show_search_radio*}>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="show_search" id="show_search1" value="post">
                    <label class="form-check-label" for="show_search1"><{$smarty.const._MD_NEWBB_POSTS}></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="show_search" id="show_search2" value="post_text" checked>
                    <label class="form-check-label" for="show_search2"><{$smarty.const._MD_NEWBB_SEARCHPOSTTEXT}></label>
                </div>

            </td>
        </tr>
        <!-- START irmtfan add show search -->
        <{if $search_rule}>
        <tr>
            <!-- irmtfan hardcode removed align="right" -->
            <td class="head" id="align_right"><strong><{$smarty.const._SR_SEARCHRULE}></strong>&nbsp;</td>
            <td class="even"><{$search_rule}></td>
        </tr>
        <{/if}>
        <tr>
            <!-- irmtfan hardcode removed align="right" -->
            <td class="head" id="align_right">&nbsp;</td>
            <!-- irmtfan remove name="submit" -->
            <td class="even"><input class="btn btn-secondary" type="submit" value="<{$smarty.const._MD_NEWBB_SEARCH}>"></td>
        </tr>
    </table>
</form>
