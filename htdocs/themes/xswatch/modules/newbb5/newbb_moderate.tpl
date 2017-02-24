<div class="newbb">
    <ul class="breadcrumb">
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_NEWBB_FORUMHOME}></a></li>
        <li><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/<{$moderate_url}>"><{$smarty.const._MD_NEWBB_SUSPEND_MANAGEMENT}></a></li>
    </ul>
</div>

<{if $error_message}>
    <div class="errorMsg"><{$error_message}></div>
    <div class="clear"></div>
    <br>
<{/if}>

<h3><{$smarty.const._MD_NEWBB_SUSPEND_LIST}></h3>
<div class="table-responsive">
    <table class="table table-hover">
    <thead>
    <tr>
    <{foreach item=colHead from=$columnHeaders}>
        <th>
            <{if $colHead.url}>
            <a href="<{$colHead.url}>" title="<{$colHead.title}>"><{$colHead.header}></a>
            <{else}>
            <{$colHead.header}>
            <{/if}>
        </th>
    <{/foreach}>
    </tr>
    </thead>
    <tbody>
    <{foreach item=row from=$columnRows}>
        <tr>
            <td><{$row.uid}></td>
            <td><{$row.start}></td>
            <td><{$row.expire}></td>
            <td><{$row.forum}></td>
            <td><{$row.desc}></td>
            <td><{$row.options}></td>
        </tr>
    <{/foreach}>
    </tbody>
    </table>
</div>
<div class="icon_right">
    <{$moderate_page_nav|default:''|replace:'form':'div'|replace:'id="xo-pagenav"':''}>
</div>


<br>
<h3><{$suspend_form.title}></h3>
<hr class="align_left" width="100%" size="1"/>
<form name="<{$suspend_form.name}>" id="<{$suspend_form.name}>" action="<{$suspend_form.action}>" method="<{$suspend_form.method}>" <{$suspend_form.extra}> >
    <{foreach item=element from=$suspend_form.elements}>
    <{if $element.hidden != true}>
    <{if $element.name === 'submit'}>
    <button name="submit" type="submit" class="btn btn-default"><{$smarty.const._SUBMIT}></button>
    <{else}>
    <div class="form-group">
        <label for="<{$element.name}>"><{$element.caption}><{if $element.required}><span class="text-info">*</span><{/if}></label>
        <{$element.body|replace:'<input ':'<input class="form-control" '|replace:'<select ':'<select class="form-control" '}>
        <{if $element.description != ''}>
        <span class="help-block"><{$element.description}></span>
        <{/if}>
    </div>
    <{/if}>
    <{/if}>
    <{/foreach}>
    <{foreachq item=element from=$suspend_form.elements}>
    <{if $element.hidden == true}>
        <{$element.body}>
    <{/if}>
    <{/foreach}>
</form>
<{$suspend_form.javascript}>
<div class="clear"></div>
<br>
