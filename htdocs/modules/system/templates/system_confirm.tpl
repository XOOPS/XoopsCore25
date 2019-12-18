<div class="confirmMsg"><{$msg}><br>
    <form method="post" action="<{$action}>">
	<{$hiddens}>
<{if ($addtoken)}>
    <{$token}>
<{/if}>
    <input type="submit" class="btn btn-default" name="confirm_submit" value="<{$submit}>" title="<{$submit}>"/>&nbsp;<input type="button" class="btn btn-default" name="confirm_back" value="<{$smarty.const._CANCEL}>" onclick="history.go(-1);" title="<{$smarty.const._CANCEL}>" />
    </form>
</div>