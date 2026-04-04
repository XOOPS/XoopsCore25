<div class="tdmdownloads">

    <div class="breadcrumb"><{$navigation|replace:'<img src="assets/images/deco/arrow.gif" alt="arrow">':'&nbsp;/&nbsp;'}></div>

    <{if !empty($message_erreur)}>
        <div class="alert alert-error"><{$message_erreur}></div>
    <{/if}>

    <div class="tdm-modify-file"><{$themeForm}></div>

</div>
