<{include file='db:publisher_header.tpl'}>

<!-- if we are on the index page OR inside a category that has subcats OR (inside a category with no subcats
    AND $display_category_summary is set to TRUE), let's display the summary table ! //-->

<{if $indexpage|default:false || $category.subcats || ($category && $display_category_summary)}>

    <{* if $display_category_summary && $category}>
        <div class="well well-sm">
            <{$lang_category_summary}>
        </div>
    <{/if *}>


    <{include file='db:publisher_categories_table.tpl'}>
    <!-- End of if !$category || $category.subcats || ($category && $display_category_summary) //-->
<{/if}>

<{if $items}>
<div class="container">
    <h4 class="pub_last_articles_full"><span class="fa fa-newspaper-o"></span>&nbsp;<{$lang_items_title}></h4>
    <div class="row mb-3">
        <{foreach item=item from=$items}>
        <div class="card col-12 col-md-6 mt-2">
			<{if $display_mainimage == 1}>					
                    <{if $item.image_path!=''}>
`                   <a href="<{$item.itemurl}>" title="<{$item.title}>">
                    <img class="card-img-top" src="<{$item.image_path}>" alt="<{$item.title}>"></a>		            
					<{else}>
					<a href="<{$item.itemurl}>">
					<img class="card-img-top" src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>"/></a> 
					<{/if}>
			<{/if}>
			
			
            <div class="card-body">
                <h5 class="card-title"><{$item.titlelink}></h5>
                <{if $show_subtitle && $item.subtitle}>
                <p class="text-muted"><{$item.subtitle}></p>
                <{/if}> 
				<p class="card-text">
                <{if $display_poster == 1}>
                <small class="text-muted"><{$item.who}> </small>
                <{/if}>
				 <{if $display_poster == 1}>
                <small class="text-muted"><{$item.when}> </small>
                <{/if}>
				 <{if $display_poster == 1}>
               <small class="text-muted">(<{$item.counter}> <{$smarty.const._MD_PUBLISHER_READS}>)</small>
                <{/if}>
				</p>
				<{if $display_summary == 1}>
                <{if $indexpage|default:false}>
                <p class="card-text"><{$item.summary}></p>
                <{else}>
                <p class="card-text"><{$item.summary|truncateHtml:80}></p>
                <{/if}>
				<{/if}>
				
				<{if $display_category == 1}> 
					   <span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                       &nbsp;&nbsp;<span class="fa fa-tag"></span>&nbsp;<{$item.category}>
                       </span>
			    <{/if}> 
				<{if $display_commentlink == 1 && $item.cancomment && $item.comments != -1}>
					   <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
				       &nbsp;&nbsp;<span class="fa fa-comment"></span>&nbsp;<{$item.comments}>
				       </span>
			    <{/if}>
				
                <{if $display_readmore == 1}>
                <div class="pull-right">
                    <a href="<{$item.itemurl}>" class="btn btn-primary btn-sm"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
                </div>
				<{/if}>
            </div>
        </div>
        <{/foreach}>
    </div>
    <{if $navbar|default:false}>
    <div class="generic-pagination col text-right mt-2">
        <{$navbar|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}>
    </div>
    <{/if}>
</div>
<{/if}>

<{include file='db:publisher_footer.tpl'}>
