<{assign var=temp value=0}>
<div class="row clearfix">
    <{foreach item=category from=$categories|default:null}>
    <{assign var=temp value=$temp+1}>
    <{if !$indexpage|default:false}>
    <div class="col-sm-12 col-md-12" style="margin-bottom: 10px;">
        <{else}>
        <div class="col-sm-4 col-md-4" style="margin-bottom: 10px;">
            <{/if}>
            <{if $selected_category|default:0 == $category.categoryid}>
                <h4 class="info"><span class="fa fa-paperclip"></span>&nbsp;
                    <{$category.name}>
                </h4>
            <{else}>
                <h4 class="info"><span class="fa fa-paperclip" style="color:#4087C4;"></span>&nbsp;
                    <{$category.categorylink}>
                </h4>
            <{/if}>
            <div style="display: block;">
                <small><{$category.description}></small>
            </div>


            <{if $category.subcats}>
                <div style="height: 1px; background: #F5F5F5; margin: 5px 0;"></div>
                <{foreach item=subcat from=$category.subcats|default:null}>
                    <small><{$subcat.categorylink}> &nbsp;</small>
                <{/foreach}>
            <{/if}>


        </div>
        <{if $temp%3 == 0}>
            <div class="clearfix"></div>
        <{/if}>
        <{/foreach}>
    </div>
    <hr>
