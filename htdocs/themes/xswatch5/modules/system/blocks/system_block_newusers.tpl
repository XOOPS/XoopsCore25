<{foreach item=user from=$block.users|default:null}>
    <ul class="userblock list-unstyled">
        <{if $user.avatar|default:'' != ''}>
            <li class="avatar-image"><img src="<{$user.avatar}>" alt="<{$user.name}>" class="rounded"></li>
        <{else}>
            <li class="avatar-image"><img src="<{$xoops_imageurl}>images/blank.gif" alt="<{$user.name}>" class="rounded"></li>
        <{/if}>
        <li class="user-name"><a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>" class="text-decoration-none"><{$user.name}></a></li>
        <li class="join-date text-end join-date d-none d-sm-inline"><{$user.joindate}></li>
    </ul>
<{/foreach}>
