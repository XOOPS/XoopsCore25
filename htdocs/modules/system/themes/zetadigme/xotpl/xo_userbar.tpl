<div id="xo-userbar">
    <{$smarty.const.THEME_WELCOME}>&nbsp;<a class="tooltip" id="xo-uname" href="<{xoAppUrl /user.php}>" title="<{$smarty.const.THEME_PROFILE}>"><{$xoops_uname}></a>
    <br>
    <!-- for my message box  -->
    <{xoInboxCount assign=pmcount}>
    <a href="<{xoAppUrl viewpmsg.php}>">
        <{if $pmcount}>
            <!-- if I have messages -->
            <img class="tooltip" src="<{xoImgUrl img/mail_warning.png}>" title="(<{$pmcount}>) <{$smarty.const.THEME_NOTREAD}>" alt="<{$smarty.const.THEME_MESSAGE}>"/>
        <{else}>
            <!-- if I do not have a message -->
            <img class="tooltip" src="<{xoImgUrl img/mail.png}>" title="<{$smarty.const.THEME_MESSAGE}>" alt="<{$smarty.const.THEME_MESSAGE}>"/>
        <{/if}>
    </a>
    <!-- end my message box-->
    <a class="tooltip" href="<{xoAppUrl /notifications.php}>" title="<{$smarty.const.THEME_NOTIFICATION}>"><img src="<{xoImgUrl img/comment_accept.png}>" alt="<{$smarty.const.THEME_NOTIFICATION}>"/></a>
    <a class="tooltip" href="<{xoAppUrl /user.php}>" title="<{$smarty.const.THEME_MYACCOUNT}>"><img src="<{xoImgUrl img/user.png}>" alt="<{$smarty.const.THEME_MYACCOUNT}>"/></a>
    <a class="tooltip" href="<{xoAppUrl /search.php}>" title="<{$smarty.const.THEME_DESC_SEARCH}>"><img src="<{xoImgUrl img/find.png}>" alt="<{$smarty.const.THEME_DESC_SEARCH}>"/></a>
    <a class="tooltip" href="<{xoAppUrl /}>" title="<{$smarty.const._YOURHOME}>"><img src="<{xoImgUrl img/home.png}>" alt="<{$smarty.const._YOURHOME}>"/></a>
    <a class="tooltip" href="<{xoAppUrl /user.php op=logout}>" title="<{$smarty.const._LOGOUT}>"><img src="<{xoImgUrl img/logout.png}>" alt="<{$smarty.const._LOGOUT}>"/></a>
</div>
