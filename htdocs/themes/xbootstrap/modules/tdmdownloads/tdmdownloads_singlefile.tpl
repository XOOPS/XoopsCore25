<div class="tdmdownloads">

    <div class="breadcrumb"><{$navigation}></div>

    <!-- <{if $new || $pop}><{$new}><{$pop}><{/if}> -->

    <h1 class="tdm-title"><{$title}> <label class="label label-success">v <{$version}></label></h1>

    <div class="tdm-download-data row">
        <{if $show_screenshot == true}>
            <{if $logourl != ""}>
                <div class="tdm-screenshot-single col-xs-8 col-sm-8 col-md-8">
                    <img src="<{$logourl}>" alt="<{$title}>">
                </div>
            <{else}>
                <div class="tdm-screenshot-single col-xs-8 col-sm-8 col-md-8">
                    <img src="<{$xoops_imageurl}>images/tdm-no-image.jpg" alt="<{$title}>">
                </div>
            <{/if}>
        <{/if}>

        <div class="col-sm-4 col-md-4">
            <ul class="list-unstyled tdm-download-details">
                <li><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_DATEPROP}> <{$date}>
                </li}>

                <li><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_AUTHOR}> <{$author}>
                </li}>

                <li><{$hits}>
                </li}>

                <li><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_RATING}>: <{$rating}> <{$votes}>
                </li}>

                    <{if $commentsnav != ""}>
                <li><{$nb_comments}>
                </li}>
                    <{/if}>

                    <{if $sup_aff == true}>
                    <{foreach item=champ from=$champ_sup}>
                <li><{$champ.data}></li>
                <{/foreach}>
                <{/if}>


                <{if $perm_vote != ""}>
                    <li><a class="btn btn-xs btn-primary" href="<{$xoops_url}>/modules/tdmdownloads/ratefile.php?lid=<{$lid}>"
                           title="<{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_RATHFILE}>"><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_RATHFILE}></a></li>
                <{/if}>

                <{if $perm_modif != ""}>
                    <li><a class="btn btn-xs btn-primary" href="<{$xoops_url}>/modules/tdmdownloads/modfile.php?lid=<{$lid}>"
                           title="<{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_MODIFY}>"><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_MODIFY}></a></li>
                <{/if}>

                <li><a class="btn btn-xs btn-primary" href="<{$xoops_url}>/modules/tdmdownloads/brokenfile.php?lid=<{$lid}>"
                       title="<{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_REPORTBROKEN}>"><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_REPORTBROKEN}></a>
                </li>

                <!--<li><{$tellafriend_texte}></li>-->

                <{if $perm_download != ""}>
                    <li><a class="btn btn-md btn-success" href="visit.php?cid=<{$cid}>&amp;lid=<{$lid}>" target="_blank" title="Download"><{$smarty.const._MD_TDMDOWNLOADS_INDEX_DLNOW}></a>
                    </li>
                <{else}>
                    <li>
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_NOPERM}>
                        </div>
                    </li>
                <{/if}>

                <{if $adminlink}>
                    <li class="text-center"><{$adminlink}></li>
                <{/if}>
            </ul>
        </div>
    </div><!-- .tdm-download-data -->

    <div class="text-center">
        <a class="big-info-icon-link" title="Info" data-toggle="collapse" href="#tdm-description"><span class="glyphicon glyphicon-info-sign"></span></a>
    </div>
    <div class="collapse" id="tdm-description">
        <{$description}>
    </div>

    <{if $paypal}>
        <{$paypal}>
    <{/if}>

    <{if $tags}>
        <{include file="db:tag_bar.tpl"}>
    <{/if}>

    <{if $show_social}>
        <div class='shareaholic-canvas' data-app='share_buttons' data-app-id=''></div>
    <{/if}>
</div><!-- .tdmdownloads -->

<!-- <{$commentsnav}> -->

<{$lang_notice}>

<{if $comment_mode == "flat"}>
    <{include file="db:system_comments_flat.tpl"}>
<{elseif $comment_mode == "thread"}>
    <{include file="db:system_comments_thread.tpl"}>
<{elseif $comment_mode == "nest"}>
    <{include file="db:system_comments_nest.tpl"}>
<{/if}>

<{include file="db:system_notification_select.tpl"}>
