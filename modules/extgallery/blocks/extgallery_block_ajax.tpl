<script>
    jQuery(document).ready(function ($) {
        $('#extgallery-carousel div.item:first-child').addClass('active');
        $('#ext-ind > li:first-child').addClass('active');
        var extgallery = $('#ext-ind > li');
        extgallery.each(
            function (index) {
                $(this).attr('data-slide-to', index);
            }
        )
    });
</script>

<div id="extgallery-carousel" class="carousel slide" data-ride="carousel">
    <ol id="ext-ind" class="carousel-indicators">
        <{foreach item=photo from=$block.photos}>
            <li data-target="#extgallery-carousel"></li>
        <{/foreach}>
    </ol>
    <div class="carousel-inner" role="listbox">
        <{foreach item=photo from=$block.photos}>
            <div class="item">
                <img src="<{$xoops_url}>/uploads/extgallery/public-photo/medium/<{$photo.photo_name}>" alt="<{$photo.photo_title}>">

                <div class="carousel-caption">
                    <h3><{$photo.photo_title}></h3>
                    <p><{$photo.photo_desc}></p>
                    <a class="btn btn-xs btn-primary" title="<{$photo.photo_title}>" href="<{$xoops_url}>/modules/extgallery/public-photo.php?photoId=<{$photo.photo_id}>">
                        <{$smarty.const._MB_EXTGALLERY_MOREINFO}>
                    </a>
                </div>
            </div>
        <{/foreach}>
        <a class="left carousel-control" href="#extgallery-carousel" data-slide="prev"><span class="icon-prev"></span></a>
        <a data-slide="next" href="#extgallery-carousel" class="right carousel-control"><span class="icon-next"></span></a>
    </div>
</div>
