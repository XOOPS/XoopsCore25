<{if $block.showgroups == true}>
	
	<table class="outer collapse">

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
                        <img style="width:32px;" src="<{$user.avatar}>" alt="<{$user.name}>"/><br>
                        <a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>"><{$user.name}></a>
                    </td>
                    <td class="odd width20 txtright alignmiddle">
                        <{$user.msglink}>
                    </td>
                </tr>
            <{/foreach}>
            <!-- end group member loop -->

        <{/foreach}>
        <!-- end group loop -->
	</table>
<{/if}>

<br>

<div class="txtcenter marg3">
    <img src="<{$block.logourl}>" alt=""/><br><{$block.recommendlink}>
</div>
