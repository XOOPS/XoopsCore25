<div style='width: auto;height:100%; border: 1px solid #ccc; padding: 4px;'>
    <div style='background-color: #F5F5F5;font-family:verdana,arial, sans-serif; font-size:small; text-align: center;  padding-bottom: 8px; margin: 0 0 4px 0; border-bottom: 1px dotted #ccc; '>
        <A STYLE='color:#000;font-weight:bold;text-decoration:none;' TARGET='_blank'
           HREF=<{$xoops_url}>><{$lang_modulename}> - <{$smarty.const._MD_LEXIKON_TERMOFTHEDAY}></A>
    </div>
    <{if $multicats == 1}>
        <div style='padding-bottom: 10px;text-align:left;font-family:verdana,arial, sans-serif; font-size:small;'><{$smarty.const._MD_LEXIKON_ENTRYCATEGORY}>
            <A STYLE='color:#000;font-weight:bold;text-decoration:none;' TARGET='_blank'
               HREF="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$syndication.id}>"><{$syndication.categoryname}></A>
        </div>
    <{/if}>
    <h4 style='margin: 0;text-align:left;font-family:verdana,arial, sans-serif; font-size:normal;'><{$syndication.term}></h4>
    <p style='text-align:left;font-family:verdana,arial, sans-serif; font-size:small;'><{$syndication.definition}></p>
    <div style='min-width:100%;border-top: 1px dotted #ccc;width=100%;position:absolute; right:3px; bottom:6px; padding-top: 12px; text-align:right;font-family:verdana,arial, sans-serif; font-size:x-small;'>
        <A
                HREF=javascript:location.reload()> <{$smarty.const._MD_LEXIKON_RANDOMIZE}></A><br>
        <{$smarty.const._MD_LEXIKON_POWER}> <A HREF="<{$xoops_url}>" TARGET='_blank'><{$lang_sitename}></A>
    </div>
</div>
