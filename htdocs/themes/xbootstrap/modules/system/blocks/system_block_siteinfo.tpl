<{if $block.showgroups == true}>
	<table style="background-color: inherit;">

        <!-- start group loop -->
        <{foreach item=group from=$block.groups|default:null}>

			<{if !empty($group.name)}>
				<thead> 
					<tr>
						<th colspan="2"><{$group.name}></th>
					</tr>
				</thead> 
            <{/if}>

            <!-- start group member loop -->
            <{foreach item=user from=$group.users|default:null}>
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

	</table>
<{/if}>
<br>

<div>
    <img src="<{$block.logourl}>" alt=""/><br><{$block.recommendlink}>
</div>
