<div id="xo-system-userform">
    <fieldset>
        <legend><{$lang_login}></legend>
        <form action="<{xoAppUrl /user.php op=login}>" method="post" class="login_form">
            <div class="credentials">
                <label for="login_form-login"><{$lang_username}></label><input type="text" name="uname" id="login_form-login" size="26" maxlength="25"
                                                                               value="<{$usercookie}>"/><br/>
                <label for="login_form-password"><{$lang_password}></label><input type="password" name="pass" id="login_form-password" size="21"
                                                                                  maxlength="32"/>
                <{if isset($lang_rememberme)}>
                    <div class="actions"><input type="checkbox" id="rememberme" name="rememberme" value="On" checked/> <label for="rememberme"><{$lang_rememberme}></label>
                    </div>
                <{/if}>
                <input type="hidden" name="op" value="login"/>
                <input type="hidden" name="xoops_redirect" value="<{$redirect_page}>"/>
            </div>
            <div class="actions"><input class="xo-formbuttons" type="submit" value="<{$lang_login}>"/></div>
        </form>
        <a name="lost"></a>

        <p><{$lang_notregister}></p>
    </fieldset>

    <fieldset>
        <legend><{$lang_lostpassword}></legend>
        <p><{$lang_noproblem}></p>

        <form action="<{xoAppUrl /lostpass.php op=mailpasswd}>" method="post" class="login_form">
            <div class="credentials">
                <label for="login_form-login"><{$lang_youremail}></label>
                <input type="text" name="email" size="26" maxlength="60"/>
                <input type="hidden" name="op" value="mailpasswd"/>
                <input type="hidden" name="t" value="<{$mailpasswd_token}>"/>
            </div>
            <div class="actions"><input class="xo-formbuttons" type="submit" value="<{$lang_sendpassword}>"/></div>
        </form>
    </fieldset>
</div>
