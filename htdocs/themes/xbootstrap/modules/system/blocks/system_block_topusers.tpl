<{foreach item=user from=$block.users}>
    <ul class="userblock list-unstyled">

        <{if $user.avatar != ""}>
            <li class="avatar-image">
                <img src="<{$user.avatar}>" alt="<{$user.name}>" class="img-circle">
                <span class="badge pull-right"><{$user.rank}></span>
            </li>
        <{else}>
            <li class="avatar-image">
                <img src="<{$xoops_imageurl}>images/blank.gif" alt="<{$user.name}>" class="img-circle">
                <span class="badge pull-right"><{$user.rank}></span>
            </li>
        <{/if}>

        <li class="user-name"><a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>"><{$user.name}></a></li>

        <li class="join-date text-right"><{$user.posts}></li>
    </ul>
<{/foreach}>
