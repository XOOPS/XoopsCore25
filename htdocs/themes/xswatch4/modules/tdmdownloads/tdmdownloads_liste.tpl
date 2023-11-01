<div class="tdmdownloads">
    <!-- Download logo-->
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">TDMDownloads</a></li>
        <li class="breadcrumb-item active">File List</li>
    </ol>

    <!-- Download searchform -->
    <div class="tdmdownloads-searchform"><{$searchForm}></div>
    <div class="tdmdownloads-thereare"><{$lang_thereare}></div>

    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col"><{$smarty.const._MD_TDMDOWNLOADS_SEARCH_TITLE}></th>
            <th scope="col" class="d-none d-lg-table-cell"> </th>
            <th scope="col"><{$smarty.const._MD_TDMDOWNLOADS_SEARCH_CATEGORIES}></th>
            <{foreach item=fielditem from=$field|default:null}>
                <th scope="col" class="d-none d-lg-table-cell"><{$fielditem}></th>
            <{/foreach}>
            <th scope="col" class="d-none d-sm-table-cell"><{$smarty.const._MD_TDMDOWNLOADS_SEARCH_DATE}></th>
            <th scope="col" class="d-none d-md-table-cell"><{$smarty.const._MD_TDMDOWNLOADS_SEARCH_NOTE}></th>
            <th scope="col" class="d-none d-md-table-cell"><{$smarty.const._MD_TDMDOWNLOADS_SEARCH_HITS}></th>
            <th scope="col">&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <{foreach item=download from=$search_list|default:null}>
            <tr>
                <td>
                    <a href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$download.cid}>&amp;lid=<{$download.lid}>" title="<{$download.title}>"><{$download.title}></a>
                </td>
                <td class="d-none d-lg-table-cell">
                    <img src="<{$download.imgurl}>" alt="<{$download.cat}>" title="<{$download.cat}>" width="30">
                </td>
                <td>
                    <a href="<{$xoops_url}>/modules/tdmdownloads/viewcat.php?cid=<{$download.cid}>" target="_blank" title="<{$download.cat}>"><{$download.cat}></a>
                </td>
                <{foreach item=fielddata from=$download.fielddata|default:null}>
                    <td class="d-none d-lg-table-cell"><{$fielddata}></td>
                <{/foreach}>
                <td class="d-none d-sm-table-cell"><{$download.date}></td>
                <td class="d-none d-md-table-cell"><{$download.rating}></td>
                <td class="d-none d-md-table-cell"><{$download.hits}></td>
                <td>
                    <a href="<{$xoops_url}>/modules/tdmdownloads/visit.php?cid=<{$download.cid}>&amp;lid=<{$download.lid}>" target="_blank">
                        <img src="<{$pathModIcon16}>/download-now.png" alt="<{$smarty.const._MD_TDMDOWNLOADS_SEARCH_DOWNLOAD}><{$download.title}>" title="<{$smarty.const._MD_TDMDOWNLOADS_SEARCH_DOWNLOAD}><{$download.title}>">
                    </a>
                    <a href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$download.cid}>&amp;lid=<{$download.lid}>" title="<{$download.title}>">
                        <img src="<{$pathModIcon16}>/view_mini.png" alt="<{$smarty.const._PREVIEW}><{$download.title}>" title="<{$smarty.const._PREVIEW}>">
                    </a>
                    <{if isset($perm_submit)}>
                        <a href="<{$xoops_url}>/modules/tdmdownloads/modfile.php?lid=<{$download.lid}>" title="<{$download.title}>">
                            <img src="<{$pathModIcon16}>/edit.png" alt="<{$smarty.const._EDIT}><{$download.title}>" title="<{$smarty.const._EDIT}>">
                        </a>
                    <{/if}>
                </td>
            </tr>
        <{/foreach}>
        </tbody>
    </table>

    <{if !empty($pagenav)}>
    <div class="generic-pagination col text-right mt-2">
        <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
    </div>
    <{/if}>

</div>
