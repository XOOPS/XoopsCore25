<div class="tdmdownloads">

    <div class="breadcrumb"><{$navigation}></div>

    <div class="alert alert-success">
        <ul>
            <li><{$smarty.const._MD_TDMDOWNLOADS_RATEFILE_VOTEONCE}></li>
            <li><{$smarty.const._MD_TDMDOWNLOADS_RATEFILE_RATINGSCALE}></li>
            <li><{$smarty.const._MD_TDMDOWNLOADS_RATEFILE_BEOBJECTIVE}></li>
            <li><{$smarty.const._MD_TDMDOWNLOADS_RATEFILE_DONOTVOTE}></li>
        </ul>
    </div>

    <{if $message_erreur != ''}>
        <div class="alert alert-error"><{$message_erreur}></div>
    <{/if}>
    <div class="form-group"><{$themeForm}></div>
</div>
