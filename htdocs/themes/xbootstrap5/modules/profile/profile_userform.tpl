<div class="container-fluid">
    <div class="row">
        <legend class="bold"><{$lang_login}></legend>
        <form action="user.php" method="post">
            <label for="profile-uname"><{$lang_username}></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa-solid fa-user"></i></span>
                <input class="form-control" type="text" name="uname" id="profile-uname" value="" placeholder="<{$smarty.const.THEME_LOGIN}>">
            </div>

            <label for="profile-pass"><{$lang_password}></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa-solid fa-lock"></i></span>
                <input class="form-control" type="password" name="pass" id="profile-pass" placeholder="<{$smarty.const.THEME_PASS}>">
            </div>
            <div class="checkbox">
                <label>
                    <{if isset($lang_rememberme)}>
                        <input type="checkbox" name="rememberme">
                        <{$lang_rememberme}>
                    <{/if}>
                </label>
            </div>

            <input type="hidden" name="op" value="login" class="form-control">
            <input type="hidden" name="xoops_redirect" value="<{$redirect_page}>" class="form-control">
            <button type="submit" class="btn btn-secondary"><{$lang_login}></button>
        </form>
        <br>
        <a name="lost"></a>

        <div><{$lang_notregister}><br></div>
    </div>

    <br>
    <div class="row">
        <legend class="bold"><{$lang_lostpassword}></legend>
        <p><{$lang_noproblem}></p>
        <form action="lostpass.php" method="post">
            <label for="profile-lostpass"><{$lang_youremail}></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fa-solid fa-envelope"></i></span>
                <input class="form-control" type="text" name="email" id="profile-lostpass">
            </div>
            <input type="hidden" name="op" value="mailpasswd" class="form-control">
            <input type="hidden" name="t" value="<{$mailpasswd_token}>" class="form-control">
            <button type="submit" class="btn btn-secondary"><{$lang_sendpassword}></button>
        </form>
    </div>
</div>
