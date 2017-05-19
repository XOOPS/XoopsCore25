<{$video.icons}>

<div class="xoopstube-loop clearfix">
    <div class="col-sm-4 col-md-4 xoopstube-video-thumb">
        <{if $video.screen_shot}>
            <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/singlevideo.php?cid=<{$video.cid}>&amp;lid=<{$video.id}>" title="<{$video.title}>">
                <{$video.videothumb}>
            </a>
        <{/if}>

        <span><{$smarty.const._MD_XOOPSTUBE_TIMEB}> <{$video.time}></span>
    </div>

    <div class="col-sm-8 col-md-8">
        <ul class="list-unstyled">
            <li>
                <h3 class="xoopstube-video-title">
                    <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/singlevideo.php?cid=<{$video.cid}>&amp;lid=<{$video.id}>"
                       title="<{$video.title}>">
                        <{$video.title}>
                    </a>
                    <{if $video.published > 0 }>
                        <span class="pull-right">
            <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/singlevideo.php?cid=<{$video.cid}>&amp;lid=<{$video.id}>">
                <i title="Play" class="glyphicon glyphicon-play"></i>
            </a>
            </span>
                    <{/if}>
                </h3>
            </li>

            <{if $xoops_isadmin}>
                <li><{$video.adminvideo}></li>
            <{/if}>

            <li><strong><{$smarty.const._MD_XOOPSTUBE_CATEGORYC}></strong> <{$video.category}></li>

            <li><strong><{$smarty.const._MD_XOOPSTUBE_SUBMITTER}>:</strong> <{$video.submitter}></li>

            <li><strong><{$smarty.const._MD_XOOPSTUBE_PUBLISHER}>:</strong> <{$video.publisher}></li>

            <li><strong><{$lang_subdate}>:</strong> <{$video.updated}></li>

            <li><{$video.hits|wordwrap:50:"\n":true}></li>

            <{if $video.showrating}>
                <li>
                    <strong><{$smarty.const._MD_XOOPSTUBE_RATINGC}></strong>
                    <img src="<{$xoops_url}>/modules/<{$video.module_dir}>/assets/images/icon/<{$video.rateimg}>" alt=""> (<{$video.votes}>)
                </li>
            <{/if}>
            <li><strong><{$smarty.const._MD_XOOPSTUBE_DESCRIPTIONC}></strong>

                <p><{$video.description|truncate:$video.total_chars}></p>
            </li>
        </ul>
    </div>
</div>
