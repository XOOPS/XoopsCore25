<{if isset($obituaries_user)}>
    <ol class="breadcrumb">
        <li><a href="index.php"><{$breadcrumb}></a></li>
        <{if $xoops_isadmin}>
            <li>[ <a href="<{$xoops_url}>/modules/obituaries/admin/main.php?op=edit&id=<{$obituaries_user.obituaries_id}>"><{$smarty.const._EDIT}></a> ]</li>
        <{/if}>
    </ol>
    <div style="margin-left: 10px; text-align: justify;">

        <{if trim($obituaries_user.obituaries_full_imgurl) != ''}>
            <div style="margin: 5px 10px;float: left;">

                <img src="<{$obituaries_user.obituaries_full_imgurl}>" alt="<{$obituaries_user.obituaries_href_title}>" class="img-thumbnail">

            </div>
        <{elseif trim($obituaries_user.obituaries_user_user_avatar) != ''}>
            <div style="margin: 5px 10px;float: left;">

                <img src="<{$xoops_url}>/uploads/<{$obituaries_user.obituaries_user_user_avatar}>" alt="<{$obituaries_user.obituaries_href_title}>"/>

            </div>
        <{else}>
            <div style="margin: 5px 10px;float: left;">

                <img src="<{$xoops_url}>/modules/obituaries/images/nophoto.jpg" alt="<{$obituaries_user.obituaries_href_title}>" width="130"/>

            </div>
        <{/if}>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
        <b><span class="glyphicon glyphicon-user"></span>&nbsp;<{$obituaries_user.obituaries_fullname}></b>
        </br>

        <b><span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$smarty.const._AM_OBITUARIES_DATE}></b> :
        <{$obituaries_user.obituaries_formated_date}>

        <br>

        <b><span class="glyphicon glyphicon-check"></span>&nbsp;<{$smarty.const._AM_OBITUARIES_FIRSTNAME}></b> : <{$obituaries_user.obituaries_firstname}>

        <br>

        <b><span class="glyphicon glyphicon-info-sign"></span>&nbsp;<{$smarty.const._AM_OBITUARIES_LASTNAME}></b> :
        <{$obituaries_user.obituaries_lastname}>

        <br><br>

        <div class="alert alert-success"><b><{$smarty.const._AM_OBITUARIES_DESCRIPTION}></b> :</div>
        <div class="well well-lg"><{$obituaries_user.obituaries_description}></div>


        <div class="alert alert-info"><b><{$smarty.const._AM_OBITUARIES_SURVIVORS}></b> :</div>
        <div class="well well-lg"><{$obituaries_user.obituaries_survivors}></div>


        <div class="alert alert-warning"><b><{$smarty.const._AM_OBITUARIES_SERVICE}></b> :</div>
        <div class="well well-lg"><{$obituaries_user.obituaries_service}></div>

        <{if $obituaries_user.obituaries_memorial != ""}>
            <div class="alert alert-danger"><b><{$smarty.const._AM_OBITUARIES_MEMORIAL}></b> :</div>
            <div class="well well-lg"><{$obituaries_user.obituaries_memorial}></div>
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

    <{if $comment_mode == "flat"}>

        <{include file="db:system_comments_flat.tpl"}>

    <{elseif $comment_mode == "thread"}>

        <{include file="db:system_comments_thread.tpl"}>

    <{elseif $comment_mode == "nest"}>

        <{include file="db:system_comments_nest.tpl"}>

    <{/if}>

</div>
