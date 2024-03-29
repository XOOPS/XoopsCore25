<{include file="db:profile_breadcrumbs.tpl"}>

<{if $steps|@count > 1 AND $current_step >= 0}>
    <div class='register-steps'>
        <span class='caption'><{$lang_register_steps}></span>
        <{foreach item=step from=$steps|default:null key=stepno name=steploop}>
            <{if $stepno == $current_step}>
                <span class='item current'><{$step.step_name}></span>
            <{else}>
                <span class='item'><{$step.step_name}></span>
            <{/if}>
            <{if !$smarty.foreach.steploop.last}>
                <span class='delimiter'>&raquo;</span>
            <{/if}>
        <{/foreach}>
    </div>
<{/if}>

<{if isset($stop)}>
	<div class="alert alert-danger" role="alert">
		<{$stop}>
    </div>
<{/if}>

<{if isset($confirm)}>
    <{foreach item=msg from=$confirm|default:null}>
        <div class="alert alert-primary" role="alert">
			<{$msg}>
		</div>
    <{/foreach}>
<{/if}>

<{if isset($regform)}>
    <{$regform.rendered}>
<{*
    <h3><{$regform.title}></h3>
    <{include file="db:profile_form.tpl" xoForm=$regform}>
*}>
<{elseif $finish}>
    <h1><{$finish}></h1>
    <{if isset($finish_message)}><p><{$finish_message}></p><{/if}>
    <{if isset($finish_login)}>
    <form id='register_login' name='register_login' action='user.php' method='post'>
    <input type='submit' value="<{$finish_login}>">
    <input type='hidden' name="op" id="op" value="login">
    <input type='hidden' name="uname" id="uname" value="<{$finish_uname}>">
    <input type='hidden' name="pass" id="pass" value="<{$finish_pass}>">
    </form>
    <{/if}>
<{/if}>
