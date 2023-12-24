<div class="usermenu">
    <ul class="nav flex-column">
        <{if isset($xoops_isadmin)}>
            <li class="nav-item">
                <a class="nav-link text-warning" href="<{xoAppUrl 'admin.php'}>" title="<{$block.lang_adminmenu}>"><span class="fa fa-wrench fa-fw"></span><{$block.lang_adminmenu}></a>
            </li>
        <{/if}>
        <li class="nav-item">
            <a class="nav-link menuTop" href="<{xoAppUrl 'user.php'}>" title="<{$block.lang_youraccount}>"><span class="fa fa-user-circle-o text-info fa-fw"></span><{$block.lang_youraccount}></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<{xoAppUrl 'edituser.php'}>" title="<{$block.lang_editaccount}>"><span class="fa fa-pencil text-info fa-fw"></span><{$block.lang_editaccount}></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<{xoAppUrl 'notifications.php'}>" title="<{$block.lang_notifications}>"><span class="fa fa-info-circle text-info fa-fw"></span><{$block.lang_notifications}></a>
        </li>
        <{xoInboxCount assign='unread_count'}>
        <{if $unread_count > 0}>
            <li class="nav-item">
                <a class="nav-link info" href="<{xoAppUrl 'viewpmsg.php'}>" title="<{$block.lang_inbox}>">
                    <span class="fa fa-envelope-o text-warning fa-fw"></span><{$block.lang_inbox}> <span class="badge bg-warning badge-rounded"><{$unread_count}></span>
                </a>
            </li>
        <{else}>
            <li class="nav-item">
                <a class="nav-link" href="<{xoAppUrl 'viewpmsg.php'}>" title="<{$block.lang_inbox}>">
                    <span class="fa fa-envelope-open-o text-info fa-fw"></span><{$block.lang_inbox}>
                </a>
            </li>
        <{/if}>
        <li class="nav-item">
            <a class="nav-link" href="<{xoAppUrl 'user.php?op=logout'}>" title="<{$block.lang_logout}>"><span class="fa fa-sign-out text-info fa-fw"></span><{$block.lang_logout}></a>
        </li>
    </ul>
</div>
