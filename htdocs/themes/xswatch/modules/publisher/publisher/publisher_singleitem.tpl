            <div class="article_full">
            <{if $display_category == 1}>
               <div class="article_full_category">
                <{$item.category}>
               </div>
            <{/if}>
		
		<{if $display_mainimage == 1}>	
			<{if $item.image_path!=''}>
            <div class="article_full_img_div">
             <a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$item.image_path}>" alt="<{$item.title}>"></a>
            </div>
            <{else}>
             <div class="article_full_img_div">
				<a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$publisher_url}>/assets/images/default_image.jpg" alt="<{$item.title}>"></a>     
           </div>	
			<{/if}>
            <{/if}>
			
            <div style="padding: 10px;">
                <h4><{$item.titlelink}></h4>
                    <{if $show_subtitle && $item.subtitle}>
                                <em><{$item.subtitle}><br></em>
                    <{/if}>
              <small>
               <{if $display_category == 1}>  
                   <span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.category}> 
               <{/if}>				                       
               <{if $display_poster == 1}> 
                   <span class="glyphicon glyphicon-user"></span>&nbsp;<{$item.who}>
               <{/if}> 
               <{if $display_date_col == 1}>   
                    <span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$item.datesub}>
                <{/if}> 
                <{if $display_hits_col == 1}> 
                     <span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$item.counter}> <{$smarty.const._MD_PUBLISHER_TOTALHITS}> 
               <{/if}> 
               <{if $display_commentlink == 1 && $item.cancomment && $item.comments != -1}>
                     <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comments}>
               <{/if}>
               </small>

                <{if $display_summary == 1}>
                <div style="margin-top:10px;">
                    <{$item.summary}><br /> 
                </div>
                <{/if}>
         
                <{if $display_readmore == 1}>
				<div class="pull-right" style="margin-top: 15px;">
                    <a href="<{$item.itemurl}>" class="btn btn-primary btn-xs"> <{$smarty.const._MD_PUBLISHER_VIEW_MORE}></a>
                </div>
                <{/if}>
                <div class="clearfix"></div>
            </div>
        </div>
        