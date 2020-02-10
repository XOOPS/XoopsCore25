<div class="alert alert-primary" role="alert">
    <p class="mb-0"><{$msg}></p>
    <hr>

    <form class="form-inline" method="post" action="<{$action}>">
	<{$hiddens}>
<{if ($addtoken)}>
    <{$token}>
<{/if}>
    <input class="form-control btn btn-primary" type="submit" name="confirm_submit" value="<{$submit}>" title="<{$submit}>"/>&nbsp;
    <input class="form-control btn btn-secondary" name="confirm_back" type="button" value="<{$smarty.const._CANCEL}>" onclick="history.go(-1);" title="<{$smarty.const._CANCEL}>" />
    </form>
</div>