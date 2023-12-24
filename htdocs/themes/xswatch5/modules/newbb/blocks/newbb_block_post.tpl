<table class="table table-hover">
    <{if isset($block.disp_mode) &&  $block.disp_mode == 0}>
        <tr>
            <th class="head"><{$smarty.const._MB_NEWBB_FORUM}></th>
            <th class="head"><{$smarty.const._MB_NEWBB_TITLE}></th>
            <th class="head"><{$smarty.const._MB_NEWBB_AUTHOR}></th>
        </tr>
        <{foreach item=topic from=$block.topics|default:null}>
        <tr>
            <td><a href="<{$topic.seo_forum_url}>"><{$topic.forum_name}></a></td>
            <td><a href="<{$topic.seo_url}>"><{$topic.title}></a></td>
            <td><{$topic.time}><br><{$topic.topic_poster}></td>
        </tr>
    <{/foreach}>

    <{elseif $block.disp_mode == 1}>
        <tr>
            <th class="head"><{$smarty.const._MB_NEWBB_TOPIC}></th>
            <th class="head"><{$smarty.const._MB_NEWBB_AUTHOR}></th>
        </tr>
        <{foreach item=topic from=$block.topics|default:null}>
        <tr>
            <td><a href="<{$topic.seo_url}>"><{$topic.title}></a></td>
            <td><{$topic.topic_poster}> <{$topic.time}></td>
        </tr>
    <{/foreach}>

    <{elseif $block.disp_mode == 2}>

        <{foreach item=topic from=$block.topics|default:null}>
        <tr>
            <td><a href="<{$topic.seo_url}>"><{$topic.title}></a></td>
        </tr>
    <{/foreach}>

    <{else}>
                <{foreach item=topic from=$block.topics|default:null}>
        <tr class="d-flex">
            <td class="col-4">
                <strong><a href="<{$topic.seo_url}>"><{$topic.title}></a></strong>
                <br><a href="<{$topic.seo_forum_url}>"><{$topic.forum_name}></a>
                <br><{$topic.topic_poster}> | <{$topic.time}>
            </td>
            <td class="col-7">
                <div><{$topic.post_text|truncateHtml:40:'...'}></div>
            </td>
        </tr>
                <{/foreach}>
    <{/if}>

</table>

<{if !empty($block.indexNav)}>
    <div class="pagenav">
        <a class="btn btn-secondary" href="<{$block.seo_top_allposts}>"><{$smarty.const._MB_NEWBB_ALLPOSTS}></a>
        <a class="btn btn-secondary" href="<{$block.seo_top_allforums}>"><{$smarty.const._MB_NEWBB_VSTFRMS}></a>
    </div>
<{/if}>
