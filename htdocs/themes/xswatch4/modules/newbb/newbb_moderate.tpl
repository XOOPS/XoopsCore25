<div class="newbb">
    <ul class="breadcrumb">
        <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MD_NEWBB_FORUMHOME}></a></li>
        <li class="breadcrumb-item active"><{$smarty.const._MD_NEWBB_SUSPEND_MANAGEMENT}></li>
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
            <a href="<{$colHead.url}>" title="<{$colHead.title}>"><{$colHead.header}> <span class="fa fa-sort" aria-hidden="true"></span></a>
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
<hr class="align_left" width="100%" size="1"/>
<{$suspend_form.rendered|replace:'<select name="forum">':'<select class="form-control" id="forum" name="forum">'}>
<div class="clear"></div>
<br>
