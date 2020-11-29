<div class="container">
	<{if $error|default:false}>
	<div class="alert alert-danger" role="alert">
		<{$error}>
	</div>
	<{/if}>

	<{if $form|default:false}>
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php"><{$index_module}></a></li>
			<{if $cat_id ==0}>
				<li class="breadcrumb-item active"><{$smarty.const._MD_XMCONTACT_INDEX_FORM}></li>
			<{else}>
				<li class="breadcrumb-item active"><{$category_title}></li>
			<{/if}>
		  </ol>
		</nav>

		<{if $cat_id !=0}>
		<div class="row pt-2 pb-3">
			<div class="col-3 col-md-4 col-lg-3 text-center">
				<img class="rounded img-fluid" src="<{$category_logo}>" alt="<{$category_title}>" class="img-rounded" style="max-height: 150px;">
			</div>
			<div class="col-9 col-md-8 col-lg-9 ">
				<h4 class="mt-0"><{$category_title}></h4>
				<{$category_description}>
			</div>		
		</div>
		<{/if}>
		<{include file="db:xmcontact_form.tpl"}>
	<{/if}>

	<{if $info_header|default:''}>
	<div class="pt-2 pb-3">
		<{$info_header}>
	</div>
	<{/if}>

	<{if $info_googlemaps|default:'' != '' && $info_addresse|default:'' != ''}>
		<div class="row" style="padding-bottom: 5px; padding-top: 5px;">
			<div class="col-md-8 col-sm-12">
				<{$info_googlemaps}>
			</div>
			<div class="col-md-4 col-sm-12">
				<{$info_addresse}>
			</div>
		</div>
	<{else}>
		<{if $info_googlemaps|default:'' != ''}>
			<div class="row" style="padding-bottom: 5px; padding-top: 5px;">
				<div class="col-sm-12">
					<{$info_googlemaps}>
				</div>
			</div>
		<{/if}>
		<{if $info_addresse|default:'' != ''}>
			<div class="row" style="padding-bottom: 5px; padding-top: 5px;">
				<div class="col-sm-12">
					<{$info_addresse}>
				</div>
			</div>
		<{/if}>
	<{/if}>
	<{if $category_count|default:0 != 0}>
	<div class="row">
		<{foreach item=category from=$category}>
		<{if $info_columncat == 1}>
			<div class="col-3 col-md-4 col-lg-3 text-center" style="padding-bottom: 5px; padding-top: 5px;">
				<img class="rounded img-fluid" src="<{$category.logo}>" alt="<{$category.title}>">
			</div>
			<div class="col-9 col-md-8 col-lg-9" style="padding-bottom: 5px; padding-top: 5px;">
				<h4 class="mt-0"><{$category.title}></h4>
				<{$category.description}>
				<br>
				<a href="index.php?op=form&cat_id=<{$category.id}>">
					<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTACT_INDEX_CONTACT}></button>
				</a>
			</div>
		<{/if}>
		<{if $info_columncat == 2}>
			<div class="col-3 col-md-2 col-lg-3 text-center" style="padding-bottom: 5px; padding-top: 5px;">
				<img class="rounded img-fluid" src="<{$category.logo}>" alt="<{$category.title}>">
			</div>
			<div class="col-9 col-md-4 col-lg-3" style="padding-bottom: 5px; padding-top: 5px;">
				<h4 class="mt-0"><{$category.title}></h4>
				<{$category.description}>
				<br>
				<a href="index.php?op=form&cat_id=<{$category.id}>">
					<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTACT_INDEX_CONTACT}></button>
				</a>
			</div>
		<{/if}>
		<{if $info_columncat == 3}>
			<div class="col-3 col-md-2 text-center" style="padding-bottom: 5px; padding-top: 5px;">
				<img class="rounded img-fluid" src="<{$category.logo}>" alt="<{$category.title}>">
			</div>
			<div class="col-9 col-md-2" style="padding-bottom: 5px; padding-top: 5px;">
				<h4 class="mt-0"><{$category.title}></h4>
				<{$category.description}>
				<br>
				<a href="index.php?op=form&cat_id=<{$category.id}>">
					<button type="button" class="btn btn-secondary btn-sm"><{$smarty.const._MD_XMCONTACT_INDEX_CONTACT}></button>
				</a>
			</div>
		<{/if}>
		<{/foreach}>
	</div>
	<{/if}>
	<{if $simple_contact|default:false}>
	<div class="pt-3 pb-2">
		<{include file="db:xmcontact_form.tpl"}>
	</div>
	<{/if}>

	<{if $info_footer|default:''}>
	<div class="pt-3 pb-2">
		<{$info_footer}>
	</div>
	<{/if}>
</div>