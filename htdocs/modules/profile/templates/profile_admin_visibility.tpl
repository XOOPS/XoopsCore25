<br>
<div class="head">
    <form id="<{$addform.name}>" method="<{$addform.method}>" action="<{$addform.action}>">
        <{foreach item=element from=$addform.elements|default:null}>
            <{$element.caption}> <{$element.body}>
        <{/foreach}>
    </form>
</div>

<table>
    <{foreach item=field from=$fields|default:null key=field_id}>
        <tr class="<{cycle values='odd,even'}>">
            <td class="width20"><{$field}></td>
            <td>
                <{if isset($visibilities.$field_id)}>
                    <ul>
                        <{foreach item=visibility from=$visibilities.$field_id|default:null}>
                            <{assign var=user_gid value=$visibility.user_group}>
                            <{assign var=profile_gid value=$visibility.profile_group}>
                            <li>
                                <{$smarty.const._PROFILE_AM_FIELDVISIBLEFOR}> <{$groups.$user_gid}>
                                <{$smarty.const._PROFILE_AM_FIELDVISIBLEON}> <{$groups.$profile_gid}>
                                <a href="visibility.php?op=del&amp;field_id=<{$field_id}>&amp;ug=<{$user_gid}>&amp;pg=<{$profile_gid}>"
                                   title="<{$smarty.const._DELETE}>">
                                    <img src="<{$xoops_url}>/modules/profile/assets/images/no.png" alt="<{$smarty.const._DELETE}>"/>
                                </a>
                            </li>
                        <{/foreach}>
                    </ul>
                <{else}>
                    <{$smarty.const._PROFILE_AM_FIELDNOTVISIBLE}>
                <{/if}>
            </td>
        </tr>
    <{/foreach}>
</table>
