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
<h4 class="pub_last_articles_list"><span class="fa fa-newspaper-o"></span>&nbsp;<{$lang_items_title}></h4>
<div class="publisher_items_list_">
    <{if $items}>
    <{foreach item=item from=$items}>
        <div class="article_list">
		<{if $display_mainimage == 1}>	
            <{if $item.image_path}>
                <div class="article_list_img">
                    <a href="<{$item.itemurl}>" title="<{$item.title}>">
                        <img src="<{$item.image_path}>" alt="<{$item.title}>"/>
                    </a>
                </div>
				<{else}>
                      <div class="article_list_img">
				       <a href="<{$item.itemurl}>" title="<{$item.title}>">
					   <img class="img-fluid" src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>">
					   </a>     
                   </div>	
            <{/if}>
		<{/if}>
            <div class="article_list_summary">
                <div class="article_list_title">
                    <h3><{$item.titlelink}></h3>
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
                </div>
				<{if $display_summary == 1}>
                <{if $indexpage|default:false}>
                <p><{$item.summary}></p>
                <{else}>
                <p><{$item.summary|truncateHtml:80}></p>
                <{/if}>
				<{/if}>

            </div>
			
			  <{if $display_readmore == 1}>
                <div class="pull-right">
                    <a href="<{$item.itemurl}>" class="btn btn-primary btn-sm"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
                </div>
			   <{/if}>
            <div class="clearfix"></div>
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
