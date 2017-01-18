<form method="get" action="<{$pageName}>">
    <table width="95%" class="outer" cellspacing="1">
        <tr>
            <td class="even" align="center">
                <{$commentModeSelect->render()}>
                <{$commentOrderSelect->render()}>
                <{$commentRefreshButton->render()}>
                <{if ($commentPostButton|default:false) }>
                <{$commentPostButton->render()}>
                <{/if}>
                <{$commentPostHidden}>
            </td>
        </tr>
    </table>
</form>
