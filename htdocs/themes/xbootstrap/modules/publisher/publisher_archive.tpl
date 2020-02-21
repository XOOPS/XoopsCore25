<div class="table-responsive">
 <table class="table table-striped">
    <tr>
        <th><{$lang_newsarchives}></th>
    </tr>
    <{foreach item=year from=$years}>
        <tr>
            <td><b><{$year.number}></b> (<{$year.articlesYearCount}>)</td>
        </tr>
        <tr>
            <td>
                <{foreach item=month from=$year.months}>
                    <a href="./archive.php?year=<{$year.number}>&month=<{$month.number}>"><{$month.string}> (<{$month.articlesMonthCount}>) </a>
                    &nbsp;
                <{/foreach}>
            </td>
        </tr>
    <{/foreach}>
</table>
</div>
<br>

<{if $show_articles == true}>

<h4><{$lang_articles}></h4>
    <div class="container-fluid">
        <{foreach item=story from=$stories}>
		      
                <div class="row">
				<{if $showmainimage == 1}>
				<a href="<{$item.itemurl}>"><img src="<{$story.item_image}>" title="<{$story.cleantitle}>" alt="<{$story.cleantitle}>" align="left" style="padding:10px"></a><br>
				<{/if}>	
				&nbsp;&nbsp;<h4><{$story.title}></h4>
				<{if $showsummary == 1}>
				&nbsp;&nbsp;<{$story.summary}><br>
				<{/if}>
                 
				<div class="pull-left">
				<{if $showcategory == 1}>
				<span style="font-size: 11px; padding: 0; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-tag"></span>&nbsp;<{$story.category}>
                </span>
				 <{/if}>
				 <{if $showposter == 1}>
                <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-user"></span>&nbsp;<{$story.author}>
                </span>
				 <{/if}>
				<{if $showdate == 1}>
                 <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$story.date}>
                </span>
				 <{/if}>
				 <{if $showhits == 1}>
				 <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$story.counter}> <{$smarty.const._MD_PUBLISHER_READS}>
                </span>
				 <{/if}>
				 <{if $showcomment == 1 && $story.cancomment && $story.comment != -1}>
				 <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                    <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$story.comment}> 
                </span>
				 <{/if}>
				</div>
			    <div class="pull-right">

				<{if $showpdfbutton == 1}>
				    <a href="<{$story.pdf_link}>" rel="nofollow"><img src="<{$xoops_url}>/modules/<{$module_dirname}>/assets/images/links/pdf.gif" border="0" alt="<{$lang_pdf}>"></a>
                <{/if}>
				<{if $showprintlink == 1}>
					<a href="<{$story.print_link}>" rel="nofollow"><img src="<{$xoops_url}>/modules/<{$module_dirname}>/assets/images/links/print.gif" border="0" alt="<{$lang_printer}>"></a>
                <{/if}>
				<{if $showemaillink == 1}>
					<a href="<{$story.mail_link}>" target="_top"><img src="<{$xoops_url}>/modules/<{$module_dirname}>/assets/images/links/friend.gif" border="0"></a>
                <{/if}>
				</div>
				
				</div>
				<hr>
	           
        <{/foreach}>
  </div>
	
    <div><br><{$lang_storytotal}></div>
<{/if}>
