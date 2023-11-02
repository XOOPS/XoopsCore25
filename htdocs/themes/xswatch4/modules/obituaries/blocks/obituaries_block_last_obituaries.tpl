<{if !empty($block.obituaries_last_users)}>
<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner">
        <{assign var=active value=' active'}>
        <{foreach item=user from=$block.obituaries_last_users|default:null}>
        <div class="carousel-item<{$active}>">
            <{if !empty($user.obituaries_picture_url)}>
            <img src="<{$user.obituaries_picture_url}>" alt="<{$user.obituaries_href_title}>">
            <{else}>
            <img src="<{$xoops_url}>/modules/obituaries/assets/images/nophoto.jpg" alt="<{$user.obituaries_href_title}>">
            <{/if}>
            <div class="carousel-caption">
                <a class="btn stretched-link text-white bg-dark" href="<{$smarty.const.OBITUARIES_URL}>user.php?obituaries_id=<{$user.obituaries_id}>"><{$user.obituaries_fullname}></a>
            </div>
        </div>
        <{assign var=active value=''}>
        <{/foreach}>
    </div>
</div>
<{else}>
    <{$smarty.const._MB_BD_NOOBITUARIES}>
<{/if}>
