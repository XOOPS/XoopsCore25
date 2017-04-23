<div class="alert alert-success"> <{$search_info}> </div>
<br>
<{if $results}>
    <table class="table" border="0" cellpadding="0" cellspacing="0" align="center" width="95%">
        <thead>
        <tr class="head" align="center">
            <th><{$smarty.const._MD_FORUMC}></th>
            <th><{$smarty.const._MD_SUBJECT}></th>
            <th><{$smarty.const._MD_AUTHOR}></th>
            <th nowrap="nowrap"><{$smarty.const._MD_POSTTIME}></th>
        </tr>
        </thead>
        <tbody>
        <!-- start search results -->
        <{section name=i loop=$results}>
        <!-- start each result -->
        <tr align="center">
            <td class="even"><a href="<{$results[i].forum_link}>"><{$results[i].forum_name}></a></td>
            <!-- irmtfan hardcode removed align="left" -->
            <td class="odd" id="align_left"><a href="<{$results[i].link}>"><{$results[i].title}></a></td>
            <td class="even"><{$results[i].poster}></a></td>
            <td class="odd"><{$results[i].post_time}></td>
        </tr>
        <!-- START irmtfan add show search -->
        <{if $results[i].post_text }>
        <tr align="center">
            <td class="even"></td>
            <td class="odd"><{$results[i].post_text}></td>
            <td class="even"></td>
            <td class="odd"></td>
        </tr>
        <{/if}>
        <!-- END irmtfan add show search -->
        <!-- end each result -->
        <{/section}>
        <!-- end search results -->
        
        <{if $search_next or $search_prev}>
        <tr>
            <!-- irmtfan hardcode removed align="left" -->
            <td colspan="2" class="align_left"><{$search_prev}> </td>
            <td colspan="2" class="align_right"> <{$search_next}></td>
        </tr>
        <{/if}>
        </tbody>
    </table>
    <br>
<{elseif $lang_nomatch}>
    <div class="resultMsg"> <{$lang_nomatch}> </div>
    <br>
<{/if}>
