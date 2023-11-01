<div class="card col-8 col-sm-6 col-md-4 col-xl-3 mb-2">
    <{if isset($show_screenshot) && $show_screenshot == true}>
        <{if !empty($down.logourl)}>
        <img class="card-img-top img-fluid" src="<{$down.logourl}>" alt="<{$down.title}>">
        <{else}>
        <img class="card-img-top img-fluid" src="<{$xoops_imageurl}>images/tdm-no-image.jpg" alt="<{$title}>">
        <{/if}>
    <{/if}>
    <div class="card-body">
        <h5 class="card-title"><{$down.title}></h5>
        <p class="card-text text-muted">
            <span class="fa fa-calendar" title="<{$smarty.const._MD_TDMDOWNLOADS_INDEX_SUBMITDATE}>"></span> <{$down.updated}>
            <span class="fa fa-user" title="<{$smarty.const._MD_TDMDOWNLOADS_INDEX_SUBMITTER}>"></span> <{$down.submitter}>
        </p>
        <p class="card-text"><{$down.description_short}>
            <a class="stretched-link" href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>" title="<{$down.title}>"><span class="fa fa-forward"></span></a>
        </p>
    </div>
    <{if !empty($down.perm_download)}>
    <div class="position-static">
        <{if $down.new}><{$down.new}><{/if}><{if $down.pop}><{$down.pop}><{/if}>
        <a title="<{$smarty.const._MD_TDMDOWNLOADS_INDEX_DLNOW}>" href="visit.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>"
           class="btn btn-success">
            <span class="fa fa-fw fa-cloud-download"></span>
        </a>
    </div>
    <{/if}>
</div>
