<{include file="db:profile_breadcrumbs.tpl"}>
<div class="row">
    <div class="col-md-6 text-center">
        <{if isset($avatar)}>
            <img src="<{$avatar}>" alt="<{$uname}>" class="img-responsive img-rounded img-thumbnail">
        <{/if}>
            <ul class="list-unstyled">
                <li><span class="label label-info"><{$uname}></span></li>
                <{if isset($email)}>
                    <li><span class="label label-info"><{$email}></span></li>
                <{/if}>
            </ul>
    </div><!-- .col-md-6 -->

    <div class="col-md-6">
        <{if !$user_ownpage && $xoops_isuser == true}>
            <form name="usernav" action="user.php" method="post">
                <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_SENDPM}>"
                       onclick="openWithSelfMain('<{$xoops_url}>/pmlite.php?send2=1&amp;to_userid=<{$user_uid}>', 'pmlite', 565, 500);">
            </form>
        <{/if}>

        <{if isset($user_ownpage) && $user_ownpage == true}>
            <form name="usernav" action="user.php" method="post">
                <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$lang_editprofile}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/edituser.php'">
                <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$lang_changepassword}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/changepass.php'">
                <{if isset($user_changeemail)}>
                    <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_CHANGEMAIL}>"
                           onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/changemail.php'">
                <{/if}>
                <{if isset($user_candelete) && $user_candelete == true}>
                    <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$lang_deleteaccount}>" onclick="location='user.php?op=delete'">
                <{/if}>
                <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$lang_avatar}>" onclick="location='edituser.php?op=avatarform'">
                <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$lang_inbox}>" onclick="location='<{$xoops_url}>/viewpmsg.php'">
                <input class="btn btn-primary btn-xs btn-block" type="button" value="<{$lang_logout}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/user.php?op=logout'">
            </form>
        <{elseif $xoops_isadmin != false}>
            <form method="post" action="<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/deactivate.php">
                <input class="btn btn-warning btn-xs btn-block" type="button" value="<{$lang_editprofile}>"
                       onclick="location='<{$xoops_url}>/modules/<{$xoops_dirname}>/admin/user.php?op=edit&amp;id=<{$user_uid}>'">
                <input type="hidden" name="uid" value="<{$user_uid}>">
                <{securityToken}>
                <{if isset($userlevel) && $userlevel == 1}>
                    <input type="hidden" name="level" value="0">
                    <input class="btn btn-danger btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_DEACTIVATE}>" onclick="submit();">
                <{else}>
                    <input type="hidden" name="level" value="1">
                    <input class="btn btn-warning btn-xs btn-block" type="button" value="<{$smarty.const._PROFILE_MA_ACTIVATE}>" onclick="submit();">
                <{/if}>
            </form>
        <{/if}>
    </div><!-- .col-md-6 -->
</div><!-- .row -->

<{foreach item=category from=$categories|default:null}>
    <{if isset($category.fields)}>
        <ul id="profile-category-<{$category.cat_id}>" class="profile-values list-unstyled">
            <li class="profile-category-title"><{$category.cat_title}></li>
            <{foreach item=field from=$category.fields|default:null}>
                <li><strong><{$field.title}>:</strong> <{$field.value}></li>
            <{/foreach}>
        </ul>
    <{/if}>
<{/foreach}>

<{if !empty($modules)}>
    <ul class="profile-values list-unstyled">
        <li class="profile-category-title"><{$recent_activity}></li>
        <{foreach item=module from=$modules|default:null}>
<!-- alain01 -->
            <div class="card my-3">
                <div class="card-header"><h5><{$module.name}> <{if $module.showall_link}><span class="x-small">| <{$module.showall_link}></span><{/if}></h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <{foreach item=result from=$module.results|default:null}>
                        <li class="list-group-item list-group-item-action">
                            <{assign var="url_image_overloaded" value=$xoops_imageurl|cat:"modules/"|cat:$result.image|replace:"$xoops_url/modules/":''}>
                            <{assign var="path_image_overloaded" value=$xoops_rootpath|cat:"/themes/"|cat:$xoops_theme|cat:"/"|cat:$url_image_overloaded|replace:$xoops_imageurl:''}>

                            <{if file_exists($path_image_overloaded)}>
                                <div class="d-inline"><img src="<{$url_image_overloaded}>" alt="<{$module.name}>"> <a href="<{$result.link}>"><{$result.title}></a></div>
                                <span class="d-inline d-sm-none"><br /></span>
                                <div class="d-inline text-muted"><small><span class="fa fa-calendar fa-sm ml-2"></span> <{$result.time}></small></div>
                                <br />
                            <{else}>
                                <img src="<{$result.image}>" alt="<{$module.name}>"> <a href="<{$result.link}>"><{$result.title}></a> <small><span class="fa fa-calendar fa-sm ml-2"></span> <{$result.time}></small><br />
                            <{/if}>
                        </li>
                        <{/foreach}>
                    </ul>
                </div>
            </div>
       <{/foreach}>
    </ul>
<{/if}>
