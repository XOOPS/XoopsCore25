<{includeq file="db:profile_breadcrumbs.tpl"}>
<div class="row">
    <div class="col-xs-6 col-md-6 aligncenter">
        <{if $avatar}>
            <img src="<{$avatar}>" alt="<{$uname}>" class="img-responsive img-rounded img-thumbnail">
        <{/if}>
        <div class="aligncenter">
            <ul class="list-unstyled">
                <li><span class="label label-info"><{$uname}></span></li>
                <{if $email}>
                    <li><span class="label label-info"><{$email}></span></li>
                <{/if}>
            </ul>
        </div><!-- .aligncenter -->
    </div><!-- .col-md-6 .aligncenter -->

    <div class="col-xs-6 col-md-6">
        <{if !$user_ownpage && $xoops_isuser == true}>
            <form name="usernav" action="user.php" method="post">
                <input class="btn btn-success btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_SENDPM}>"
                       onclick="openWithSelfMain('<{$xoops_url}>/pmlite.php?send2=1&amp;to_userid=<{$user_uid}>', 'pmlite', 565, 500);">
            </form>
        <{/if}>

        <{if $user_ownpage == true}>
            <form name="usernav" action="user.php" method="post">
                <input class="btn btn-success btn-xs btn-block" type="button" value="<{$lang_editprofile}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/edituser.php'">
                <input class="btn btn-success btn-xs btn-block" type="button" value="<{$lang_changepassword}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/changepass.php'">
                <{if $user_changeemail}>
                    <input class="btn btn-success btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_CHANGEMAIL}>"
                           onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/changemail.php'">
                <{/if}>

                <{if $user_candelete == true}>
                    <form method="post" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/user.php">
                        <input type="hidden" name="op" value="delete">
                        <input type="hidden" name="uid" value="<{$user_uid}>">
                        <input class="btn btn-success btn-xs btn-block" type="button" value="<{$lang_deleteaccount}>" onclick="submit();">
                    </form>
                <{/if}>

                <input class="btn btn-success btn-xs btn-block" type="button" value="<{$lang_avatar}>" onclick="location='edituser.php?op=avatarform'">
                <input class="btn btn-success btn-xs btn-block" type="button" value="<{$lang_inbox}>" onclick="location='<{$xoops_url}>/viewpmsg.php'">
                <input class="btn btn-success btn-xs btn-block" type="button" value="<{$lang_logout}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/user.php?op=logout'">
            </form>
        <{elseif $xoops_isadmin != false}>
            <form method="post" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/deactivate.php">
                <input class="btn btn-info btn-xs btn-block" type="button" value="<{$lang_editprofile}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/user.php?op=edit&amp;id=<{$user_uid}>'">
                <input type="hidden" name="uid" value="<{$user_uid}>">
                <{securityToken}>
                <{if $userlevel == 1}>
                    <input type="hidden" name="level" value="0">
                    <input class="btn btn-info btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_DEACTIVATE}>" onclick="submit();">
                <{else}>
                    <input type="hidden" name="level" value="1">
                    <input class="btn btn-info btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_ACTIVATE}>" onclick="submit();">
                <{/if}>
            </form>
        <{/if}>
    </div><!-- .col-md-6 -->
</div><!-- .row -->

<{foreach item=category from=$categories}>
    <{if isset($category.fields)}>
        <ul id="profile-category-<{$category.cat_id}>" class="profile-values list-unstyled">
            <li class="profile-category-title"><{$category.cat_title}></li>
            <{foreach item=field from=$category.fields}>
                <li><strong><{$field.title}>:</strong> <{$field.value}></li>
            <{/foreach}>
        </ul>
    <{/if}>
<{/foreach}>

<{if $modules}>
    <ul class="profile-values list-unstyled">
        <li class="profile-category-title"><{$recent_activity}></li>
        <{foreach item=module from=$modules}>
            <li><strong><{$module.name}></strong></li>
            <{foreach item=result from=$module.results}>
                <li><img src="<{$result.image}>" alt="<{$module.name}>"> <a href="<{$result.link}>"><{$result.title}></a> (<{$result.time}>)</li>
            <{/foreach}>
            <{$module.showall_link}>
        <{/foreach}>
    </ul>
<{/if}>
