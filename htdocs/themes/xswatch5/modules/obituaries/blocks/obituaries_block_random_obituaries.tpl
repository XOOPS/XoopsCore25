<{if !empty($block.obituaries_random_users)}>
    <div class="row">
        <{foreach item=user from=$block.obituaries_random_users|default:null}>
        <div class="card col-8 col-sm-6 col-md-4 col-xl-3">
            <{if $block.obituaries_display_picture == 1 && $user.obituaries_picture_url != ''}>
            <img class="card-img-top" title="<{$user.obituaries_href_title}>"
                src="<{$user.obituaries_picture_url}>" alt="<{$user.obituaries_href_title}>"
                 width="<{$block.obituaries_picture_width}>">
            <{elseif $block.obituaries_display_picture == 1}>
                <img class="card-img-top" title="<{$user.obituaries_href_title}>"
                    src="<{$xoops_url}>/modules/obituaries/assets/images/nophoto.jpg" alt="<{$user.obituaries_href_title}>"
                    width="<{$block.obituaries_picture_width}>">
            <{/if}>
            <div class="card-body">
                <p class="card-title">
                    <a href="<{$smarty.const.OBITUARIES_URL}>user.php?obituaries_id=<{$user.obituaries_id}>"
                       class="stretched-link"
                       title="<{$user.obituaries_href_title}>"><{$user.obituaries_fullname}></a>
                </p>
            </div>
        </div>
        <{/foreach}>
    </div>
<{else}>
    <{$smarty.const._MB_BD_NOOBITUARIES}>
<{/if}>
