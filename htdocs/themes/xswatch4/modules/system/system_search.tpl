<{if !empty($results)}>
	<h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
	<{$smarty.const._SR_KEYWORDS}>: <mark><{$keywords}></mark>
	<br>
	<{if !empty($error_length)}>
		<{$error_length}> <strong><{$error_keywords}></strong>
		<br>
	<{/if}>
	<{if !empty($nomatch)}>
		<br>
		<{$nomatch}>
		<br>
	<{else}>
		<{foreach item=searchitem from=$search|default:null}>
			<div class="card my-3">
				<div class="card-header">
					<h5>
						<{$searchitem.module_name}>
						<{if !empty($searchitem.module_show_all)}>
							<span class="d-none d-sm-inline"><span class="x-small">| <a href="<{$searchitem.module_show_all}>"><{$smarty.const._SR_SHOWALLR}></a></span></span>
							<span class="d-inline d-sm-none">| <span class="ml-2"></span><a href="<{$searchitem.module_show_all}>"><span class="fa fa-search-plus fa-flip-horizontal fa-lg"></span></a></span>
						<{/if}>
					</h5>
				</div>

				<div class="card-body">
					<ul class="list-group list-group-flush">
						<{foreach item=data from=$searchitem.module_data|default:null}>
							<li class="list-group-item list-group-item-action">
	<!-- Alain01 -->
								<{assign var="url_image_overloaded" value=$xoops_imageurl|cat:$data.image_link}>
								<{assign var="path_image_overloaded" value=$xoops_rootpath|cat:"/themes/"|cat:$xoops_theme|cat:"/"|cat:$data.image_link}>
								<{if file_exists($path_image_overloaded)}>
									<div class="d-inline"><img src="<{$url_image_overloaded}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a></div>
								<{else}>
									<div class="d-inline"><img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a></div>
								<{/if}>

								<{if !empty($data.uname)}>

									<div class="d-none d-md-inline">
										<br />
										<small><span class="fa fa-user fa-sm ml-2 text-muted"></span> <a href="<{$data.uname_link}>"><{$data.uname}></a></small>

										<{if $data.time}>
											<small><span class="text-muted"><span class="fa fa-calendar fa-sm ml-2"></span> <{$data.time}></span></small>
										<{/if}>
									</div>
								<{/if}>
							</li>
						<{/foreach}>
					</ul>
				</div>
			</div>
		<{/foreach}>
	<{/if}>
<{/if}>
<{if !empty($showallbyuser)}>
    <h3><{$smarty.const._SR_SEARCHRESULTS}></h3>
    <{if isset($nomatch) ? $nomatch!= true : true}>
        <{if !empty($showall)}>
            <{$smarty.const._SR_KEYWORDS}>:
            <mark><{$keywords}></mark>
            <br>
        <{/if}>

		<div class="card my-3">
			<div class="card-header">
				<div class="d-flex justify-content-between">
					<div>
						<h5><{$module_name}></h5>
						<{$showing|replace:"-":"- "}>
					</div>
					<{if $previous || $next}>
						<div>
						<{if isset($previous)}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary" href="<{$previous}>" role="button"><{$smarty.const._SR_PREVIOUS|replace:"<<":"<span class='fa fa-chevron-left fa-lg'></span>"}></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary" href="<{$previous}>" role="button"><span class="fa fa-chevron-left"></span></a></span>
						<{else}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary disabled" role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><{$smarty.const._SR_PREVIOUS|replace:"<<":"<span class='fa fa-chevron-left fa-lg'></span>"}></span></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary disabled" role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><span class="fa fa-chevron-left"></span></span></a></span>
						<{/if}>
						<span class="mx-1"></span>
						<{if isset($next)}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary" href="<{$next}>" role="button"><{$smarty.const._SR_NEXT|replace:">>":"<span class='fa fa-chevron-right fa-lg'></span>"}></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary" href="<{$next}>" role="button"><span class="fa fa-chevron-right"></span></a></span>
						<{else}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary disabled"  role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><{$smarty.const._SR_NEXT|replace:">>":"<span class='fa fa-chevron-right fa-lg'></span>"}></span></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary disabled"  role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><span class="fa fa-chevron-right"></span></span></a></span>
						<{/if}>
						</div>
					<{/if}>
				</div>
			</div>
			<div class="card-body">
				<ul class="list-group list-group-flush">
					<{foreach item=data from=$results_arr|default:null}>
						<li class="list-group-item list-group-item-action">
	<!-- Alain01 -->
							<{assign var="url_image_overloaded" value=$xoops_imageurl|cat:$data.image_link}>
							<{assign var="path_image_overloaded" value=$xoops_rootpath|cat:"/themes/"|cat:$xoops_theme|cat:"/"|cat:$data.image_link}>

							<{if file_exists($path_image_overloaded)}>
								<div class="d-inline"><img src="<{$url_image_overloaded}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a></div>
							<{else}>
								<div class="d-inline"><img src="<{$data.image_link}>" title="<{$data.image_title}>" alt="<{$data.image_title}>"/> <a href="<{$data.link}>"><{$data.link_title}></a></div>
							<{/if}>

							<{if $data.uname}>

								<div class="d-none d-md-inline text-muted">
									<br />
									<small><span class="fa fa-user fa-sm ml-2"></span> <a href="<{$data.uname_link}>"><{$data.uname}></a></small>

									<{if $data.time}>
										<small><span class="fa fa-calendar fa-sm ml-2"></span> <{$data.time}></small>
									<{/if}>
								</div>
							<{/if}>
							<br />
						</li>
					<{/foreach}>
				</ul>
			</div>
			<div class="card-footer">
				<div class="d-flex justify-content-between">
					<div>
						<h5><{$module_name}></h5>
						<{$showing|replace:"-":"- "}>
					</div>
					<{if !empty($previous) || !empty($next)}>
						<div>
						<{if !empty($previous)}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary" href="<{$previous}>" role="button"><{$smarty.const._SR_PREVIOUS|replace:"<<":"<span class='fa fa-chevron-left fa-lg'></span>"}></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary" href="<{$previous}>" role="button"><span class="fa fa-chevron-left"></span></a></span>
						<{else}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary disabled" role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><{$smarty.const._SR_PREVIOUS|replace:"<<":"<span class='fa fa-chevron-left fa-lg'></span>"}></span></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary disabled" role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><span class="fa fa-chevron-left"></span></span></a></span>
						<{/if}>
						<span class="mx-1"></span>
						<{if !empty($next)}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary" href="<{$next}>" role="button"><{$smarty.const._SR_NEXT|replace:">>":"<span class='fa fa-chevron-right fa-lg'></span>"}></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary" href="<{$next}>" role="button"><span class="fa fa-chevron-right"></span></a></span>
						<{else}>
							<span class="d-none d-sm-inline"><a class="btn btn-secondary disabled"  role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><{$smarty.const._SR_NEXT|replace:">>":"<span class='fa fa-chevron-right fa-lg'></span>"}></span></a></span>
							<span class="d-inline d-sm-none"><a class="btn btn-secondary disabled"  role="button" tabindex="-1" aria-disabled="true"><span class="text-muted"><span class="fa fa-chevron-right"></span></span></a></span>
						<{/if}>
						</div>
					<{/if}>
				</div>
			</div>

		</div>
	<{else}>
		<p>
			<{$smarty.const._SR_NOMATCH}>
		</p>
	<{/if}>
<{/if}>
<{if !empty($form)}>
	<hr>
	<{$form}>
<{/if}>
