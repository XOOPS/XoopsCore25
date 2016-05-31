<div class="txtcenter">
    <form style="margin-top: 0;" action="<{xoAppUrl user.php}>" method="post">
        <{$block.lang_username}><br>
        <input type="text" name="uname" size="12" value="<{$block.unamevalue}>" maxlength="25"/><br>
        <{$block.lang_password}><br>
        <input type="password" name="pass" size="12" maxlength="32"/><br>
        <{if isset($block.lang_rememberme)}>
            <input type="checkbox" name="rememberme" value="On" class="formButton"/>
            <{$block.lang_rememberme}>
            <br>
        <{/if}>
        <br>
        <input type="hidden" name="xoops_redirect" value="<{$xoops_requesturi}>"/>
        <input type="hidden" name="op" value="login"/>
        <input type="submit" value="<{$block.lang_login}>"/><br>
        <{$block.sslloginlink}>
    </form>
    <br>
    <a href="<{xoAppUrl user.php#lost}>" title="<{$block.lang_lostpass}>"><{$block.lang_lostpass}></a>
    <br><br>
    <a href="<{xoAppUrl register.php}>" title="<{$block.lang_registernow}>"><{$block.lang_registernow}></a>
</div>
