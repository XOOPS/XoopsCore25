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
<div class="pub_article_t_bottom clearfix">
    <{if $item.image_path || $item.images}>
        <figure>
            <{if $item.images}>
                <div id="articleslider" class="owl-carousel owl-theme">
                    <div class="item">
                        <img class="img-responsive" src="<{$item.image_path}>"/>
                    </div>
                    <{foreach item=image from=$item.images}>
                        <div class="item">
                            <img class="img-responsive" src="<{$image.path}>" alt="<{$image.name}>"/>
                        </div>
                    <{/foreach}>
                </div>
            <{elseif $item.image_path}>
                <img class="img-responsive" src="<{$item.image_path}>" alt="<{$item.image_name}>"/>
            <{/if}>
        </figure>
    <{/if}>
    <header>
        <h2 <{if !$item.image_path}>class="no-top-margin"<{/if}>><{$item.title}></h2>
        <{if $show_subtitle && $item.subtitle}>
            <h5><{$item.subtitle}></h5>
        <{/if}>
    </header>
    <div class="pub_article_t_bottom_info">
        <div class="pull-left">
            <span class="glyphicon glyphicon-paperclip"></span>&nbsp;<{$item.category}>
            &nbsp;&nbsp;<{$item.who_when}>
        </div>
        <div class="pull-right">
            <{if $display_comment_link && $item.cancomment}>
                <{$item.comments}> <{$smarty.const._MD_PUBLISHER_COMMENTS}>
            <{/if}>
        </div>
    </div>
    <div>
        <{$item.maintext}>
    </div>

</div>
<{if $pagenav}>
    <div class="pub_pagenav text-right">
        <{$pagenav}>
    </div>
<{/if}>
<div class="clearfix"></div>
<div class="pub_article_extras">
    <{if $rating_enabled}>
        <div class="pull-left">
            <small><{$item.ratingbar}></small>
        </div>
    <{/if}>
    <div class="pull-right text-right">
        <{$item.adminlink}>
    </div>
    <div class="clearfix"></div>
</div>

<{if $itemfooter}>
    <div class="panel-footer">
        <small><{$itemfooter}></small>
    </div>
<{/if}>


<!-- Attached Files -->
<{if $item.files}>
    <table class="table table-bordered table-condensed" style="margin: 15px 0;">
        <thead>
        <tr>
            <th width="60%"><{$smarty.const._CO_PUBLISHER_FILENAME}></th>
            <th width="30%"><{$smarty.const._MD_PUBLISHER_DATESUB}></th>
            <th width="9%"><{$smarty.const._MD_PUBLISHER_HITS}></th>
        </tr>
        </thead>
        <tbody>
        <{foreach item=file from=$item.files}>
            <tr>
                <td>
                    <{if $file.mod}>
                        <a href="<{$publisher_url}>/file.php?op=mod&fileid=<{$file.fileid}>">
                            <img src="<{$publisher_url}>/assets/images/links/edit.gif" title="<{$smarty.const._CO_PUBLISHER_EDITFILE}>"
                                 alt="<{$smarty.const._CO_PUBLISHER_EDITFILE}>"/></a>
                        <a href="<{$publisher_url}>/file.php?op=del&fileid=<{$file.fileid}>">
                            <img src="<{$publisher_url}>/assets/images/links/delete.png" title="<{$smarty.const._CO_PUBLISHER_DELETEFILE}>"
                                 alt="<{$smarty.const._CO_PUBLISHER_DELETEFILE}>"/></a>
                    <{/if}>
                    <a href="<{$publisher_url}>/visit.php?fileid=<{$file.fileid}>" target="_blank">
                        <img src="<{$publisher_url}>/assets/images/links/file.gif" title="<{$lang_download_file}>"
                             alt="<{$smarty.const._MD_PUBLISHER_DOWNLOAD_FILE}>"/>&nbsp;<strong><{$file.name}></strong>
                    </a>

                    <div style="font-size:12px;"><{$file.description}></div>

                </td>
                <td><{$file.datesub}></td>
                <td><{$file.hits}></td>
            </tr>
        <{/foreach}>

        </tbody>
    </table>
<{/if}>
<!-- End Attached Files -->

<!-- Items by same Author -->
<{if $perm_author_items && $item.uid != 0}>
    <div class="pub_article_extras">
        <div class="btn btn-primary btn-lg btn-block">
            <a href="<{$publisher_url}>/author_items.php?uid=<{$item.uid}>">
                <{$smarty.const._MD_PUBLISHER_ITEMS_SAME_AUTHOR}>
            </a>
        </div>
    </div>
<{/if}>
<!-- END Items by same Author -->

<!-- Other articles in the category -->
<{if $other_items == "previous_next"}>
    <{if $previous_item_link || $next_item_link}>
        <{if $previous_item_link}>
            <div class="pull-left">
                <a href="<{$previous_item_url}>">
                    <img style="vertical-align: middle;" src="<{$publisher_images_url}>/links/previous.gif" title="<{$smarty.const._MD_PUBLISHER_PREVIOUS_ITEM}>"
                         alt="<{$smarty.const._MD_PUBLISHER_PREVIOUS_ITEM}>"/>
                </a>
                <{$previous_item_link}>
            </div>
        <{/if}>
        <{if $next_item_link}>
            <div class="text-right">
                <{$next_item_link}>
                <a href="<{$next_item_url}>">
                    <img style="vertical-align: middle;" src="<{$publisher_images_url}>/links/next.gif" title="<{$smarty.const._MD_PUBLISHER_NEXT_ITEM}>"
                         alt="<{$smarty.const._MD_PUBLISHER_NEXT_ITEM}>"/>
                </a>
            </div>
        <{/if}>
    <{/if}>
<{elseif $other_items == 'all'}>
    <table class="table table-bordered table-condensed" style="margin: 15px 0;">
        <thead>
        <tr>
            <th><{$smarty.const._MD_PUBLISHER_OTHER_ITEMS}></th>
            <{if $display_date_col == 1}>
                <th style="text-align: center;"><{$smarty.const._MD_PUBLISHER_DATESUB}></th>
            <{/if}>
            <{if $display_hits_col == 1}>
                <th style="text-align: center;"><{$smarty.const._MD_PUBLISHER_HITS}></th>
            <{/if}>
        </tr>
        </thead>
        <tbody>
        <!-- Start item loop -->
        <{foreach item=item from=$items}>
            <tr>
                <td class="even" align="left"><{$item.titlelink}></td>
                <{if $display_date_col == 1}>
                    <td style="text-align: center;"><{$item.datesub}></td>
                <{/if}>
                <{if $display_hits_col == 1}>
                    <td style="text-align: center;"><{$item.counter}></td>
                <{/if}>
            </tr>
        <{/foreach}>
        <!-- End item loop -->
        </tbody>
    </table>
<{/if}>
<!-- END Other articles in the category -->

<{if $tagbar}>
    <p><{include file="db:tag_bar.tpl"}></p>
<{/if}>

<{include file='db:publisher_footer.tpl'}>
