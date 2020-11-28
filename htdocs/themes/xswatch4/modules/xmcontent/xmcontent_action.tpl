<div class="xmcontent">
    <{if $error_message|default:false}>
        <div class="alert alert-danger" role="alert"><{$error_message}></div>
    <{/if}>
    <{if $form|default:false}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
			<li class="breadcrumb-item active" aria-current="page"><{$smarty.const._AM_XMCONTENT_EDIT}></li>
		  </ol>
		</nav>
        <div class="xmform">
            <{$form}>
        </div>
    <{/if}>    
</div><!-- .xmcontente -->