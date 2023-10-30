<div class="tdmdownloads">

    <div class="breadcrumb"><{$navigation|replace:'<img src="assets/images/deco/arrow.gif" alt="arrow">':'&nbsp;/&nbsp;'}></div>

    <!-- <{if $new || $pop}><{$new}><{$pop}><{/if}> -->

    <h1 class="tdm-title"><{$title}> <{if !empty($version)}><label class="label label-success"><{$version}></label><{/if}></h1>

    <div class="tdm-download-data row">
        <{if isset($show_screenshot) && $show_screenshot == true}>
            <{if !empty($logourl)}>
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

                    <{if !empty($commentsnav)}>
                <li><{$nb_comments}>
                </li}>
                    <{/if}>

                    <{if isset($sup_aff) && $sup_aff == true}>
                    <{foreach item=champ from=$champ_sup|default:null}>
                <li><{$champ.data}></li>
                <{/foreach}>
                <{/if}>


                <{if !empty($perm_vote)}>
                    <li><a class="btn btn-xs btn-primary" href="<{$xoops_url}>/modules/tdmdownloads/ratefile.php?lid=<{$lid}>"
                           title="<{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_RATHFILE}>"><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_RATHFILE}></a></li>
                <{/if}>

                <{if !empty($perm_modif)}>
                    <li><a class="btn btn-xs btn-primary" href="<{$xoops_url}>/modules/tdmdownloads/modfile.php?lid=<{$lid}>"
                           title="<{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_MODIFY}>"><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_MODIFY}></a></li>
                <{/if}>

                <li><a class="btn btn-xs btn-primary" href="<{$xoops_url}>/modules/tdmdownloads/brokenfile.php?lid=<{$lid}>"
                       title="<{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_REPORTBROKEN}>"><{$smarty.const._MD_TDMDOWNLOADS_SINGLEFILE_REPORTBROKEN}></a>
                </li>

                <!--<li><{$tellafriend_texte}></li>-->

                <{if !empty($perm_download)}>
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

                <{if isset($adminlink)}>
                    <li class="text-center"><{$adminlink}></li>
                <{/if}>
            </ul>
        </div>
    </div><!-- .tdm-download-data -->

    <div class="col m2-3 mb-3">
        <{$description}>
    </div>

    <{if isset($paypal)}>
        <{$paypal}>
    <{/if}>

    <{if isset($tags)}>
        <{include file="db:tag_bar.tpl"}>
    <{/if}>

    <{if isset($show_social)}>
        <div class='shareaholic-canvas' data-app='share_buttons' data-app-id=''></div>
    <{/if}>
</div><!-- .tdmdownloads -->

<{$commentsnav}>
<div class="row d-flex justify-content-center"><{$lang_notice}></div>

<{if isset($comment_mode)}>
    <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
    <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
    <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
    <{/if}>
<{/if}>

<{include file="db:system_notification_select.tpl"}>
