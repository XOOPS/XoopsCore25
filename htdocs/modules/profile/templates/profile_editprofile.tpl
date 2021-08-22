<{include file="db:profile_breadcrumbs.tpl"}>


<{if $stop|default:false}>
    <div class='errorMsg txtleft'><{$stop}></div>
    <br class='clear'/>
<{/if}>

<{include file="db:profile_form.tpl" xoForm=$userinfo}>
