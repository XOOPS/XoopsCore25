<div id="usermenu">
    <{if $xoops_isadmin}>
        <a class="menuTop" href="<{xoAppUrl admin.php}>" title="<{$block.lang_adminmenu}>"><{$block.lang_adminmenu}></a>
        <a href="<{xoAppUrl user.php}>" title="<{$block.lang_youraccount}>"><{$block.lang_youraccount}></a>
    <{else}>
        <a class="menuTop" href="<{xoAppUrl user.php}>" title="<{$block.lang_youraccount}>"><{$block.lang_youraccount}></a>
    <{/if}>
    <a href="<{xoAppUrl edituser.php}>" title="<{$block.lang_editaccount}>"><{$block.lang_editaccount}></a>
    <a href="<{xoAppUrl notifications.php}>" title="<{$block.lang_notifications}>"><{$block.lang_notifications}></a>
    <{if $block.new_messages > 0}>
        <a class="highlight" href="<{xoAppUrl viewpmsg.php}>" title="<{$block.lang_inbox}>"><{$block.lang_inbox}>
            (<strong><{$block.new_messages}></strong>)</a>
    <{else}>
        <a href="<{xoAppUrl viewpmsg.php}>" title="<{$block.lang_inbox}>"><{$block.lang_inbox}></a>
    <{/if}>
    <a href="<{xoAppUrl user.php?op=logout}>" title="<{$block.lang_logout}>"><{$block.lang_logout}></a>
</div>
