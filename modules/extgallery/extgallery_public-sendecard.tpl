<form class="form-horizontal" role="form" id="<{$send_ecard.name}>" action="<{$send_ecard.action}>"
      method="<{$send_ecard.method}>" <{$send_ecard.extra}>>
    <h3 class="gallerytitle"><{$send_ecard.title}></h3>

    <img class="pull-right img-thumbnail" src="<{$photo}>" alt="<{$lang.from}>">

    <ul class="list-unstyled form-send-e-card">

        <li><h3 class="gallerytitle"><{$lang.from}>:</h3></li>

        <li><{$send_ecard.elements.ecard_fromname.caption}></li>

        <li><{$send_ecard.elements.ecard_fromname.body}></li>

        <li><{$send_ecard.elements.ecard_fromemail.caption}></li>

        <li><{$send_ecard.elements.ecard_fromemail.body}></li>

        <li><h3 class="gallerytitle"><{$lang.to}>:</h3></li>

        <li><{$send_ecard.elements.ecard_toname.caption}></li>
        <li><{$send_ecard.elements.ecard_toname.body}></li>

        <li><{$send_ecard.elements.ecard_toemail.caption}></li>
        <li><{$send_ecard.elements.ecard_toemail.body}></li>

        <li><{$send_ecard.elements.ecard_greetings.caption}></li>

        <li><{$send_ecard.elements.ecard_greetings.body}></li>

        <li><{$send_ecard.elements.ecard_desc.caption}></li>

        <li><{$send_ecard.elements.ecard_desc.body}></li>

        <{if $send_ecard.elements.captcha.body}>
            <li><{$send_ecard.elements.captcha.caption}></li>
            <li><img src="<{xoAppUrl modules/extgallery/}>assets/images/captcha.php" alt="captcha"></li>
            <li><{$send_ecard.elements.captcha.body}></li>
        <{/if}>
        <{$send_ecard.elements.step.body}>
        <{$send_ecard.elements.photo_id.body}>
        <li class="aligncenter"><{$send_ecard.elements.submit.body}></li>

    </ul>
</form>
