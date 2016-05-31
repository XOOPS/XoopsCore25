<table cellspacing="1" class="outer">
    <{foreach item=user from=$block.users}>
        <tr class="<{cycle values='even,odd'}> alignmiddle">
            <td class="txtcenter">
                <{if $user.avatar != ""}>
                    <img style="width:32px;" src="<{$user.avatar}>" alt="<{$user.name}>"/>
                    <br>
                <{/if}>
                <a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>"><{$user.name}></a>
            </td>
            <td class="txtcenter"><{$user.joindate}></td>
        </tr>
    <{/foreach}>
</table>
