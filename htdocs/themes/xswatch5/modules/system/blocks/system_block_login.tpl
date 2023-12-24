<div>
    <form action="<{xoAppUrl 'user.php'}>" method="post" role="form">
        <div class="mb-3 mt-2">
            <!-- <{$block.lang_username}> -->
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><span class="fa fa-user fa-lg fa-fw text-info my-1"></span></div>
                </div>
                <input class="form-control form-control-sm" type="text" name="uname" placeholder="<{$smarty.const.THEME_LOGIN}>">
            </div>
        </div>

        <div class="mb-3 mt-2">
            <!-- <{$block.lang_password}> -->
             <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text"><span class="fa fa-lock fa-lg fa-fw text-info my-1"></span></div>
                </div>
                <input class="form-control form-control-sm" type="password" name="pass" placeholder="<{$smarty.const.THEME_PASS}>">
            </div>
        </div>
        
        <div class="checkbox my-2 text-center">
            <label>
                <{if isset($block.lang_rememberme)}>
                    <input type="checkbox" name="rememberme" value="On" class="formButton">
                    <{$block.lang_rememberme}>
                <{/if}>
            </label>
        </div>

        <input type="hidden" name="xoops_redirect" value="<{$xoops_requesturi}>">
        <input type="hidden" name="op" value="login">
        <p class="text-center">
            <input type="submit" class="btn btn-primary btn-block" value="<{$block.lang_login}>">
        </p>
        <hr />
        <{$block.sslloginlink|default:''}>

        <div class="d-flex justify-content-around">
            <div class="">
                <a class="btn btn-secondary btn-sm" href="<{xoAppUrl 'user.php#lost'}>" title="<{$block.lang_lostpass}>"><{$block.lang_lostpass}></a>
            </div>
            <div class="">
                <a class="btn btn-info btn-sm" href="<{xoAppUrl 'register.php'}>" title="<{$block.lang_registernow}>"><{$block.lang_registernow}></a>
            </div>
        </div>

    </form>
</div>
