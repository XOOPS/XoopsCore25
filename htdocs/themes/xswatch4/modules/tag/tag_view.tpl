<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/" title="<{$module_name}>"><{$module_name}></a></li>
    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/list.tag.php" title="<{$smarty.const._MD_TAG_TAGS}>"><{$smarty.const._MD_TAG_TAGS}></a></li>
    <li class="breadcrumb-item active"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php<{$smarty.const.URL_DELIMITER}><{$tag_term}>" title="<{$tag_title}>" rel="tag"><{$tag_title}></a></li>
</ol>

<div class="tag-jumpto">
    <form id="form-tag-jumpto" name="form-tag-jumpto" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/view.tag.php" method="get">
        <div class="form-group row">
            <label class="col-2 text-right" for="term"><{$smarty.const._MD_TAG_JUMPTO}>: </label>
            <div class="col-5">
                <input class="form-control" type="text" id="term" name="term" value="">
            </div>
            <div class="col-1">
                <button class="btn btn-primary" type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"><{$smarty.const._SUBMIT}></button>
            </div>
        </div>
    </form>
</div>

<div class="tag-item-list" style="padding-top: 10px;">
    <{foreach item=article from=$tag_articles}>
        <div class="tag-item-title" style="padding-top: 10px;">
            <a href="<{$xoops_url}>/modules/<{$article.dirname}>/" title="<{$article.module}>"><{$article.module}></a>:
            <a href="<{$xoops_url}>/modules/<{$article.dirname}>/<{$article.link}>"
               title="<{$article.title}>"><{$article.title}></a>
        </div>
        <div class="tag-item-meta" style="padding-left: 10px;">
            <a href="<{$xoops_url}>/userinfo.php?uid=<{$article.uid}>"
               title="<{$article.uname}>"><{$article.uname}></a> <{$article.time}><br>
            <{assign var=tagbar value=$article.tags}>
            <{include file="db:tag_bar.tpl"}>
        </div>
        <div class="tag-item-content">
            <{$article.content}>
        </div>
    <{/foreach}>
</div>

<{if $tag_addon}>
    <div class="tag-item-meta" style="padding-top: 10px;">
        <{$tag_addon.title}>:
        <{foreach item=addon from=$tag_addon.addons}> <{$addon}><{/foreach}>
    </div>
<{/if}>

<div id="pagenav" style="padding-top: 10px;">
    <{$pagenav}>
</div>
