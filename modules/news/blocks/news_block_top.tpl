<ul class="list-unstyled">
    <{foreach item=news from=$block.stories}>
        <li>
            <{if $block.sort=='counter'}>
                [<{$news.hits}>]
            <{elseif $block.sort=='published'}>
                [<{$news.date}>]
            <{else}>
                [<{$news.rating}>]
            <{/if}>
            <a title="<{$news.title}>" href="<{$xoops_url}>/modules/news/article.php?storyid=<{$news.id}>"><{$news.title}></a>
        </li>
    <{/foreach}>
</ul>
