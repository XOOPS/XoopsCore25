<div>
    <ul class="list-unstyled">
        <{foreach item=photo from=$block.photos}>
            <li class="mb10 text-center">
                <a class="btn btn-primary btn-block btn-xs" title="<{$photo.photo_title}>" href="<{$xoops_url}>/modules/extgallery/public-photo.php?photoId=<{$photo.photo_id}>"><{$photo.photo_title}></a>
                <{if $block.hits}><span class="label label-info"><{$photo.photo_hits}></span> <{/if}>
                <{if $block.date}><span class="label label-info"><{$photo.photo_date}></span><{/if}>
                <{if $block.rate}><span class="label label-info"><{$photo.photo_rating}></span><{/if}>
            </li>
        <{/foreach}>
    </ul>
</div>
