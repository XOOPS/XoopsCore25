<form action="field.php" method="post" id="fieldform">
    <table>
        <th><{$smarty.const._PROFILE_AM_NAME}></th>
        <th><{$smarty.const._PROFILE_AM_TITLE}></th>
        <th><{$smarty.const._PROFILE_AM_DESCRIPTION}></th>
        <th><{$smarty.const._PROFILE_AM_TYPE}></th>
        <th><{$smarty.const._PROFILE_AM_CATEGORY}></th>
        <th><{$smarty.const._PROFILE_AM_WEIGHT}></th>
        <th><{$smarty.const._PROFILE_AM_REQUIRED}></th>
        <th><{$smarty.const._PROFILE_AM_ACTION}></th>
        <{foreach item=category from=$fieldcategories}>
            <{foreach item=field from=$category}>
                <tr class="<{cycle values='odd, even'}>">
                    <td><{$field.field_name}></td>
                    <td><{$field.field_title}></td>
                    <td><{$field.field_description}></td>
                    <td><{$field.fieldtype}></td>
                    <td>
                        <{if $field.canEdit}>
                            <select name="category[<{$field.field_id}>]"><{html_options options=$categories selected=$field.cat_id}></select>
                        <{/if}>
                    </td>
                    <td>
                        <{if $field.canEdit}>
                            <input type="text" name="weight[<{$field.field_id}>]" size="5" maxlength="5" value="<{$field.field_weight}>"/>
                        <{/if}>
                    </td>
                    <td align="center">
                        <{if $field.canEdit}>
                            <a href="field.php?op=toggle&amp;field_required=<{$field.field_required}>&amp;field_id=<{$field.field_id}>"><img
                                        src="<{xoModuleIcons16}><{$field.field_required}>.png" title="<{$smarty.const._PROFILE_AM_REQUIRED_TOGGLE}>"
                                        alt="<{$smarty.const._PROFILE_AM_REQUIRED_TOGGLE}>"/></a>
                        <{/if}>
                    </td>
                    <td align="center">
                        <{if $field.canEdit}>
                            <input type="hidden" name="oldweight[<{$field.field_id}>]" value="<{$field.field_weight}>"/>
                            <input type="hidden" name="oldcat[<{$field.field_id}>]" value="<{$field.cat_id}>"/>
                            <input type="hidden" name="field_ids[]" value="<{$field.field_id}>"/>
                            <a href="field.php?id=<{$field.field_id}>" title="<{$smarty.const._EDIT}>"><img src="<{xoModuleIcons16 edit.png}>"
                                                                                                            alt="<{$smarty.const._EDIT}>"
                                                                                                            title="<{$smarty.const._EDIT}>"/></a>
                        <{/if}>
                        <{if $field.canDelete}>
                            &nbsp;
                            <a href="field.php?op=delete&amp;id=<{$field.field_id}>" title="<{$smarty.const._DELETE}>"><img
                                        src="<{xoModuleIcons16 delete.png}>" alt="<{$smarty.const._DELETE}>" title="<{$smarty.const._DELETE}>"</a>
                        <{/if}>
                    </td>
                </tr>
            <{/foreach}>
        <{/foreach}>
        <tr class="<{cycle values='odd, even'}>">
            <td colspan="5">
            </td>
            <td>
                <{$token}>
                <input type="hidden" name="op" value="reorder"/>
                <input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"/>
            </td>
            <td colspan="2">
            </td>
        </tr>
    </table>
</form>
