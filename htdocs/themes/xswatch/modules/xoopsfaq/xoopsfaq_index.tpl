<ol class="breadcrumb">
    <li><a href="index.php"><{$smarty.const._XO_LA_XOOPSFAQ}></a></li>
</ol>

<div class="alert alert-info"><{$smarty.const._XO_LA_TOC}></div>

<ul class="list-group">
    <{foreach item=category from=$categories}>&nbsp;&nbsp;&nbsp;
        <strong><a href="index.php?cat_id=<{$category.id}>">
                <li class="list-group-item"><span class="label label-danger"><span class="glyphicon glyphicon-check"></span>&nbsp;&nbsp;<{$category.name}></span>
                </li>
            </a></strong>
        <!-- start question loop -->
        <{foreach item=question from=$category.questions}>
            <li class="list-group-item">&nbsp;<a href="index.php?cat_id=<{$category.id}>#q<{$question.link}>"><span
                            class="glyphicon glyphicon-hand-right"></span>&nbsp;&nbsp;<{$question.title}></a></li>
        <{/foreach}>
        <!-- end question loop -->
    <{/foreach}>
</ul>

