<style type="text/css">
    .heyula {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .heyula li {
        width: 10%;
        float: left;
    }
</style>

<{if count($obituaries_users) > 0}>
    <ol class="breadcrumb">
        <li><a href="index.php"><{$breadcrumb}></a></li>
    </ol>
    <p align="center">
        <a href="<{$xoops_url}>/modules/obituaries/index.php"><img src="<{$xoops_url}>/modules/obituaries/images/logo.png" alt="" class="img-thumbnail"/></a>
    </p>
    <br>
    <ul class="heyula">
        <{foreach item=obituaries_user from=$obituaries_users}>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">

                    <{if trim($obituaries_user.obituaries_full_imgurl) != ''}>
                        <img src="<{$obituaries_user.obituaries_full_imgurl}>" alt="<{$obituaries_user.obituaries_href_title}>" class="img-thumbnail">
                    <{elseif trim($obituaries_user.obituaries_user_user_avatar) != ''}>
                        <img src="<{$xoops_url}>/uploads/<{$obituaries_user.obituaries_user_user_avatar}>" alt="<{$obituaries_user.obituaries_href_title}>"
                             class="img-thumbnail"/>
                    <{else}>
                        <img src="<{$xoops_url}>/modules/obituaries/images/nophoto.jpg" alt="<{$obituaries_user.obituaries_href_title}>" width="130"
                             class="img-thumbnail"/>
                    <{/if}>

                    <div class="caption">
                        <div style="text-align: center;"><h3><a href="<{$smarty.const.OBITUARIES_URL}>user.php?obituaries_id=<{$obituaries_user.obituaries_id}>"
                                                                title="<{$obituaries_user.obituaries_href_title}>"><{$obituaries_user.obituaries_fullname}></a></h3></div>
                        <div style="text-align: center;"><p><span class="glyphicon glyphicon-calendar"></span>&nbsp;<span class="label label-danger"><{$obituaries_user.obituaries_formated_date}></span>&nbsp;<span
                                        class="label label-success"><a
                                            href="<{$smarty.const.OBITUARIES_URL}>user.php?obituaries_id=<{$obituaries_user.obituaries_id}>"
                                            title="<{$obituaries_user.obituaries_href_title}>">more</a></span></p></div>
                    </div>
                </div>
                </br>
            </div>
        <{/foreach}>
    </ul>
<{else}>
    <h3><{$smarty.const._OBITUARIES_ERROR3}></h3>
<{/if}>

<{if isset($pagenav)}>
    <div align="center"><{$pagenav}></div>
<{/if}>




