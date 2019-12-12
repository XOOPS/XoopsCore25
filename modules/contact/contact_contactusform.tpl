<div class="contact-module">
    <{$contactform.javascript}>
    <form name="<{$contactform.name}>" action="<{$contactform.action}>" method="<{$contactform.method}>"
            <{$contactform.extra}>>
        <h4><{$contactform.title}></h4>
        <{foreach item=element from=$contactform.elements}>
            <{if $element.hidden != true}>
                <{$element.caption}>
                <{$element.body}>
            <{else}>
                <{$element.body}>
            <{/if}>
        <{/foreach}>
    </form>
</div>
