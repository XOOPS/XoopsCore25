<{if $block.currentcat}>
    <span class="label label-success" style="margin-right: 3px;">
        <{$block.currentcat}>
    </span>
<{/if}>

<{foreach item=category from=$block.categories|default:null}>
    <span class="label label-primary label-default" style="margin-right: 3px;"><{$category.categoryLink}></span>
    <{if $category.items}>
        <{foreach item=item from=$category.items|default:null}>
            <span class="label label-primary label-default" style="margin-right: 3px;"><{$item.titleLink}></span>
        <{/foreach}>
    <{/if}>
<{/foreach}>
