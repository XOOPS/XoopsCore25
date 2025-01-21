<!-- Article Slider -->
<script src="<{xoImgUrl}>js/owl/owl.carousel.js"></script>
<style>
    #articleslider .item img {
        display: block;
        width: 100%;
        height: auto;
    }
</style>
<script>
    $(document).ready(function () {
        $(".owl-carousel").owlCarousel({
            margin: 10,
            autoHeight: true,
            autoplay: true,
            items: 1,
            dotsEach: true,
            dots: true,
            loop: true,
            autoplayHoverPause: true
        });
    });
</script>
<{include file='db:publisher_header.tpl'}>
<div class="pub_article_t_top clearfix">
    <header>
        <h2>
            <{$item.titlelink}>
            <span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-tag"></span>&nbsp;<{$item.category}>
                </span>
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-user"></span>&nbsp;<{$item.who}>
                </span>
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-calendar"></span>&nbsp;<{$item.when}>
                </span>
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-comment"></span>&nbsp;<{$item.comments}>
                </span>
            <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="fa fa-check-circle"></span>&nbsp;<{$item.counter}> <{$smarty.const._MD_PUBLISHER_READS}>
                </span>
        </h2>

        <{if $show_subtitle && $item.subtitle}>
            <h5><{$item.subtitle}></h5>
        <{/if}>
    </header>
    <{if $item.image_path || $item.images}>
        <figure>
            <{if $item.images}>
                <div id="articleslider" class="owl-carousel owl-theme" style="margin-bottom:10px;">
                    <div class="item">
                        <img class="img-fluid" src="<{$item.image_path}>">
                    </div>
                    <{foreach item=image from=$item.images|default:null}>
                        <div class="item">
                            <img class="img-fluid" src="<{$image.path}>" alt="<{$image.name}>">
                        </div>
                    <{/foreach}>
                </div>
            <{elseif $item.image_path}>
                <img style="margin-bottom:15px;" class="img-fluid" src="<{$item.image_path}>" alt="<{$item.image_name}>">
            <{/if}>
        </figure>
    <{/if}>
    <div>
        <{$item.maintext}>

        <div class="shareaholic-canvas" data-bs-app="share_buttons" data-bs-app-id=""></div>
    </div>

</div>
<{if $pagenav|default:false}>
    <div class="pub_pagenav text-end">
        <{$pagenav}>
    </div>
<{/if}>
<div class="clearfix"></div>
<div class="pub_article_extras">
    <{if isset($rating_enabled)}>
        <div class="pull-left">
            <small><{$item.ratingbar}></small>
        </div>
    <{/if}>
    <div class="pull-right text-end">
        <{$item.adminlink}>
    </div>
    <div class="clearfix"></div>
</div>

<{if isset($itemfooter)}>
    <div class="panel-footer">
        <small><{$itemfooter}></small>
    </div>
<{/if}>


<!-- Attached Files -->
<{if $item.files}>

    <{foreach item=file from=$item.files|default:null}>

    <{/foreach}>
    <table class="table table-bordered table-condensed" style="margin: 15px 0;">
        <thead>
        <tr>
            <th width="60%"><{$smarty.const._CO_PUBLISHER_FILENAME}></th>
            <th width="30%"><{$smarty.const._MD_PUBLISHER_DATESUB}></th>
            <th width="9%"><{$smarty.const._MD_PUBLISHER_HITS}></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <{if $file.mod}>
                    <a href="<{$publisher_url}>/file.php?op=mod&fileid=<{$file.fileid}>">
                        <img src="<{$publisher_url}>/assets/images/links/edit.gif" title="<{$smarty.const._CO_PUBLISHER_EDITFILE}>" alt="<{$smarty.const._CO_PUBLISHER_EDITFILE}>"></a>
                    <a href="<{$publisher_url}>/file.php?op=del&fileid=<{$file.fileid}>">
                        <img src="<{$publisher_url}>/assets/images/links/delete.png" title="<{$smarty.const._CO_PUBLISHER_DELETEFILE}>" alt="<{$smarty.const._CO_PUBLISHER_DELETEFILE}>"></a>
                <{/if}>
                <a href="<{$publisher_url}>/visit.php?fileid=<{$file.fileid}>" target="_blank">
                    <img src="<{$publisher_url}>/assets/images/links/file.gif" title="<{$lang_download_file|default:''}>" alt="<{$smarty.const._MD_PUBLISHER_DOWNLOAD_FILE}>">&nbsp;<strong><{$file.name}></strong>
                </a>

                <div style="font-size:12px;"><{$file.description}></div>

            </td>
            <td><{$file.datesub}></td>
            <td><{$file.hits}></td>
        </tr>
        </tbody>
    </table>
<{/if}>
<!-- End Attached Files -->

<!-- Items by same Author -->
<{if $perm_author_items && $item.uid|default:0 != 0}>
    <div class="pub_article_extras">
        <div class="btn btn-primary btn-lg w-100">
            <a href="<{$publisher_url}>/author_items.php?uid=<{$item.uid}>">
                <{$smarty.const._MD_PUBLISHER_ITEMS_SAME_AUTHOR}>
            </a>
        </div>
    </div>
<{/if}>
<!-- END Items by same Author -->

<!-- Other articles in the category -->
<{if $other_items == "previous_next"}>
    <{if $previous_item_link|default:'' || $next_item_link|default:''}>
        <{if isset($previous_item_link)}>
            <div class="pull-left">
                <a href="<{$previous_item_url}>">
                    <img style="vertical-align: middle;" src="<{$publisher_images_url}>/links/previous.gif" title="<{$smarty.const._MD_PUBLISHER_PREVIOUS_ITEM}>" alt="<{$smarty.const._MD_PUBLISHER_PREVIOUS_ITEM}>">
                </a>
                <{$previous_item_link}>
            </div>
        <{/if}>
        <{if isset($next_item_link)}>
            <div class="text-end">
                <{$next_item_link}>
                <a href="<{$next_item_url}>">
                    <img style="vertical-align: middle;" src="<{$publisher_images_url}>/links/next.gif" title="<{$smarty.const._MD_PUBLISHER_NEXT_ITEM}>" alt="<{$smarty.const._MD_PUBLISHER_NEXT_ITEM}>">
                </a>
            </div>
        <{/if}>
    <{/if}>
<{elseif $other_items == 'all'}>

    <{if $display_date_col == 1}>

    <{/if}>
    <{if $display_hits_col == 1}>

    <{/if}>

    <{foreach item=item from=$items|default:null}>

        <{if $display_date_col == 1}>

        <{/if}>
        <{if $display_hits_col == 1}>

        <{/if}>

    <{/foreach}>
    <table class="table table-bordered table-condensed" style="margin: 15px 0;">
        <thead>
        <tr>
            <th><{$smarty.const._MD_PUBLISHER_OTHER_ITEMS}></th>
            <th style="text-align: center;"><{$smarty.const._MD_PUBLISHER_DATESUB}></th>
            <th style="text-align: center;"><{$smarty.const._MD_PUBLISHER_HITS}></th>
        </tr>
        </thead>
        <tbody>
        <!-- Start item loop -->
        <tr>
            <td class="even" align="left"><{$item.titlelink}></td>
            <td style="text-align: center;"><{$item.datesub}></td>
            <td style="text-align: center;"><{$item.counter}></td>
        </tr><!-- End item loop -->
        </tbody>
    </table>
<{/if}>
<!-- END Other articles in the category -->

<{if $tagbar|default:false}>
    <p><{include file="db:tag_bar.tpl"}></p>
<{/if}>

<{include file='db:publisher_footer.tpl'}>
