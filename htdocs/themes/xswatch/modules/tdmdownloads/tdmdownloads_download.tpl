<div class="col-sm-4 col-md-4 tdm-minibox">
    <{if $show_screenshot == true}>
        <{if $down.logourl != ''}>
            <div class="tdm-download-logo">
                <img src="<{$down.logourl}>" alt="<{$down.title}>">
            </div>
        <{else}>
            <div class="tdm-download-logo">
                <img src="<{$xoops_imageurl}>images/tdm-no-image.jpg" alt="<{$title}>">
            </div>
        <{/if}>
    <{/if}>

    <a class="tdm-title" title="<{$down.title}>" href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
        <{$down.title}>
    </a>
    <!--
    <{if $down.new}><{$down.new}><{/if}>

    <{if $down.pop}><{$down.pop}><{/if}>
    -->
    <div class="row tdm-download-data">
        <div class="col-md-5"><span class="glyphicon glyphicon-calendar" title="<{$smarty.const._MD_TDMDOWNLOADS_INDEX_SUBMITDATE}>"></span>
            <{$down.updated}>
        </div>
        <div class="col-md-7"><span class="glyphicon glyphicon-user" title="<{$smarty.const._MD_TDMDOWNLOADS_INDEX_SUBMITTER}>"></span>
            <{$down.submitter}>
        </div>
    </div>

    <div class="tdm-short-description">
        <{$down.description_short}>
    </div><!-- .tdm-short-description -->

    <a class="btn btn-primary col-md-9" title="<{$down.title}>"
       href="<{$xoops_url}>/modules/tdmdownloads/singlefile.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>">
        <{$smarty.const._MD_TDMDOWNLOADS_MOREDETAILS}>
    </a>

    <{if $down.perm_download != ""}>
        <a title="<{$smarty.const._MD_TDMDOWNLOADS_INDEX_DLNOW}>" href="visit.php?cid=<{$down.cid}>&amp;lid=<{$down.id}>"
           class="btn btn-success btn-xs tdm-download-btn col-md-2 pull-right">
            <span class="glyphicon glyphicon-cloud-download"></span>
        </a>
    <{/if}>

    <!-- <{$down.adminlink}> -->
</div><!-- .tdm-minibox -->
