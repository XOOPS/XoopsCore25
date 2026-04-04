<div class="alert alert-primary" role="alert">
    <div class="d-flex flex-wrap align-items-center gap-3">
        <div class="flex-grow-1"><{$msg}></div>
        <form class="d-inline-flex gap-2" method="post" action="<{$action}>">
            <{$hiddens}>
            <{if ($addtoken)}>
                <{$token}>
            <{/if}>
            <input class="btn btn-primary" type="submit" name="confirm_submit" value="<{$submit}>" title="<{$submit}>">
            <input class="btn btn-secondary" name="confirm_back" type="button" value="<{$smarty.const._CANCEL}>" onclick="history.go(-1);" title="<{$smarty.const._CANCEL}>">
        </form>
    </div>
</div>
