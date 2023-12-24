<div class="xmcontent">
    <{if $form|default:false}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
			<li class="breadcrumb-item active" aria-current="page"><{$smarty.const._AM_XMCONTENT_EDIT}></li>
		  </ol>
		</nav>
		<{if $error_message|default:false}>
			<div class="alert alert-danger" role="alert"><{$error_message}></div>
		<{/if}>
		<{if $message_tips|default:false == true}>
			<div class="alert alert-info"><{$smarty.const._AM_XMCONTENT_CONTENT_TIPS}></div>
		<{/if}>
        <div class="xmform">
            <{$form}>
        </div>
    <{/if}>    
</div><!-- .xmcontente -->