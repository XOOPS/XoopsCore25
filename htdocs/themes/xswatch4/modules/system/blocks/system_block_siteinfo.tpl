<table style="background-color: inherit;">

    <{if $block.showgroups == true}>

        <!-- start group loop -->
        <{foreach item=group from=$block.groups}>
            <tr>
                <th colspan="2"><{$group.name}></th>
            </tr>
            <!-- start group member loop -->
            <{foreach item=user from=$group.users}>
                <tr>
                    <td class="even txtcenter alignmiddle">
                        <img style="width:48px;" src="<{$user.avatar}>" alt="<{$user.name}>"/><br>
                        <a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>"><{$user.name}></a>
                    </td>
                    <td class="odd width20 txtright alignmiddle">
                        <a href="javascript:openWithSelfMain('<{$xoops_url}>/pmlite.php?send2=1&to_userid=<{$user.id}>','pmlite',565,500);">
                        <span class="fa fa-envelope fa-lg" aria-hidden="true"></span>
                        </a>
                    </td>
                </tr>
            <{/foreach}>
            <!-- end group member loop -->

        <{/foreach}>
        <!-- end group loop -->
    <{/if}>
</table>

<br>

<div>
    <img src="<{$block.logourl}>" alt=""/><br><{$block.recommendlink}>
</div>
