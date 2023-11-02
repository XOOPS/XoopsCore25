<{include file="db:profile_breadcrumbs.tpl"}>
<div>( <{$total_users}> )</div>
<{if !empty($users)}>
    <table>
        <tr>
            <{foreach item=caption from=$captions|default:null}>
                <th><{$caption}></th>
            <{/foreach}>
        </tr>
        <{foreach item=user from=$users|default:null}>
            <tr class="<{cycle values='odd, even'}>">
                <{foreach item=fieldvalue from=$user.output|default:null}>
                    <td><{$fieldvalue}></td>
                <{/foreach}>
            </tr>
        <{/foreach}>
    </table>
    <{$nav|default:''}>
<{else}>
    <div class="errorMsg">
        <{$smarty.const._PROFILE_MA_NOUSERSFOUND}>
    </div>
<{/if}>
