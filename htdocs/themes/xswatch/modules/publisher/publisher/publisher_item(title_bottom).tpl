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

			
             <{if $item.image_path==''}>
			  <{if $display_defaultimage}>
			  <img class="img-responsive" src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>" title="<{$item.title}>"/>
		      <{/if}>  
			<{/if}>
    <{if $item.image_path || $item.images}>
        <figure>
            <{if $item.images}>
                <div id="articleslider" class="owl-carousel owl-theme" style="margin-bottom:10px;">
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
                <img style="margin-bottom:15px;" class="img-responsive" src="<{$item.image_path}>" alt="<{$item.image_name}>"/>
            <{/if}>
        </figure>
    <{/if}>
    <div class="pub_article_t_top clearfix">
    <header>
        <h2><{$item.title}>    
			<div class="pub_article_t_bottom_info">
            <{if $display_itemcategory}>
                <span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.category}>
                </span>
            <{/if}>
           <{if $display_who_link}>
                <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-user"></span>&nbsp;<{$item.who}>
                </span>
            <{/if}>
           <{if $display_when_link}>
                <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$item.when}>
                </span>
            <{/if}>
            <{if $display_comment_link && $item.cancomment && $item.comments != -1}>
                <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comments}>
                </span>
            <{/if}>
            <{if $display_hits_link}>
                <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$item.counter}> <{$smarty.const._MD_PUBLISHER_READS}>
                </span>
             <{/if}>
			</div> 
			 </h2>
            <{if $show_subtitle && $item.subtitle}>
            <h5><{$item.subtitle}></h5>
        <{/if}>
    </header>

	
	
        <{$item.maintext}>

        <div class='shareaholic-canvas' data-app='share_buttons' data-app-id=''></div>
    </div>

</div>
<{if $pagenav}>
    <div class="pub_pagenav text-right">
        <{$pagenav}>
    </div>
<{/if}>
<div class="clearfix"></div>
<{if $tagbar}>
    <p><{include file="db:tag_bar.tpl"}></p>
<{/if}>


<div class="pub_article_extras">
    <{if $rating_enabled}>
        <div class="pull-left">
            <small><{$item.ratingbar}></small>
        </div>
    <{/if}>
    <div class="pull-right text-right">
  <{if $display_print_link}>
     <{$item.printlink}> 
  <{/if}>
  <{if $display_pdf_button}>
     <{$item.pdfbutton}>
   <{/if}>
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
    <table class="table table-hover table-condensed" style="margin: 15px 0;">
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
                        <img src="<{$publisher_url}>/assets/images/links/file.png" title="<{$lang_download_file}>"
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
    <{if $previousItemLink || $nextItemLink}>
        <{if $previousItemLink}>
            <div class="pull-left">
                <a href="<{$previousItemUrl}>">
                    <img style="vertical-align: middle;" src="<{$publisherImagesUrl}>/links/previous.gif" title="<{$smarty.const._MD_PUBLISHER_PREVIOUS_ITEM}>"
                         alt="<{$smarty.const._MD_PUBLISHER_PREVIOUS_ITEM}>"/>
                </a>
                <{$previousItemLink}>
            </div>
        <{/if}>
        <{if $nextItemLink}>
            <div class="text-right">
                <{$nextItemLink}>
                <a href="<{$nextItemUrl}>">
                    <img style="vertical-align: middle;" src="<{$publisherImagesUrl}>/links/next.gif" title="<{$smarty.const._MD_PUBLISHER_NEXT_ITEM}>"
                         alt="<{$smarty.const._MD_PUBLISHER_NEXT_ITEM}>"/>
                </a>
            </div>
        <{/if}>
    <{/if}>
<{elseif $other_items == 'all'}>
    <table class="table table-hover table-condensed" style="margin: 15px 0;">
        <thead>
        <tr>
            <th><{$smarty.const._MD_PUBLISHER_OTHER_ITEMS}> </th>
            <{if $show_date_col == 1}>
                <th style="text-align: center;"><{$smarty.const._MD_PUBLISHER_DATESUB}></th>
            <{/if}>
            <{if $show_hits_col == 1}>
                <th style="text-align: center;"><{$smarty.const._MD_PUBLISHER_HITS}></th>
            <{/if}>
        </tr>
        </thead>
        <tbody>
        <!-- Start item loop -->
        <{foreach item=item from=$items}>
            <tr>
                <td class="even" align="left">
                <{if $show_mainimage == 1}>					
		         <{if $item.item_image==''}>
		          <a href="<{$item.itemurl}>"><img src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>" title="<{$item.title}>" align="left" width="100" style="padding:5px"/>
		          <{else}>
				   <a href="<{$item.itemurl}>"><img src="<{$item.item_image}>" alt="<{$item.title}>" align="left" width="100" style="padding:5px" /></a> 
                <{/if}> 
				<{/if}>
                <{$item.titlelink}>
	               <{if $show_summary == 1}>
                       <br><{$item.summary}><br />
                    <{/if}> 
					<{if $show_readmore == 1}>
					     <div class="pull-right">
                        <a href="<{$item.itemurl}>" class="btn btn-primary btn-sm"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
                        </div>
                     <{/if}>
					 <{if $display_category == 1}> 
					   <span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                       &nbsp;&nbsp;<span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.category}>
                       </span>
					<{/if}>
					 <{if $show_poster == 1}>
                       <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
				       &nbsp;&nbsp;<span class="glyphicon glyphicon-user"></span>&nbsp;<{$item.who}>
				       </span>
                    <{/if}>
					<{if $show_commentlink == 1 && $item.cancomment && $item.comments != -1}>
                       <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
				       &nbsp;&nbsp;<span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comments}>
				       </span>    
                    <{/if}>
                    </td>
                <{if $show_date_col == 1}>
                    <td style="text-align: center;"><{$item.datesub}></td>
                <{/if}>
                <{if $show_hits_col == 1}>
                    <td style="text-align: center;"><{$item.counter}></td>
                <{/if}>
                <{if $show_poster == 1}>
                    <td style="text-align: center;"><{$item.who}></td>
                <{/if}>
            </tr>
        <{/foreach}>
        <!-- End item loop -->
        </tbody>
    </table>
<{/if}>
<!-- END Other articles in the category -->



<{include file='db:publisher_footer.tpl'}>
