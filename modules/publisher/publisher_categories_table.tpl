<{assign var=temp value=0}>
<div class="row">
<{foreach item=category from=$categories}>
    <div class="media col-12 col-md-6">
        <{if !empty($category.image_path)}>
        <{if $category.categoryurl|default:false}><a href="<{$category.categoryurl}>"><{/if}>
        <img class="mr-3 mb-2 xswatch-media-img" src="<{$category.image_path}>" alt="<{$category.name}>">
        <{if $category.categoryurl|default:false}></a><{/if}>
        <{/if}>
        <div class="media-body">
            <h5 class="mt-0 mb-1"><{if $selected_category == $category.categoryid}><{$category.name}><{else}><{$category.categorylink}><{/if}></h5>
            <{$category.description}>
        <{if $category.subcats}>
        <br>
        <{foreach item=subcat from=$category.subcats}>
        <small><{$subcat.categorylink}> &nbsp;</small>
        <{/foreach}>
        <{/if}>
        </div>
    </div>
<{/foreach}>
</div>
    <{if $catnavbar|default:false}>
    <div class=""row">
        <div class="generic-pagination col text-right mt-2">
        <{$catnavbar|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}>
        </div>
    </div>
    <{/if}>
<hr>
