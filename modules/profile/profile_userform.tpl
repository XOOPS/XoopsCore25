<div class="container-fluid">
	<legend class="bold"><{$lang_login}></legend>
	<form action="user.php" method="post">
		<label for="profile-uname"><{$lang_username}></label>
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa fa-user" aria-hidden="true"></i></span>
			</div>
			<input class="form-control" type="text" name="uname" id="profile-uname" value="" placeholder="<{$smarty.const.THEME_LOGIN}>">
		</div>

		<label for="profile-pass"><{$lang_password}></label>
		<div class="input-group">
			<div class="input-group-prepend">
				<span class="input-group-text"><i class="fa fa-lock" aria-hidden="true"></i></span>
			</div>
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

		<input type="hidden" name="op" value="login"/>
		<input type="hidden" name="xoops_redirect" value="<{$redirect_page}>"/>
		<button type="submit" class="btn btn-secondary"><{$lang_login}></button>
	</form>
	<br>
	<a name="lost"></a>

	<div><{$lang_notregister}><br></div>
	<br>
	<legend class="bold"><{$lang_lostpassword}></legend>
	<p><{$lang_noproblem}></p>
	<form action="lostpass.php" method="post">
		<div class="form-group">
			<div class="input-group mb-3">
				<div class="input-group-prepend">
					<span class="input-group-text" id="mail-addon1">@</span>
				</div>
				<input class="form-control" type="text" name="email" id="profile-lostpass" placeholder="<{$smarty.const.THEME_EMAIL}>" aria-label="<{$smarty.const.THEME_EMAIL}>" aria-describedby="mail-addon1">
			</div>
			<input type="hidden" name="op" value="mailpasswd"/>
			<input type="hidden" name="t"  value="<{$mailpasswd_token}>"/>
			<button type="submit" class="btn btn-secondary"><{$lang_sendpassword}></button>
		</div>
	</form>
</div>


