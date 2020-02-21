<{if $block.template == 'normal'}>
    <{if $block.latestnews_scroll }>
        <marquee behavior='scroll' align='center' direction='<{$block.scrolldir}>' height='<{$block.scrollheight}>' scrollamount='3' scrolldelay='<{$block.scrollspeed}>' onmouseover='this.stop()' onmouseout='this.start()'>
    <{/if}>
    <{section name=i loop=$block.columns}>
    
            <{foreach item=item from=$block.columns[i]}>
      <{if $item.topic_title}>                 
     <div class="article_full_category">
       <{$item.category}>
     </div>
     <{/if}>
	  <div class="article_full">
            <{if $item.display_item_image}>
				 <{if $item.item_image != ''}>
            <div class="article_full_img_div">
             <a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$item.item_image}>" alt="<{$item.alt}>" title="<{$item.alt}>" width="<{$block.imgwidth}>" height="<{$block.imgheight}>" style="border:<{$block.border}>px solid #<{$block.bordercolor}>"></a>
            </div>
            <{else}>
             <div class="article_full_img_div">
		    <a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$block.publisher_url}>thumb.php?src=<{$block.publisher_url}>/assets/images/default_image.jpg&w=<{$block.imgheight}>" title="<{$item.alt}>" alt="<{$item.alt}>" width="<{$block.imgwidth}>" height="<{$block.imgheight}>" style="border:<{$block.border}>px solid #<{$block.bordercolor}>"></a>    
		   </div>	
			<{/if}>
            <{/if}>
			
    <div style="padding: 10px;">
        <h4><{$item.title}></h4>
                               <{if $item.poster}>
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-user"></span>&nbsp; <{$item.poster}></span>
                                </span>
                                <{/if}>	 
                               <{if $item.posttime}>
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$item.posttime}>  
								</span> 
                                <{/if}>	
                               <{if $item.read }>
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$item.read}> <{$block.lang_reads}>
                                </span>
                                <{/if}>	
                                 <{if $item.comment && $item.cancomment && $item.comment != -1}>								
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comment}>
                                </span>	
                                <{/if}>	
                                
        <div style="margin-top:10px;">
            <{if $item.display_summary}>
			<{$item.text}>
			 <div class="pull-right">
                       <a href="<{$item.itemurl}>">
                            <{$item.more}>
                        </a>
             </div>
			<{/if}>
        </div>
        <div class="pull-left" style="margin-top: 15px;">
            <{if $op != 'preview'}>
                 
					<span style="float: right; text-align: right;">
                                <{if $item.print }>						
		                          <{$item.print}> 
                                 <{/if}>
                                <{if $item.pdf }>	
                                  <{$item.pdf}>
                                 <{/if}>
                                <{if $item.email}>
                                 <{$item.email}>
                                <{/if}>                             
                                  <{if $item.display_adminlink}> 
                                  <{$item.admin}>
                                   <{/if}> 
				    </span>				  
            <{else}>
                <span style="float: right;">&nbsp;</span>
            <{/if}>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

            <{/foreach}>
     
    <{/section}>
	 <{if $block.latestnews_scroll }></marquee><{/if}>

    <div><{$block.morelink}><{$block.topiclink}><{$block.archivelink}><{$block.submitlink}></div>
<{/if}>

<{if $block.template == 'extended'}>

<style>
@media (min-width: 768px) {
  .row.equal {
    display: flex;
    flex-wrap: wrap;
  }
}
</style>
  <{if $block.latestnews_scroll }>
        <marquee behavior='scroll' align='center' direction='<{$block.scrolldir}>' height='<{$block.scrollheight}>' scrollamount='3' scrolldelay='<{$block.scrollspeed}>' onmouseover='this.stop()' onmouseout='this.start()'>
    <{/if}>
<div class="container-fluid">
  <div class="row equal">
