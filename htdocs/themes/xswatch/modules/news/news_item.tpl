<section role="main" class="news-item">
    <article role="article">
        <{if $story.picture != ""}>
            <div class="news-header">
                <{if $story.poster != ""}><em><strong><{$lang_postedby}>: </strong><{$story.poster}><{/if}> <{$lang_on}> <{$story.posttime}></em>

                <strong class="pull-right hit-counter"><{$story.hits}> <{$lang_reads}></strong>

                <h2 class="news-title" role="heading"><{$story.topic_title}> <{$story.news_title}></h2>
                <a title="<{$story.news_title|strip_tags}>" data-toggle="modal" data-target="#myModal" href="#myModal">
                    <img src="<{$story.picture}>" alt="<{$story.news_title|strip_tags}>">
                </a>
            </div>
        <{else}>
            <h2 role="heading" class="news-no-image"><{$story.topic_title}>: <{$story.news_title}></h2>
            <{if $story.poster != ""}>
                <em><strong><{$lang_postedby}>: </strong><{$story.poster}><{/if}><{$lang_on}> <{$story.posttime}></em>
            <strong class="pull-right hit-counter"><{$story.hits}> <{$lang_reads}></strong>
        <{/if}>
        <{if $story.files_attached}>
            <{$story.attached_link}>
        <{/if}>
        <{$story.text}>
    </article>
    <{$story.morelink}>
</section>

<!-- Modal Image -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><{$story.news_title}></h4>
            </div>
            <div class="modal-body">
                <img src="<{$story.picture}>" alt="<{$story.news_title|strip_tags}>" class="img-responsive">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div><!-- .modal -->
