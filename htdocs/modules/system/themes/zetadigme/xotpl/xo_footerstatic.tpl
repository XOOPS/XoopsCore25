<div id="xo-footerstatic">
    <div id="controls">
        <div>
            <ul>
                <li class="icon"><a href="<{xoAppUrl /}>" title="<{$smarty.const._YOURHOME}>"><img src="<{xoImgUrl img/home.png}>" alt="<{$smarty.const._YOURHOME}>"/></a></li>
                <li class="separate">&nbsp;</li>
                <li class="text"><a href="<{xoAppUrl modules/system/admin.php?fct=preferences}>" title="<{$lang_preferences}>"><{$lang_preferences}></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=preferences&op=show&confcat_id=1}>" title="<{$lang_preferences}>"><img src="<{xoImgUrl img/prefs_small.png}>" alt="<{$smarty.const._CPHOME}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=preferences&op=showmod&mod=1}>" title="<{$smarty.const.THEME_SYSSETTING}>"><img src="<{xoImgUrl img/configure_shortcuts.png}>" alt="<{$smarty.const.THEME_SYSSETTING}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl admin.php}>" title="<{$smarty.const._CPHOME}>"><img src="<{xoImgUrl img/process.png}>" alt="<{$smarty.const._CPHOME}>"/></a></li>
                <li class="separate">&nbsp;</li>
                <li class="text"><a href="<{xoAppUrl modules/system/admin.php}>" title="<{$smarty.const.THEME_ADMTOOLS}>"><{$smarty.const.THEME_ADMTOOLS}></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=modulesadmin}>" title="<{$lang_modules}>"><img src="<{xoImgUrl img/modules_small.png}>" title="<{$lang_modules}>"></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=blocksadmin}>" title="<{$lang_blocks}>"><img src="<{xoImgUrl img/blocks_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=tplsets}>" title="<{$smarty.const.THEME_TPLSET}>"><img src="<{xoImgUrl img/tpls_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=maintenance}>" title="<{$smarty.const.THEME_MAINTENANCE}>"><img src="<{xoImgUrl img/maintenance_small.png}>"/></a></li>
                <li class="separate">&nbsp;</li>
                <li class="text"><a href="<{xoAppUrl modules/system/admin.php?fct=users}>" title="<{$smarty.const.THEME_USER}>"><{$smarty.const.THEME_USER}></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=groups}>" title="<{$lang_groups}>"><img src="<{xoImgUrl img/groups_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=userrank}>" title="<{$lang_ranks}>"><img src="<{xoImgUrl img/userrank_small.png}>"/></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=users}>" title="<{$lang_edituser}>"><img src="<{xoImgUrl img/edituser_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=findusers}>" title="<{$smarty.const.THEME_FINDUSER}>"><img src="<{xoImgUrl img/finduser_small.png}>" alt=""/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=mailusers}>" title="<{$lang_mailuser}>"><img src="<{xoImgUrl img/mailuser_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=comments}>" title="<{$lang_comments}>"><img src="<{xoImgUrl img/comments_small.png}>"/></a></li>
                <li class="separate">&nbsp;</li>
                <li class="text"><a href="<{xoAppUrl modules/system/admin.php?fct=images}>" title="<{$lang_images}>"><{$lang_images}></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=images}>" title="<{$lang_images}>"><img src="<{xoImgUrl img/images_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=avatars}>" title="<{$lang_avatars}>"><img src="<{xoImgUrl img/avatar_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=smilies}>" title="<{$lang_smilies}>"><img src="<{xoImgUrl img/smilies_small.png}>"/></a></li>
                <li class="icon"><a href="<{xoAppUrl modules/system/admin.php?fct=banners}>" title="<{$lang_banners}>"><img src="<{xoImgUrl img/banners_small.png}>"/></a></li>
                <li class="separate">&nbsp;</li>

                <li class="chat text"><a href="#" title="<{$smarty.const.THEME_UPTOP}>"><img src="<{xoImgUrl img/up.png}>" alt="<{$smarty.const.THEME_UPTOP}>"/></a></li>
                <li class="chat separate">&nbsp;</li>
                <li class="chat text">Powered by <a href="http://xoops.org" title="XOOPS Project HomePage"><{$xoops_version}></a></li>
                <li class="chat separate">&nbsp;</li>
                <li class="chat"><a href="<{xoAppUrl /user.php op=logout}>" title="<{$smarty.const._LOGOUT}>"><img src="<{xoImgUrl img/logout.png}>" alt="<{$smarty.const._LOGOUT}>"/></a></li>
                <!-- for my message box  -->
                <{xoInboxCount assign=pmcount}>
                <li class="chat">
                    <a href="<{xoAppUrl viewpmsg.php}>" title="<{$smarty.const.THEME_MESSAGE}> (<{$pmcount}>)">
                        <{if $pmcount}>
                            <!-- if I have messages -->
                            <img src="<{xoImgUrl img/mail_warning.png}>" alt="(<{$pmcount}>) <{$smarty.const.THEME_NOTREAD}>"/>
                            (
                            <span class="shadow" style="color:#ff0000; font-weight: bold;"><{$pmcount}></span>
                            )
                        <{else}>
                            <!-- if I do not have a message -->
                            <img src="<{xoImgUrl img/mail.png}>" alt="<{$smarty.const.THEME_MESSAGE}>"/>
                        <{/if}>
                    </a>
                </li>
                <li class="chat"><a href="<{xoAppUrl notifications.php}>" title="<{$smarty.const.THEME_NOTIFICATION}>"><img src="<{xoImgUrl img/comment_accept.png}>" alt="<{$smarty.const.THEME_NOTIFICATION}>"/></a></li>
                <li class="chat"><a href="<{xoAppUrl edituser.php}>" title="<{$smarty.const.THEME_EDITPROFILE}>"><img src="<{xoImgUrl img/user_edit.png}>" alt="<{$smarty.const.THEME_MYACCOUNT}>"/></a></li>
                <li class="chat"><a href="<{xoAppUrl /user.php}>" title="<{$smarty.const.THEME_MYACCOUNT}>"><img src="<{xoImgUrl img/user.png}>" alt="<{$smarty.const.THEME_MYACCOUNT}>"/></a></li>
                <li class="chat separate">&nbsp;</li>
            </ul>
        </div>
    </div>
</div>
