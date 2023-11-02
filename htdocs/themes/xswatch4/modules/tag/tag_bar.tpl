<{if !empty($tagbar)}>
    <div class="row">
        <div class="col xoops-tag-bar">
            <ul class="list-unstyled">
                <li class="tag-title"><{$tagbar.title}>:</li>
                <{foreach item=tag from=$tagbar.tags|default:null}>
                    <li><{$tag|replace:"'>":"' > <span class=\"fa fa-hashtag\"></span>"}></li>
                <{/foreach}>
            </ul>
        </div><!-- .xoops-tags -->
    </div>
    <!-- .row -->
<{/if}>
