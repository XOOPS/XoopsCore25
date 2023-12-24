<{if !$xoops_page|strstr:'viewpmsg' && !$xoops_page|strstr:'readpmsg'}>
    <{xoInboxCount assign='newPms'}>
    <{if $newPms>0}>
    <{* Turn off hide with data-bs-autohide="false" *}>
    <{* Adjust millisecond time to hide in data-bs-delay *}>
    
        <div class="position-fixed top-0 start-0 p-3" style="z-index: 5">   
            <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="4000">
			<div class="toast-header">
				<span class="fa fa-fw fa-envelope"></span>
				<strong class="me-auto">&nbsp;<{$smarty.const.THEME_INBOX_ALERT}> <span class="badge bg-primary rounded-pill"><{$newPms}></span></strong>
                    <small></small>
				<button type="button" class="btn-btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
			</div>
                <div class="toast-body bg-secondary text-center">
                   <a class="btn btn-primary" href="<{$xoops_url}>/viewpmsg.php" role="button"><{$smarty.const.THEME_INBOX_LINK}></a>
			</div>
		</div>
        </div>
		<script>
			$(document).ready(function(){
				$('.toast').toast('show');
			});
		</script>
    <{/if}>
<{/if}>