<{section name=i loop=$block.columns}>
    <{foreach item=item from=$block.columns[i]}>   
    <div class='col-md-4 col-sm-12'>
      <{if $item.display_item_image}>
		            <{if $item.item_image != ''}>
				   <a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$item.item_image}>" title="<{$item.alt}>" alt="<{$item.alt}>" width="<{$block.imgwidth}>" height="<{$block.imgheight}>" style="border:<{$block.border}>px solid #<{$block.bordercolor}>"></a>
				    <{else}>
					<a href="<{$item.itemurl}>"><img class="img-responsive" src="<{$block.publisher_url}>thumb.php?src=<{$block.publisher_url}>/assets/images/default_image.jpg&w=<{$block.imgheight}>" title="<{$item.alt}>" alt="<{$item.alt}>" width="<{$block.imgwidth}>" height="<{$block.imgheight}>"  style="border:<{$block.border}>px solid #<{$block.bordercolor}>></a>
	                 <{/if}>
          <{/if}>
	 <p><{$item.title}></p>
         
            <{if $block.letters != 0}>
                            <p>
                        			<{if $item.display_summary}>
									<{$item.text}> <br>
									<{/if}>
								<{$item.more}><br>
	                            <{if $item.topic_title}>
                                <span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
								<span class="glyphicon glyphicon-tag"></span>&nbsp;<{$item.topic_title}>
								</span>
                                <{/if}>	
                                <{if $item.poster}>
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-user"></span>&nbsp; <{$item.poster}></span>
                                </span>
                                <{/if}>	 
                               <{if $item.posttime}>
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$item.posttime}>  
								</span> 
                                <{/if}>	
                               <{if $item.read }>
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<{$item.read}> <{$block.lang_reads}>
                                </span>
                                <{/if}>	
                                <{if $item.comment && $item.cancomment && $item.comment != -1}>
								<span style="font-size: 11px; padding: 0 0 0 16px; margin: 0; line-height: 12px; opacity:0.8;-moz-opacity:0.8;">
                                <span class="glyphicon glyphicon-comment"></span>&nbsp;<{$item.comment}>
                                </span>	
                                <{/if}>	
                                <{if $item.print }>						
                                <{$item.print}> 
                                
                                 <{/if}>
                                <{if $item.pdf }>	
                                  <{$item.pdf}>
                                 <{/if}>
                                <{if $item.email}><{$item.email}>
                                  <{/if}>                             
                                  <{if $item.display_adminlink}><{$item.admin}>
                                  <{/if}>                     
							</p>
            <{/if}>	  
		  
    </div>
    
   <{/foreach}>  
<{/section}>
  </div>
</div>
    <{if $block.latestnews_scroll }></marquee><{/if}>

    <div><{$block.morelink}><{$block.topiclink}><{$block.archivelink}><{$block.submitlink}></div>
<{/if}>

<{if $block.template == 'ticker'}>
    <marquee behavior='scroll' align='middle' direction='<{$block.scrolldir}>' height='<{$block.scrollheight}>' scrollamount='3' scrolldelay='<{$block.scrollspeed}>' onmouseover='this.stop()'
             onmouseout='this.start()'>
        <{section name=i loop=$block.columns}>
            <div style="padding:10px;">
                <{foreach item=item from=$block.columns[i]}> &nbsp;<{$item.title}>&nbsp; <{/foreach}>
            </div>
        <{/section}>
    </marquee>
<{/if}>


