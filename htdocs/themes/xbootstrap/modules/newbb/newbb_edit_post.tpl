<div class="forum_header">
    <div class="forum_title">
        <h2><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$lang_forum_index}></a></h2>
        <!-- irmtfan hardcode removed align="left" -->
        <hr class="align_left" width="50%" size="1"/>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_FORUMINDEX}></a>
        <span class="delimiter">&raquo;</span>
        <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php?cat=<{$category.id}>"><{$category.title}></a>
        <{if $parentforum}>
            <{foreachq item=forum from=$parentforum}>
            <span class="delimiter">&raquo;</span>
            &nbsp;
            <a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum.forum_id}>"><{$forum.forum_name}></a>
        <{/foreach}>
        <{/if}>
        <span class="delimiter">&raquo;</span>
        &nbsp;<a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/viewforum.php?forum=<{$forum_id}>"><{$forum_name}></a>
        <span class="delimiter">&raquo;</span>
        &nbsp;<strong><{$form_title}></strong>
    </div>
</div>
<div class="clear"></div>
<br>

<{if $disclaimer}>
    <div class="confirmMsg"><{$disclaimer}></div>
    <div class="clear"></div>
    <br>
<{/if}>

<{if $error_message}>
    <div class="errorMsg"><{$error_message}></div>
    <div class="clear"></div>
    <br>
<{/if}>

<{if $post_preview}>
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
    <table width='100%' class='outer' cellspacing='1'>
        <{foreachq item=element from=$form_post.elements}>
        <{if $element.hidden != true}>
            <tr valign="top">
                <td class="head">
                    <div class="xoops-form-element-caption<{if $element.required}>-required<{/if}>"><span
                                class="caption-text"><{$element.caption}></span><span class="caption-marker">*</span>
                    </div>
                    <{if $element.description != ''}>
                        <div class="xoops-form-element-help"><{$element.description}></div>
                    <{/if}>
                </td>
                <td class="odd" style="white-space: nowrap;"><{$element.body}></td>
            </tr>
        <{/if}>
        <{/foreach}>
    </table>
    <{foreachq item=element from=$form_post.elements}>
    <{if $element.hidden == true}>
        <{$element.body}>
    <{/if}>
    <{/foreach}>
</form>
<{$form_post.javascript}>
<div class="clear"></div>
<br>

<{if $posts_context}>
    <table width='100%' class='outer' cellspacing='1'>
        <{foreachq item=post from=$posts_context}>
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
