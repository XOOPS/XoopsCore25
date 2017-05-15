<div class="xoopstube">
    <{$video.icons}>

    <div class="xoopstube-video-play">
        <{$video.showvideo}>
    </div><!-- .xoopstube-video-play -->

    <div class="xoopstube-data clearfix">
        <div class="col-md-6 xoopstube-video-info">
            <h3><{$video.title}></h3>

            <div class="row">
                <div class="col-md-8"><strong><{$smarty.const._MD_XOOPSTUBE_PUBLISHER}>:</strong> <{$video.publisher}></div>
                <div class="col-md-4 alignright"><{$video.hits|wordwrap:50:"\n":true}></div>
            </div>

            <div class="row">
                <div class="col-md-6"><strong><{$smarty.const._MD_XOOPSTUBE_SUBMITTER}>:</strong> <{$video.submitter}></div>
                <div class="col-md-6 alignright"><{$video.updated|wordwrap:50:"\n":true}></div>
            </div>

            <div class="row">
                <div class="col-md-6"><strong><{$smarty.const._MD_XOOPSTUBE_CATEGORYC}></strong> <{$video.category}></div>
                <div class="col-md-6 alignright"><strong><{$smarty.const._MD_XOOPSTUBE_TIMEB}></strong> <{$video.time}></div>
            </div>

            <strong><{$smarty.const._MD_XOOPSTUBE_DESCRIPTIONC}></strong>

            <p><{$video.description2}></p>
        </div>

        <div class="col-md-6">
            <ul class="list-unstyled xoopstube-list">
                <{if $video.showrating}>
                    <{if $video.allow_rating}>
                        <li><i class="glyphicon glyphicon-thumbs-up"></i>
                            <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/ratevideo.php?cid=<{$video.cid}>&amp;lid=<{$video.id}>"
                               title="<{$smarty.const._MD_XOOPSTUBE_RATETHISFILE}>"><{$smarty.const._MD_XOOPSTUBE_RATETHISFILE}></a></li>
                    <{/if}>
                <{/if}>

                <li><i class="glyphicon glyphicon-warning-sign"></i>
                    <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/brokenvideo.php?lid=<{$video.id}>"
                       title="<{$smarty.const._MD_XOOPSTUBE_REPORTBROKEN}>">
                        <{$smarty.const._MD_XOOPSTUBE_REPORTBROKEN}>
                    </a>
                </li>

                <{if $video.useradminvideo}>
                    <li><i class="glyphicon glyphicon-edit"></i><{$video.usermodify}></li>
                <{/if}>

                <li><i class="glyphicon glyphicon-share-alt"></i>
                    <a href="mailto:?subject=<{$video.mail_subject}>&body=<{$video.mail_body}>" title="<{$smarty.const._MD_XOOPSTUBE_TELLAFRIEND}>">
                        <{$smarty.const._MD_XOOPSTUBE_TELLAFRIEND}>
                    </a>
                </li>

                <{if $video.comment_rules > 0}>
                    <li><i class="glyphicon glyphicon-comment"></i>
                        <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/singlevideo.php?cid=<{$video.cid}>&amp;lid=<{$video.id}>"
                           title="<{$smarty.const._COMMENTS}>">
                            <{$smarty.const._COMMENTS}> (<{$video.comments}>)
                        </a>
                    </li>
                <{/if}>

                <{if $video.showrating}>
                    <li>
                        <{$smarty.const._MD_XOOPSTUBE_RATINGC}>
                        <img src="<{$xoops_url}>/modules/<{$video.module_dir}>/assets/images/icon/<{$video.rateimg}>" alt=""> (<{$video.votes}>)
                    </li>
                <{/if}>

                <{if $tagbar}>
                    <li><{include file="db:tag_bar.tpl"}></li>
                <{/if}>

                <{if $xoops_isadmin}>
                    <li><{$video.adminvideo}></li>
                <{/if}>
            </ul>
        </div>
        <div class="col-md-12 xoopstube-other-video">
            <{if $video.othervideox > 0}>
                <h3 class=".xoops-default-title"><{$other_videos}></h3>
                <ul class="list-unstyled xoopstube-list">
                    <{foreach item=video_user from=$video_uid}>
                        <li><i class="glyphicon glyphicon-film"></i>
                            <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/singlevideo.php?cid=<{$video_user.cid}>&amp;lid=<{$video_user.lid}>"
                               title="<{$video_user.title}>"><{$video_user.title}></a>
                            <span class="pull-right">(<{$video_user.published}>)</span>
                        </li>
                    <{/foreach}>
                </ul>
            <{/if}>
        </div>

        <div class="col-md-12 text-center xoopstube-credits"><em><{$lang_copyright}></em></div>

        <div class="col-md-12">
            <{if $video.showsbookmarx > 0}>
                <div class='shareaholic-canvas' data-app='share_buttons' data-app-id=''></div>
            <{/if}>
        </div>
    </div><!-- .xoopstube-data -->
</div><!-- .xoopstube -->

<{$commentsnav}> <{$lang_notice}>

<{if $comment_mode == "flat"}>
    <{include file="db:system_comments_flat.tpl"}>
<{elseif $comment_mode == "thread"}>
    <{include file="db:system_comments_thread.tpl"}>
<{elseif $comment_mode == "nest"}>
    <{include file="db:system_comments_nest.tpl"}>
<{/if}>

<{include file="db:system_notification_select.tpl"}>
