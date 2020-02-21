<{include file='db:publisher_header.tpl'}>

<{if $indexpage || $category.subcats || ($category && $display_category_summary)}>

    <{if $display_category_summary && $category}>
        <div class="well well-sm">
            <{$lang_category_summary}>
        </div>
    <{/if}>

    <{include file='db:publisher_categories_table.tpl'}>
    <!-- End of if !$category || $category.subcats || ($category && $display_category_summary) //-->
<{/if}>
<h4 class="pub_last_articles_wf">
    <span class="fa fa-newspaper-o"></span>&nbsp;<{$lang_items_title}>
</h4>
<div class="publisher_items_list_">
    <{if $items}>
    <{foreach item=item from=$items}>
        <div class="article_wf">
            <div class="article_wf_title">
                <h3><{$item.titlelink}></h3>
            </div>
			<{if $display_mainimage == 1}>	
				 <{if $item.image_path!=''}>
            <div class="article_wf_img">
             <a href="<{$item.itemurl}>"><img class="img-fluid" src="<{$item.image_path}>" alt="<{$item.title}>" title="<{$item.title}>"></a>
            </div>
            <{else}>
             <div class="article_wf_img">
				<a href="<{$item.itemurl}>"><img class="img-fluid" src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>" title="<{$item.title}>"></a>     
           </div>	
			<{/if}>
            <{/if}>
			
			
			<{if $display_summary == 1}>
            <div class="article_wf_summary">
                <span style="font-weight: normal;">
                <{$item.summary}>
                    </span>
            </div>
			<{/if}>
			<{if $display_readmore == 1}>
            <div class="pull-right" style="margin-top: 15px;">
                <a href="<{$item.itemurl}>"
                   class="btn btn-primary btn-xs"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
            </div>
			<{/if}> 
            <div class="clearfix"></div>
				        <small class="text-muted">
			        <{if $display_category == 1}>
					    <span>
					    <span class="fa fa-tag"></span>&nbsp;<{$item.category}> 
					    </span>
					 <{/if}> 
					 <{if $display_poster == 1}> 
					     <span>
					     <span class="fa fa-user"></span>&nbsp;<{$item.who}>
                         </span>					
					 <{/if}> 
					 <{if $display_date_col == 1}> 
					     <span>
					     <span class="fa fa-calendar"></span>&nbsp; <{$item.datesub}> 
					     </span>
					 <{/if}> 
					 <{if $display_hits_col == 1}> 
					     <span>
					     <span class="fa fa-check-circle-o"></span>&nbsp; <{$item.counter}>  
					     </span>
					 <{/if}> 
                     <{if $display_commentlink == 1 && $item.cancomment && $item.comments != -1}> 
					     <span>
					     <span class="fa fa-comment"></span>&nbsp;<{$item.comments}>
					     </span>
					 <{/if}>
					 </small>
    
        </div>
		<{/foreach}>
</div>

    <div class="generic-pagination col text-right mt-2">
        <{$navbar|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}>
    </div>

<{$press_room_footer}>


<{/if}>
<!-- end of if $items -->

<{include file='db:publisher_footer.tpl'}>