<{if $block.template == 'slider1'}>

    <{php}>$GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
        $GLOBALS['xoTheme']->addStylesheet(PUBLISHER_URL . '/assets/css/jquery.popeye.css');
        $GLOBALS['xoTheme']->addStylesheet(PUBLISHER_URL . '/assets/css/jquery.popeye.style.css');
        $GLOBALS['xoTheme']->addStylesheet(PUBLISHER_URL . '/assets/css/publisher.css');
    <{/php}>
    <script type="text/javascript">
        jQuery(document).ready(function () {

            //Execute the slideShow, set 4 seconds for each images
            slideShow(5000);

        });

        function slideShow(speed) {


            //append a LI item to the UL list for displaying caption
            $('ul.pub_slideshow1').append('<LI id=pub_slideshow1-caption class=caption><DIV class=pub_slideshow1-caption-container><H3></H3><P></P></DIV></LI>');

            //Set the opacity of all images to 0
            $('ul.pub_slideshow1 li').css({opacity: 0.0});

            //Get the first image and display it (set it to full opacity)
            $('ul.pub_slideshow1 li:first').css({opacity: 1.0});

            //Get the caption of the first image from REL attribute and display it
            $('#pub_slideshow1-caption h3').tpl($('ul.pub_slideshow1 a:first').find('img').attr('title'));
//        $('#pub_slideshow1-caption').find('h3').html($('ul.pub_slideshow1 a:first').find('img').attr('title')); //suggested by PhpStorm

            $('#pub_slideshow1-caption p').html($('ul.pub_slideshow1 a:first').find('img').attr('alt'));

            //Display the caption
            $('#pub_slideshow1-caption').css({opacity: 0.7, bottom: 0});

            //Call the gallery function to run the slideshow
            var timer = setInterval('gallery()', speed);

            //pause the slideshow on mouse over
            $('ul.pub_slideshow1').hover(
                    function () {
                        clearInterval(timer);
                    },
                    function () {
                        timer = setInterval('gallery()', speed);
                    }
            );

        }

        function gallery() {


            //if no IMGs have the show class, grab the first image
            var current = ($('ul.pub_slideshow1 li.show') ? $('ul.pub_slideshow1 li.show') : $('#ul.pub_slideshow1 li:first'));

            //Get next image, if it reached the end of the slideshow, rotate it back to the first image
            var next = ((current.next().length) ? ((current.next().attr('id') == 'pub_slideshow1-caption') ? $('ul.pub_slideshow1 li:first') : current.next()) : $('ul.pub_slideshow1 li:first'));

            //Get next image caption
            var title = next.find('img').attr('title');
            var desc = next.find('img').attr('alt');

            //Set the fade in effect for the next image, show class has higher z-index
            next.css({opacity: 0.0}).addClass('show').animate({opacity: 1.0}, 1000);

            //Hide the caption first, and then set and display the caption
            $('#pub_slideshow1-caption').animate({bottom: -70}, 300, function () {
                //Display the content
                $('#pub_slideshow1-caption h3')._createTrPlaceholder(title);
                $('#pub_slideshow1-caption p').tpl(desc);
                $('#pub_slideshow1-caption').animate({bottom: 0}, 500);
            });

            //Hide the current image
            current.animate({opacity: 0.0}, 1000).removeClass('show');

        }
    </script>
    <{section name=i loop=$block.columns}>

        <ul class="pub_slideshow1">
        <{foreach item=item from=$block.columns[i]}>
            <li>
                <a href="<{$item.itemurl}>"><img src="<{$item.item_image}>" width="100%" height="<{$block.imgheight}>" title="<{$item.alt}>" alt="<{$item.text}>"></a>
            </li>
        <{/foreach}>
        </ul><{/section}>

<{/if}>

<{if $block.template == 'slider2'}>

    <{php}>$GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
        $GLOBALS['xoTheme']->addStylesheet(PUBLISHER_URL . '/assets/css/jquery.popeye.css');
        $GLOBALS['xoTheme']->addStylesheet(PUBLISHER_URL . '/assets/css/jquery.popeye.style.css');
        $GLOBALS['xoTheme']->addStylesheet(PUBLISHER_URL . '/assets/css/publisher.css');
        $GLOBALS['xoTheme']->addScript(PUBLISHER_URL . '/assets/js/jquery.easing.js');
        $GLOBALS['xoTheme']->addScript(PUBLISHER_URL . '/assets/js/script.easing.js');<{/php}>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('#lofslidecontent45').lofJSidernews({
                interval: 4000,
                direction: 'opacity',
                duration: 1000,
                easing: 'easeInOutSine'
            });
        });

    </script>
   <{section name=i loop=$block.columns}>
        <div id="lofslidecontent45" class="lof-slidecontent">

            <div class="lof-main-outer">
                <ul class="lof-main-wapper">
                    <{foreach item=item from=$block.columns[i]}>
                        <li>
                            <img src="<{$item.item_image}>" alt="<{$item.alt}>" width="<{$block.imgwidth}>" height="<{$block.imgheight}>">
                        </li>
                    <{/foreach}>
                </ul>
            </div>

            <div class="lof-navigator-outer">
                <ul class="lof-navigator">
                    <{foreach item=item from=$block.columns[i]}>
                        <li>
                            <div>
                                <img src="<{$item.item_image}>" alt="" width="60" height="60">

                                <h3><a href="<{$item.itemurl}>"> <{$item.alt}> </a></h3>
                            </div>
                        </li>
                    <{/foreach}>
                </ul>
            </div>
        </div>
        <script type="text/javascript">

        </script>
    <{/section}>

<{/if}>
