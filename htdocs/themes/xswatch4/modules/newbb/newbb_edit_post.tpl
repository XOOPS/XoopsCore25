<div>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$lang_forum_index}></a></li>

    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_NEWBB_FORUMHOME}></a></li>

    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.id}>"><{$category.title}></a></li>

    <!-- If is subforum-->
    <{if !empty($parentforum)}>
    <{foreach item=forum from=$parentforum|default:null}>
    <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum.forum_id}>"><{$forum.forum_name}></a></li>
    <{/foreach}>
    <{/if}>

    <li class="breadcrumb-item active"><{$form_title}></li>
</ol>
</div>
<div class="clear"></div>
<br>

<{if !empty($disclaimer)}>
    <div class="confirmMsg"><{$disclaimer}></div>
    <div class="clear"></div>
    <br>
<{/if}>

<{if !empty($error_message)}>
    <div class="errorMsg"><{$error_message}></div>
    <div class="clear"></div>
    <br>
<{/if}>

<{if !empty($post_preview)}>
    <table width='100%' class='outer' cellspacing='1'>
        <tr valign="top">
            <td class="head"><{$post_preview.subject}></td>
        </tr>
        <tr valign="top">
            <td><{$post_preview.meta}><br><br>
                <{$post_preview.content}>
            </td>
        </tr>
    </table>
    <div class="clear"></div>
    <br>
<{/if}>

<form name="<{$form_post.name}>" id="<{$form_post.name}>" action="<{$form_post.action}>"
      method="<{$form_post.method}>" <{$form_post.extra}> >
	  <div class="form-group row">
        <{foreach item=element from=$form_post.elements|default:null}>
        <{assign var="legend_required" value=false}>
        <{if isset($element.hidden) && $element.hidden != true}>
			<label class="col-xs-12 col-sm-2 col-form-label text-sm-right">
				<{$element.caption|default:''}>
                <{if !empty($element.required)}>
                    <span class="xo-caption-required">*</span>
                    <{assign var="legend_required" value=true}>
                <{/if}>
			</label>
			<div class="col-xs-12 col-sm-10">
				<{$element.body}>
				<{if !empty($element.description)}>
					<p class="form-text text-muted"><{$element.description}></p>
				 <{/if}>
			</div>
        <{/if}>
        <{/foreach}>
		</div>
    <{if isset($legend_required) && $legend_required == true}>
        <div class="col-12 mb-2"> <span class="xo-caption-required">*</span> =  <{$smarty.const._REQUIRED}></div>
    <{/if}>
    <{foreach item=element from=$form_post.elements|default:null}>
    <{if isset($element.hidden) && $element.hidden == true}>
        <{$element.body}>
    <{/if}>
    <{/foreach}>
</form>
<{$form_post.javascript}>
<div class="clear"></div>
<br>

<{if !empty($posts_context)}>
    <table width='100%' class='outer' cellspacing='1'>
        <{foreach item=post from=$posts_context|default:null}>
        <tr valign="top">
            <td class="head"><{$post.subject}></td>
        </tr>
        <tr valign="top">
            <td><{$post.meta}><br><br>
                <{$post.content}>
            </td>
        </tr>
        <{/foreach}>
    </table>
<{/if}>
