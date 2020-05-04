<table class="table">

    <{if $block.showgroups == true}>

        <!-- start group loop -->
        <{foreach item=group from=$block.groups}>
			<thead class="thead-light text-center">
            <tr>
                <th><{$group.name}></th>
            </tr>
			</thead>
            <!-- start group member loop -->
            <{foreach item=user from=$group.users}>
                <tr>
                    <td class="text-center">
                        <img style="width:80px;" src="<{$user.avatar}>" alt="<{$user.name}>" title="<{$user.name}>" class="img-fluid rounded-circle"/><br>
                        <small><a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>"><{$user.name}></a>
                        <a href="javascript:openWithSelfMain('<{$xoops_url}>/pmlite.php?send2=1&to_userid=<{$user.id}>','pmlite',565,500);"></small>
                        &nbsp;<span class="fa fa-envelope" aria-hidden="true"></span>
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

<div class='text-center'>
    <img src="<{$block.logourl}>" alt=""/><br><{$block.recommendlink}>
</div>
