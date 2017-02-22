<{* Alphabet block *}>
<div class="clearer">
    <div class="toprow">
        <fieldset>
            <legend><{$smarty.const._MD_LEXIKON_BROWSELETTER}></legend>
            <div class="letters">
                <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php"
                   title="[ <{$publishedwords}> ]"><{$smarty.const._MD_LEXIKON_ALL}></a> |
                <{foreach item=letterlinks from=$alpha.initial}>
                    <{if $letterlinks.total > 0}> <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php?init=<{$letterlinks.id}>" title="[ <{$letterlinks.total}> ]" ><{/if}><{$letterlinks.linktext}>
                    <{if $letterlinks.total > 0}></a><{/if}> |<{/foreach}>
                <{if $totalother > 0}><a
                        href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php?init=<{$smarty.const._MD_LEXIKON_OTHER}>"
                        title="[ <{$totalother}> ]"><{/if}><{$smarty.const._MD_LEXIKON_OTHER}><{if $totalother > 0}></a><{/if}>
            </div>
        </fieldset>
    </div>
</div>

<{* Category block *}>
<{if $multicats == 1}>
    <div class="clearer">
        <fieldset class="item" style="border:1px solid #778;margin:1em 0;text-align:left;background-color:trans;">
            <legend><{$smarty.const._MD_LEXIKON_BROWSECAT}></legend>
            <div class="letters" style="margin:1em 0;width:100%;padding:0;text-align:center;line-height:1.3em;">
                <{foreach item=catlinks from=$block0.categories}>
                    <{if $catlinks.image != "" && $show_screenshot == '1'}>
                        <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$category.id}>"
                           target="_parent">
                            <img src="<{$xoops_url}>/uploads/lexikon/categories/images/<{$catlinks.image}>"
                                 width="<{$logo_maximgwidth}>" align="middle" alt="[<{$catlinks.total}>]"/></A>
                    <{/if}>
                    <{if $catlinks.total > 0}><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$catlinks.id}>" title="[<{$catlinks.total}>]"><{/if}><{$catlinks.linktext}>
                    <{if $catlinks.total > 0}></a> <{/if}>[<{$catlinks.total}>] |
                <{/foreach}>
                <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php"
                   title="[<{$publishedwords}>]"><{$smarty.const._MD_LEXIKON_ALLCATS}></a> [<{$publishedwords}>]
            </div>
        </fieldset>
    </div>
<{/if}>
