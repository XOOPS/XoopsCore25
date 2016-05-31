<{includeq file="db:profile_breadcrumbs.tpl"}>

<{if $old_avatar}>
    <div class="pad10 center">
        <h4 class="bold red"><{$smarty.const._US_OLDDELETED}></h4>
        <img src="<{$old_avatar}>" alt="" />
    </div>
<{/if}>

<{if $uploadavatar}>
<{$uploadavatar.javascript}>
<form name="<{$uploadavatar.name}>" action="<{$uploadavatar.action}>" method="<{$uploadavatar.method}>" <{$uploadavatar.extra}>>
  <table class="outer" cellspacing="1">
    <tr>
    <th colspan="2"><{$uploadavatar.title}></th>
    </tr>
    <!-- start of form elements loop -->
    <{foreach item=element from=$uploadavatar.elements}>
      <{if $element.hidden != true}>
      <tr>
        <td class="head"><{$element.caption}>
        <{if $element.description}>
            <div style="font-weight: normal;"><{$element.description}></div>
        <{/if}>
        </td>
        <td class="<{cycle values='even,odd'}>"><{$element.body}></td>
      </tr>
      <{else}>
      <{$element.body}>
      <{/if}>
    <{/foreach}>
    <!-- end of form elements loop -->
  </table>
</form>
<br>
<{/if}>

<br>
<{$chooseavatar.javascript}>
<form name="<{$chooseavatar.name}>" action="<{$chooseavatar.action}>" method="<{$chooseavatar.method}>" <{$chooseavatar.extra}>>
  <table class="outer" cellspacing="1">
    <tr>
    <th colspan="2"><{$chooseavatar.title}></th>
    </tr>
    <!-- start of form elements loop -->
    <{foreach item=element from=$chooseavatar.elements}>
      <{if $element.hidden != true}>
      <tr>
        <td class="head"><{$element.caption}>
        <{if $element.description}>
            <div style="font-weight: normal;"><{$element.description}></div>
        <{/if}>
        </td>
        <td class="<{cycle values='even,odd'}>"><{$element.body}></td>
      </tr>
      <{else}>
      <{$element.body}>
      <{/if}>
    <{/foreach}>
    <!-- end of form elements loop -->
  </table>
</form>
