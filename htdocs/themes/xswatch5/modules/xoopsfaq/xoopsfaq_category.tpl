<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.collapsefaq').forEach(function(el) {
            new bootstrap.Collapse(el, {toggle: false}).hide();
        });
        if (window.location.hash) {
            hashChange(window.location.hash);
        }
    });
    window.onhashchange = function () {
        hashChange(window.location.hash);
    };
    function hashChange(hash) {
        document.querySelectorAll('.collapsefaq').forEach(function(el) {
            bootstrap.Collapse.getOrCreateInstance(el).hide();
        });
        setTimeout(function() {
            const hashid = hash.substring(1);
            const target = document.getElementById('collapsefa' + hashid);
            if (target) {
                bootstrap.Collapse.getOrCreateInstance(target).toggle();
            }
            const anchor = document.querySelector(hash);
            if (anchor) {
                anchor.scrollIntoView({behavior: 'smooth'});
            }
        }, 500);
    }
</script>
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="index.php"><{$smarty.const._XO_LA_XOOPSFAQ}></a></li>
    <li class="breadcrumb-item active"><a href="#"><{$category_name}></a></li>
</ol>

<div class="accordion" id="accordionExample">
<{foreach item=question from=$questions|default:null}>
    <div id="q<{$question.id}>" class="card">
        <div class="card-header" id="faqheading<{$question.id}>">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefaq<{$question.id}>" aria-expanded="true" aria-controls="collapseOne">
                    <{$question.title}>
                </button>
            </h2>
        </div>

        <div id="collapsefaq<{$question.id}>" class="collapsefaq show" aria-labelledby="faqheading<{$question.id}>" data-parent="#accordionExample">
            <div class="card-body">
                <{$question.answer}>
            </div>
        </div>
    </div>
<{/foreach}>
</div>

<div style="text-align:center; padding: 3px; margin:3px;">
    <{$commentsnav}>
    <{$lang_notice}><{$smarty.const._XO_LA_BACKTOINDEX}>
</div>

<div style="margin:3px; padding: 3px;">
    <!-- start comments loop -->
    <{if isset($comment_mode)}>
        <{if $comment_mode == "flat"}>
            <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
            <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
            <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    <{/if}>
    <!-- end comments loop -->
</div>
