<ul class="list-unstyled tdm-new-download-block">
    <{foreachq item=downloads from=$block}>
    <li><i class="glyphicon glyphicon-cloud-download"></i>
        <a title="<{$downloads.title}>" href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?lid=<{$downloads.lid}>">
            <{$downloads.title}>
        </a>

        <{if $downloads.inforation}>
            <{$smarty.const._MB_TDMDOWNLOADS_SUBMITDATE}><{$downloads.date}>
            <{$smarty.const._MB_TDMDOWNLOADS_SUBMITTER}><{$downloads.submitter}>
            <{$smarty.const._MB_TDMDOWNLOADS_REATING}><{$downloads.rating}>
            <{$smarty.const._MB_TDMDOWNLOADS_HITS}><{$downloads.hits}>
        <{/if}>
    </li>
    <{/foreach}>
</ul>
