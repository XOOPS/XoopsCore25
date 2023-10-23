<{if !empty($tagbar)}>
    <div class="row">
        <div class="col-md-12 xoops-tag-bar">
            <ul class="list-unstyled">
                <li class="tag-title"><{$tagbar.title}>:</li>
                <{foreach item=tag from=$tagbar.tags|default:null}>
                    <li><span class="glyphicon glyphicon-tag"></span> <{$tag}></li>
                <{/foreach}>
            </ul>
        </div><!-- .xoops-tags -->
    </div>
    <!-- .row -->
<{/if}>
