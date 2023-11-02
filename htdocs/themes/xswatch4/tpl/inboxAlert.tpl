<{if !$xoops_page|strstr:'viewpmsg' && !$xoops_page|strstr:'readpmsg'}>
    <{xoInboxCount assign='newPms'}>
    <{if isset($newPms) && $newPms > 0}>
    <{* Turn off hide with data-autohide="false" *}>
    <{* Adjust millisecond time to hide in data-delay *}>
    <div data-autohide="true" data-delay="4000" role="alert" aria-live="assertive" aria-atomic="true" class="toast" style="position: absolute; top: 0; left: 0;" >
        <div class="toast-header">
            <span class="fa fa-fw fa-envelope"></span>
            <strong class="mr-auto">&nbsp;<{$smarty.const.THEME_INBOX_ALERT}> <span class="badge badge-primary badge-pill"><{$newPms}></span></strong>
            <small> </small>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <a href="<{$xoops_url}>/viewpmsg.php"><{$smarty.const.THEME_INBOX_LINK}></a>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('.toast').toast('show');
        });
    </script>
    <{/if}>
<{/if}>
