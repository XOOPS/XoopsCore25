<div>
    <div class="even" style="padding: 5px; line-height: 150%;">
        <span style="padding: 2px;"><{$online.image}></span>
        <strong><{$smarty.const._MD_NEWBB_USERS_ONLINE}> <{$online.num_total}>  <{$smarty.const._MD_NEWBB_BROWSING_FORUM}></strong>
    </div>
    <div class="odd" style="padding: 5px; line-height: 150%;">
        [ <span class="online_admin"><{$smarty.const._MD_NEWBB_ADMINISTRATOR}></span> ] [ <span
                class="online_moderator"><{$smarty.const._MD_NEWBB_MODERATOR}></span> ]
        <br><{$online.num_anonymous}> <{$smarty.const._MD_NEWBB_ANONYMOUS_USERS}>
        <{if $online.num_user}>
            <br>
            <{$online.num_user}> <{$smarty.const._MD_NEWBB_REGISTERED_USERS}>
            <{foreach item=user from=$online.users}>
            <a href="<{$user.link}>">
                <{if $user.level eq 2}>
                    <span class="online_admin"><{$user.uname}></span>
                <{elseif $user.level eq 1}>
                    <span class="online_moderator"><{$user.uname}></span>
                <{else}>
                    <{$user.uname}>
                <{/if}>
            </a>
        <{/foreach}>
        <{/if}>
    </div>
</div>
