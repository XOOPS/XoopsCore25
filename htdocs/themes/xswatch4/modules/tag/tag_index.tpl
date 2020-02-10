<!-- phppp (D.J.): http://xoopsforge.com; https://xoops.org.cn -->

<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/" title="<{$smarty.const._MD_TAG_TAGS}>"><{$smarty.const._MD_TAG_TAGS}></a></li>
    <li class="breadcrumb-item active"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php<{$smarty.const.URL_DELIMITER}><{$tag_term}>" title="<{$tag_page_title|strip_tags}>" rel="tag"><{$tag_page_title|regex_replace:'/^.+g>/U':''|replace:'</strong>':''}></a></li>
</ol>

<div class="tag-jumpto">
    <form id="form-tag-jumpto" name="form-tag-jumpto" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php" method="get">
        <div class="form-group row">
            <label class="col-2 text-right" for="term"><{$lang_jumpto}>: </label>
            <div class="col-5">
                <input class="form-control" type="text" id="term" name="term" value="">
            </div>
            <div class="col-1">
                <button class="btn btn-primary" type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"><{$smarty.const._SUBMIT}></button>
            </div>
        </div>
    </form>
</div>

<div class="tag-cloud" style="margin-top: 10px; padding: 10px; border: solid 2px #ddd; line-height: 150%;">
    <{foreach item=tag from=$tags}>
        <span class="tag-item tag-level-<{$tag.level}>" style="font-size: <{$tag.font}>%; margin-right: 5px;">
    <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag.term}>"
       title="<{$tag.title}>" rel="tag"><{$tag.title}></a>
</span>
    <{/foreach}>
</div>


<div id="pagenav" style="padding-top: 10px;">
    <{$pagenav}>
</div>
