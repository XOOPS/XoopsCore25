<{foreach item=user from=$block.users}>
    <ul class="userblock list-unstyled p-2">
        <{if $user.avatar != ""}>
            <li class="avatar-image"><img src="<{$user.avatar}>" title="<{$user.name}>" alt="<{$user.name}>" class="rounded-circle"></li>
        <{else}>
            <li class="avatar-image"><img src="<{$xoops_imageurl}>images/blank.gif" title="<{$user.name}>" alt="<{$user.name}>" class="rounded-circle"></li>
        <{/if}>

        <li class="user-name"><a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>"><{$user.name}></a></li>

        <li class="text-right text-muted"><small><{$user.joindate}></small></li>
    </ul>
<{/foreach}>
