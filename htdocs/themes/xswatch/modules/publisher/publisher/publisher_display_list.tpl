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

<h4 class="pub_last_articles_list"><span class="glyphicon glyphicon-chevron-right"></span>&nbsp;<{$lang_items_title}></h4>
<div class="publisher_items_list_">
    <{if $items}>
    <{foreach item=item from=$items}>
        <div class="article_list">
		    <{if $display_mainimage == 1}>	
				 <{if $item.image_path!=''}>
                      <div class="article_list_img">
                        <a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$item.image_path}>" alt="<{$item.title}>"></a>
                      </div>
                  <{else}>
                      <div class="article_list_img">
				       <a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>"></a>     
                   </div>	
			     <{/if}>
            <{/if}>
			
            <div class="article_list_summary">
                <div class="article_list_title">
                    <h3><{$item.titlelink}></h3>
					<{if $show_subtitle && $item.subtitle}>
                                <em><{$item.subtitle}><br></em>
                    <{/if}>

					 <small>  
					 <{if $display_category == 1}>
					    <span>
					    <span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.category}> 
					    </span>
					 <{/if}> 
					 <{if $display_poster == 1}> 
					     <span>
					     <span class="glyphicon glyphicon-user"></span>&nbsp;<{$item.who}>
                         </span>					
					 <{/if}> 
					 <{if $display_date_col == 1}> 
					     <span>
					     <span class="glyphicon glyphicon-calendar"></span>&nbsp; <{$item.datesub}> 
					     </span>
					 <{/if}> 
					 <{if $display_hits_col == 1}> 
					     <span>
					     <span class="glyphicon glyphicon-ok-circle"></span>&nbsp; <{$item.counter}>  
					     </span>
					 <{/if}> 
                     <{if $display_commentlink == 1 && $item.cancomment && $item.comments != -1}> 
					     <span>
					     <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comments}>
					     </span>
					 <{/if}>
                      </small>
					
                </div>
				<{if $display_summary == 1}>
                   <div style="margin-top:10px;">
                    <{$item.summary}><br /> 
                   </div>
                <{/if}>
						
				<{if $display_readmore == 1}>
					<a href="<{$item.more}>"><{$smarty.const._MD_PUBLISHER_READMORE}></a><br />
                <{/if}>

            </div>
            <div class="clearfix"></div>
        </div>
    <{/foreach}>
</div>

    <div align="right"><{$navbar}></div>

<{$press_room_footer}>


<{/if}>
<!-- end of if $items -->

<{include file='db:publisher_footer.tpl'}>
