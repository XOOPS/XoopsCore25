<div class="news-home">
    <{if $topic_rssfeed_link|default:'' != ''}>
        <{$topic_rssfeed_link}>
    <{/if}>

    <{if $displaynav == true}>
        <div class="text-center">
            <form name="form1" action="<{$xoops_url}>/modules/news/index.php" method="get">
                <{$topic_select}> <select name="storynum"><{$storynum_options}></select> <input type="submit" value="<{$lang_go}>">
            </form>
        </div>
    <{/if}>

    <{if $topic_description|default:'' != ''}>
        <{$topic_description}>
    <{/if}>

    <div id="xoopsgrid" class="row">
        <{section name=i loop=$columns}>
            <{foreach item=story from=$columns[i]|default:null}>
                <div class="col-xs-12 col-md-6 home-news-loop">
                    <{if $story.picture|default:'' != ''}>
                        <div class="home-thumbnails">
                            <img src="<{$story.picture}>" alt="<{$story.pictureinfo}>" class="img-fluid">
                        </div>
                        <!-- .home-thumbnails -->
                    <{else}>
                        <div class="home-thumbnails">
                            <img src="<{$xoops_imageurl}>images/tdm-no-image.jpg" alt="" class="img-fluid">
                        </div>
                        <!-- .home-thumbnails -->
                    <{/if}>
                    <h3 class="xoops-default-title"><{$story.news_title|strip_tags}></h3>

                    <div class="excerpt-news"><{$story.text}></div>
                    <{$story.morelink}>
                </div>
            <{/foreach}>
        <{/section}>
    </div>

    <div class="text-center generic-pagination">
        <{$pagenav|default:''}>
    </div>

</div>

<{include file='db:system_notification_select.tpl'}>
