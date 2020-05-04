<h5><{$lang_allaboutuser}></h5>

<{if $user_ownpage == true}>
    <form name="usernav" action="user.php" method="post">

        <br><br>

        <table class="width70 aligncenter bnone">
            <tr class="text-center">
                <td><input type="button" class="btn btn-primary btn-sm" value="<{$lang_editprofile}>" onclick="location='edituser.php'"/>
                    <input type="button" class="btn btn-primary btn-sm" value="<{$lang_avatar}>" onclick="location='edituser.php?op=avatarform'"/>
                    <input type="button" class="btn btn-primary btn-sm" value="<{$lang_inbox}>" onclick="location='viewpmsg.php'"/>

                    <{if $user_candelete == true}>
                        <input type="button" class="btn btn-danger btn-sm" value="<{$lang_deleteaccount}>" onclick="location='user.php?op=delete'"/>
                    <{/if}>

                    <input type="button" class="btn btn-primary btn-sm" value="<{$lang_logout}>" onclick="location='user.php?op=logout'"/></td>
            </tr>
        </table>
    </form>
    <br>
    <br>
<{elseif $xoops_isadmin != false}>
    <br>
    <br>
    <table class="width70 aligncenter bnone">
        <tr class="text-center">
            <td><input type="button" value="<{$lang_editprofile}>"
                       onclick="location='<{$xoops_url}>/modules/system/admin.php?fct=users&amp;uid=<{$user_uid}>&amp;op=modifyUser'"/>
                <input type="button" value="<{$lang_deleteaccount}>"
                       onclick="location='<{$xoops_url}>/modules/system/admin.php?fct=users&amp;op=delUser&amp;uid=<{$user_uid}>'"/>
        </tr>
    </table>
    <br>
    <br>
<{/if}>

<table class="table borderless">
    <tr>
        <td>
            <table>
				<thead class="thead-light">
                <tr>
                    <th colspan="2" class="text-center"><{$smarty.const._US_BASICINFO}></th>
                </tr>
				</thead>
                <{if $user_avatarurl}>
                    <tr>
                        <td><strong><{$lang_avatar}></strong></td>
                        <td><img src="<{$user_avatarurl}>" alt="Avatar" class="img-fluid"/></td>
                    </tr>
                <{/if}>
                <{if $user_realname}>
                    <tr>
                        <td><strong><{$lang_realname}></strong></td>
                        <td><{$user_realname}></td>
                    </tr>
                <{/if}>
                <{if $user_websiteurl}>
                    <tr>
                        <td><strong><{$lang_website}></strong></td>
                        <td><{$user_websiteurl}></td>
                    </tr>
                <{/if}>
                <{if $user_email}>
                    <tr>
                        <td><strong><{$lang_email}></strong></td>
                        <td><{$user_email}></td>
                    </tr>
                <{/if}>
                <{if $xoops_isuser && !$user_ownpage == true}>
                    <tr>
                        <td><strong><{$lang_privmsg}></strong></td>
                        <td><{$user_pmlink}></td>
                    </tr>
                <{/if}>
                <!--<{if $user_icq}>
                    <tr>
                        <td><{$lang_icq}></td>
                        <td><{$user_icq}></td>
                    </tr>
                <{/if}>
                <{if $user_aim}>
                    <tr>
                        <td><{$lang_aim}></td>
                        <td><{$user_aim}></td>
                    </tr>
                <{/if}>
                <{if $user_yim}>
                    <tr>
                        <td><{$lang_yim}></td>
                        <td><{$user_yim}></td>
                    </tr>
                <{/if}>
                <{if $user_msnm}>
                    <tr>
                        <td><{$lang_msnm}></td>
                        <td><{$user_msnm}></td>
                    </tr>
                <{/if}>-->
                <{if $user_location}>
                    <tr>
                        <td><strong><{$lang_location}></strong></td>
                        <td><{$user_location}></td>
                    </tr>
                <{/if}>
                <{if $user_occupation}>
                    <tr>
                        <td><strong><{$lang_occupation}></strong></td>
                        <td><{$user_occupation}></td>
                    </tr>
                <{/if}>
                <{if $user_interest}>
                    <tr>
                        <td><strong><{$lang_interest}><strong></td>
                        <td><{$user_interest}></td>
                    </tr>
                <{/if}>
                <{if $user_extrainfo}>
                    <tr>
                        <td><strong><{$lang_extrainfo}><strong></td>
                        <td><{$user_extrainfo}></td>
                    </tr>
                <{/if}>
            </table>
        </td>
        <td>
            <table class="table">
				<thead class="thead-light">
                <tr>
                    <th colspan="2" class="text-center"><{$lang_statistics}></th>
                </tr>
				</thead>
                <tr>
                    <td><strong><{$lang_membersince}></strong></td>
                    <td><{$user_joindate}></td>
                </tr>
                <tr>
                    <td><strong><{$lang_rank}></strong></td>
                    <td><{$user_rankimage}><br><{$user_ranktitle}></td>
                </tr>
                <tr>
                    <td><strong><{$lang_posts}></strong></td>
                    <td ><{$user_posts}></td>
                </tr>
                <tr>
                    <td><strong><{$lang_lastlogin}></strong></td>
                    <td><{$user_lastlogin}></td>
                </tr>
            </table>
            <{if $user_signature}>
                <br>
                <table class="table">
					<thead class="thead-light">
                    <tr>
                        <th colspan="2" class="text-center"><{$lang_signature}></th>
                    </tr>
					</thead>
                    <tr>
                        <td><{$user_signature}></td>
                    </tr>
                </table>
            <{/if}>
        </td>
    </tr>
</table>

<!-- start module search results loop -->
<h5><{$smarty.const._US_RECENTACTIVITY}></h5>
<{foreach item=module from=$modules}>
    <strong><{$module.name}></strong><br>
    <!-- start results item loop -->
    <{foreach item=result from=$module.results}>
		<{if $result.image}>
		<img src="<{$result.image}>" alt="<{$module.name}>"/>
		<{/if}>
        &nbsp;<strong><a href="<{$result.link}>" title="<{$result.title}>"><{$result.title}></a></strong>
        <br>
		&nbsp;<span class="text-muted"><small><i class="fa fa-calendar"></i>&nbsp;<{$result.time|date_format:"%B %e %Y %l:%M %p"}></small></span>
		<br>	
    <{/foreach}>
    <!-- end results item loop -->
	<{if $module.showall_link}>
	<button type="button" class="btn btn-info btn-sm"><i class="fa fa-search"></i>&nbsp;<{$module.showall_link}></button><br><br>
	<{/if}>

<{/foreach}>

<br>
<!-- end module search results loop -->
