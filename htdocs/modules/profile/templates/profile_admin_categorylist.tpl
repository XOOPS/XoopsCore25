<table>
    <tr>
        <th><{$smarty.const._PROFILE_AM_TITLE}></th>
        <th><{$smarty.const._PROFILE_AM_DESCRIPTION}></th>
        <th><{$smarty.const._PROFILE_AM_WEIGHT}></th>
        <th><{$smarty.const._PROFILE_AM_ACTION}></th>
    </tr>
    <{foreach item=category from=$categories}>
        <tr class="<{cycle values='odd, even'}>">
            <td><{$category.cat_title}></td>
            <td><{$category.cat_description}></td>
            <td align="center"><{$category.cat_weight}></td>
            <td align="center">
                <a href="category.php?id=<{$category.cat_id}>" title="<{$smarty.const._EDIT}>"><img src="<{xoModuleIcons16 edit.png}>"
                                                                                                    alt="<{$smarty.const._EDIT}>"
                                                                                                    title="<{$smarty.const._EDIT}>"/></a>
                &nbsp;<a href="category.php?op=delete&amp;id=<{$category.cat_id}>" title="<{$smarty.const._DELETE}>"><img
                            src="<{xoModuleIcons16 delete.png}>" alt="<{$smarty.const._DELETE}>" title="<{$smarty.const._DELETE}>"</a>
            </td>
        </tr>
    <{/foreach}>
</table>
