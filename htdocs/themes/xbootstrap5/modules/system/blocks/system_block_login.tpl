<div class="loginform">
    <form action="<{xoAppUrl 'user.php'}>" method="post" role="form">
        <div class="mb-3">
            <{$block.lang_username}>
            <input class="form-control" type="text" name="uname" placeholder="<{$smarty.const.THEME_LOGIN}>">
        </div>

        <div class="mb-3">
            <{$block.lang_password}>
            <input class="form-control" type="password" name="pass" placeholder="<{$smarty.const.THEME_PASS}>">
        </div>

        <div class="checkbox">
            <label>
                <{if isset($block.lang_rememberme)}>
                    <input type="checkbox" name="rememberme" value="On" class="formButton">
                    <{$block.lang_rememberme}>
                <{/if}>
            </label>
        </div>

        <input type="hidden" name="xoops_redirect" value="<{$xoops_requesturi}>">
        <input type="hidden" name="op" value="login">
        <input type="submit" class="btn btn-primary w-100 btn-sm" value="<{$block.lang_login}>">
        <{$block.sslloginlink|default:''}>
    </form>
    <a class="btn btn-info btn-sm w-100" href="<{xoAppUrl 'user.php#lost'}>" title="<{$block.lang_lostpass}>"><{$block.lang_lostpass}></a>

    <a class="btn btn-info btn-sm w-100" href="<{xoAppUrl 'register.php'}>" title="<{$block.lang_registernow}>"><{$block.lang_registernow}></a>
</div>
