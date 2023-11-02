<{if isset($obituaries_user)}>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/obituaries/index.php">Obituaries</a></li>
            <li class="breadcrumb-item active" aria-current="page"><{$obituaries_user.obituaries_lastname}>, <{$obituaries_user.obituaries_firstname}></li>
            <{if isset($xoops_isadmin)}>
            <a title="<{$smarty.const._EDIT}>" class="ml-2" href="<{$xoops_url}>/modules/obituaries/admin/main.php?op=edit&id=<{$obituaries_user.obituaries_id}>"><span class="fa fa-edit"></span></a>
            <{/if}>
        </ol>
    </nav>

    <div>
    <div class="row">
        <div class="col">
        <{if trim($obituaries_user.obituaries_full_imgurl) != ''}>
            <div>
                <img src="<{$obituaries_user.obituaries_full_imgurl}>" alt="<{$obituaries_user.obituaries_href_title}>" class="img-thumbnail">
            </div>
        <{else}>
            <div>
                <img src="<{$xoops_url}>/modules/obituaries/assets/images/nophoto.jpg" alt="<{$obituaries_user.obituaries_href_title}>" width="130"/>
            </div>
        <{/if}>
        </div>
        <div class="col">
        <h3><{$obituaries_user.obituaries_lastname}>, <{$obituaries_user.obituaries_firstname}></h3>
            <p><strong><{$smarty.const._AM_OBITUARIES_DATE}>: </strong> <{$obituaries_user.obituaries_formated_date}></p>
        </div>
    </div>
        <div class="mt-2 alert alert-success"><b><{$smarty.const._AM_OBITUARIES_DESCRIPTION}></b> :</div>
        <div class="mb-3 ml-2"><{$obituaries_user.obituaries_description}></div>

        <{if !empty($obituaries_user.obituaries_survivors)}>
        <div class="alert alert-info"><b><{$smarty.const._AM_OBITUARIES_SURVIVORS}></b> :</div>
        <div class="mb-3 ml-2"><{$obituaries_user.obituaries_survivors}></div>
        <{/if}>

        <{if !empty($obituaries_user.obituaries_service)}>
        <div class="alert alert-warning"><b><{$smarty.const._AM_OBITUARIES_SERVICE}></b> :</div>
        <div class="mb-3 ml-2"><{$obituaries_user.obituaries_service}></div>
        <{/if}>

        <{if !empty($obituaries_user.obituaries_memorial)}>
            <div class="alert alert-danger"><b><{$smarty.const._AM_OBITUARIES_MEMORIAL}></b> :</div>
            <div class="mb-3 ml-2"><{$obituaries_user.obituaries_memorial}></div>
        <{/if}>
    </div>
    <{if $obituaries_user.obituaries_uid > 0}>
        <br>
        <br>
        <div align="center"><b><a href="<{$xoops_url}>/userinfo.php?uid=<{$obituaries_user.obituaries_uid}>"><span class="label label-danger"><{$smarty.const._AM_OBITUARIES_XOOPS_PROFILE}></span></a></b>
        </div>
        <br>
    <{/if}>

<{/if}>

<div style="text-align: center; padding: 3px; margin:3px;">

    <{$commentsnav}>

    <{$lang_notice}>

</div>


<div style="margin:3px; padding: 3px;">

    <{if isset($comment_mode)}>
        <{if $comment_mode == "flat"}>
            <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
            <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
            <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    <{/if}>

</div>
