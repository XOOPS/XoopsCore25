<{include file='db:publisher_header.tpl'}>

<!-- if we are on the index page OR inside a category that has subcats OR
 (inside a category with no subcats AND $display_category_summary is set to TRUE),
 let's display the summary table ! //-->
<{if $indexpage || $category.subcats || ($category && $display_category_summary)}>

    <!-- let's begin the display of the other display type -->
    <{if $collapsable_heading == 1}>
        <div class="publisher_collaps_title">
            <a href='javascript:' onclick="toggle('toptable'); toggleIcon('toptableicon')"><img id='toptableicon' src='<{$publisher_url}>/assets/images/links/close12.gif'
                                                                                                alt=''></a>&nbsp;<{$lang_category_summary}>
        </div>
        <div id='toptable'>
            <span class='publisher_collaps_info'><{$lang_category_summary}></span>
        <!-- Content under the collapsable bar //-->
    <{/if}>

    <{include file='db:publisher_categories_table.tpl'}>

    <{if $collapsable_heading == 1}>
        </div>
    <{/if}>
    <br>
    <!-- End of if !$category || $category.subcats || ($category && $display_category_summary) //-->
<{/if}>
<{if $items}>
    <{if $collapsable_heading == 1}>
        <div class="publisher_collaps_title">
            <a href='javascript:' onclick="toggle('bottomtable'); toggleIcon('bottomtableicon')">
                <img id='bottomtableicon' src='<{$publisher_url}>/assets/images/links/close12.gif' alt=''>
            </a>&nbsp;<{$lang_items_title}>
        </div>
        <div id='bottomtable'>
            <span class="publisher_collaps_info"><{$smarty.const._MD_PUBLISHER_ITEMS_INFO}> </span>
    <{/if}>
    <div align="right"><{$navbar}></div>
    <table class="table table-hover">
        <tr>
            <td align="left" class="itemHead" width='60%'><strong><{$smarty.const._CO_PUBLISHER_TITLE}></strong></td>
            <{if $display_date_col == 1}>
                <td align="center" class="itemHead" width="30%"><strong><{$smarty.const._MD_PUBLISHER_DATESUB}></strong></td>
            <{/if}> <{if $display_hits_col == 1}>
                <td align="center" class="itemHead" width="10%"><strong><{$smarty.const._MD_PUBLISHER_HITS}></strong></td>
            <{/if}>
        </tr>
        <!-- Start item loop -->
        <{foreach item=item from=$items}>
            <tr>
                <td class="even" align="left">
                    <{if $display_mainimage == 1}>					
                    <{if $item.image_path!=''}>
		            <a href="<{$item.itemurl}>"><img src="<{$item.image_path}>" alt="<{$item.title}>" align="left" width="100" style="padding:5px"/></a> 
		           <{else}>
					<a href="<{$item.itemurl}>"><img src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>" align="left" width="100"style="padding:5px"/></a> 
					<{/if}>
					<{/if}>
					<strong><{$item.titlelink}></strong>
                    <{if $show_subtitle && $item.subtitle}>
                        <br>
                        <em><{$item.subtitle}></em>
                    <{/if}>
					
					
					<br />
					<{if $display_summary == 1}><{$item.summary}><br /><{/if}> 
					<{if $display_readmore == 1}>
					 <div class="pull-right">
                    <a href="<{$item.itemurl}>" class="btn btn-primary btn-sm"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
                     </div><{/if}>
					<small>
					<{if $display_category == 1}> 
					   <span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                       &nbsp;&nbsp;<span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.category}>
                       </span>
					<{/if}>  
					<{if $display_poster == 1}>
                       <span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                       &nbsp;&nbsp;<span class="glyphicon glyphicon-user"></span>&nbsp;<{$item.who}>
                       </span>
                    <{/if}>
					<{if $display_commentlink == 1 && $item.cancomment && $item.comments != -1}>
					   <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
				       &nbsp;&nbsp;<span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comments}>
				       </span>
					<{/if}>
					</small>
					
                </td>
                <{if $display_date_col == 1}>
                    <td class="odd" align="left">
                        <div align="center"><{$item.datesub}> </div>
                    </td>
                <{/if}>
                <{if $display_hits_col == 1}>
                    <td class="odd" align="left">
                        <div align="center"><{$item.counter}></div>
                    </td>
                <{/if}>
            </tr>
        <{/foreach}> <!-- End item loop -->
        <tr></tr>
    </table>
    <div align="right"><{$navbar}></div>
    <{if $collapsable_heading == 1}>
        </div>
    <{/if}>
<{/if}><!-- end of if $items -->

<{include file='db:publisher_footer.tpl'}>
