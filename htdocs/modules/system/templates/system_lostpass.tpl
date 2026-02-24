<fieldset class="pad10">
    <legend class="bold"><{$lp_heading|escape}></legend>

    <{if $lp_message != ''}>
        <div class="resultMsg"><{$lp_message|escape}></div><br>
    <{/if}>

    <{if $lp_errors|@count > 0}>
        <div class="errorMsg">
            <ul>
                <{foreach item=err from=$lp_errors}>
                    <li><{$err|escape}></li>
                <{/foreach}>
            </ul>
        </div><br>
    <{/if}>

    <{if $lp_show_form}>
        <form method="post" action="<{$lp_action|escape}>">
            <input type="hidden" name="uid" value="<{$lp_uid|intval}>">
            <input type="hidden" name="token" value="<{$lp_token|escape}>">
            <{$lp_token_html}>

            <div><{$lp_lang_password|escape}><br>
                <input type="password" name="pass" size="21" autocomplete="new-password" required>
            </div><br>

            <div><{$lp_lang_verifypass|escape}><br>
                <input type="password" name="vpass" size="21" autocomplete="new-password" required>
            </div><br>

            <div class="xoops-form-element-caption-required">
                <{$lp_min_pw_note|escape}>
            </div><br>

            <div><input type="submit" value="<{$lp_lang_submit|escape}>"></div>
        </form>
    <{/if}>
</fieldset>
