<ol class="breadcrumb">
    <li><a href="index.php"><{$smarty.const._XO_LA_XOOPSFAQ}></a></li>
    <li><a href="index.php"><{$smarty.const._XO_LA_MAIN}></a></li>
    <li><a href="#"><{$category_name}></a></li>
</ol>


<ul class="list-group">
    <!-- start question loop -->
    <{foreach item=question from=$questions}>
        <li class="list-group-item"><a href="#q<{$question.id}>"><span class="glyphicon glyphicon-hand-right"></span>&nbsp;&nbsp;<{$question.title}></a>
        </li>
    <{/foreach}>
    <!-- end question loop -->
</ul>


<!-- start question and answer loop -->
<{foreach item=question from=$questions}>
    <div class="panel panel-default">
        <div class="panel-heading"><a id="q<{$question.id}>" name="q<{$question.id}>"></a><span class="label label-danger"><{$question.title}></span>
        </div>
        <div class="panel-body">
            <td class="even"><{$question.answer}>
                <div style="text-align: right;"><a href="#top"><span class="label label-primary"><{$smarty.const._XO_LA_BACKTOTOP}></span></a></div>
            </td>
        </div>
    </div>
<{/foreach}>
<!-- end question and answer loop -->

<br><br>
<div style="text-align:center;"><b>[ <a href="index.php"><{$smarty.const._XO_LA_BACKTOINDEX}></a> ]</b></div>

<div style="text-align:center; padding: 3px; margin:3px;">
    <{$commentsnav}>
    <{$lang_notice}><{$smarty.const._XO_LA_BACKTOINDEX}>
</div>

<div style="margin:3px; padding: 3px;">
    <!-- start comments loop -->
    <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
    <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
    <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
    <{/if}>
    <!-- end comments loop -->
</div>
