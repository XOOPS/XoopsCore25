<{if !empty($block.obituaries_today_users)}>
    <div class="row">
        <{foreach item=user from=$block.obituaries_today_users|default:null}>
            <div class="card col-8 col-sm-6 col-md-4 col-xl-3">
                    <{if $block.obituaries_display_picture == 1 && $user.obituaries_picture_url != ''}>
                            <img class="card-img-top" src="<{$user.obituaries_picture_url}>" title="<{$user.obituaries_href_title}>" alt="<{$user.obituaries_href_title}>" width="<{$block.obituaries_picture_width}>">
                        </a>
                    <{elseif $block.obituaries_display_picture == 1}>
                            <img class="card-img-top" src="<{$xoops_url}>/modules/obituaries/assets/images/nophoto.jpg"
                                 alt="<{$user.obituaries_href_title}>" title="<{$user.obituaries_href_title}>" width="<{$block.obituaries_picture_width}>">
                        </a>
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
    <{if $block.obituaries_today_more }>
        <div align="center"><a
                    href="<{$smarty.const.OBITUARIES_URL}>index.php?op=today"><{$smarty.const._MB_OBITUARIES_SHOW_MORE}></a>
        </div>
    <{/if}>
<{else}>
    <{$smarty.const._MB_OBITUARIES_NOOBITUARIES}>
<{/if}>
