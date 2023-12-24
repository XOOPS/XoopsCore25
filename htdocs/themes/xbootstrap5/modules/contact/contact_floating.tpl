<!--Button position-->
<div style="position:absolute;top:250px;right:0px;">
    <a id="toggler" href="#"><img src="<{$xoops_imageurl}>images/mail_cont_r.png" style="width:15px;height:75px;" alt="Contact"></a>
</div>

<!--Enter ReCaptcha code https://www.google.com/recaptcha/-->
<{*<{php}>$GLOBALS['xoopsTpl']->assign('recaptchakey', 'Key ReCaptcha');<{/php}>*}>
<!--Opening the block-->
<script type="text/javascript">
    window.onload = function () {
        document.getElementById('toggler').onclick = function () {
            openbox('box', this);
            return false;
        };
    };

    function openbox(id, toggler) {
        var div = document.getElementById(id);
        if (div.style.display == 'block') {
            div.style.display = 'none';
            toggler.innerHTML = '<img src="<{$xoops_imageurl}>images/mail_cont_r.png" style="width:15px;height:75px;" alt="Contact" />';
        }
        else {
            div.style.display = 'block';
            toggler.innerHTML = '<img src="<{$xoops_imageurl}>images/mail_cont_on.png" style="width:75px;height:75px;" alt="Contact" />';
        }
    }

</script>
<!--<script src='https://www.google.com/recaptcha/api.js'></script>-->
<div id="box" style="display: none;">
    <!--Block position-->
    <div style="position:absolute;top:250px;right:80px;padding:5px;width:430px;background-size:cover;background-image: url('<{$xoops_imageurl}>images/post_cont.png')">
        <form name="save" id="save" action="<{xoAppUrl}>modules/contact/send.php" onsubmit="return xoopsFormValidate_save();" method="post" enctype="multipart/form-data">
            <div style="display:inline-block;width:100px;text-align:right;">
                <label for="contact_name"><{$smarty.const._MD_MYTHEME_NAME}></label>
            </div>
            <div style="display:inline-block;">
                <input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="<{$smarty.const._MD_MYTHEME_NAME_INFO}>" style="border:1px solid black;border-radius:8px;">
            </div>
            <br>
            <div style="display:inline-block;width:100px;text-align:right;">
                <label for="contact_mail"><{$smarty.const._MD_MYTHEME_MAIL}></label>
            </div>
            <div style="display:inline-block;">
                <input type="text" class="form-control" id="contact_mail" name="contact_mail" placeholder="<{$smarty.const._MD_MYTHEME_MAIL_INFO}>" style="border:1px solid black;border-radius:8px;">
            </div>
            <br>
            <div style="display:inline-block;width:100px;text-align:right;">
                <label for="contact_subject"><{$smarty.const._MD_MYTHEME_SUBJECT}></label>
            </div>
            <div style="display:inline-block;">
                <input type="text" class="form-control" id="contact_subject" name="contact_subject" placeholder="<{$smarty.const._MD_MYTHEME_SUBJECT_INFO}>" style="border:1px solid black;border-radius:8px;">
            </div>
            <br>
            <div style="display:inline-block;width:100px;text-align:right;">
                <label for="contact_message"><{$smarty.const._MD_MYTHEME_MESSAGE}></label>
            </div>
            <div style="display:inline-block;">
                <textarea name="contact_message" id="contact_message" class="form-control" rows="3" placeholder="<{$smarty.const._MD_MYTHEME_MESSAGE_INFO}>" style="border:1px solid black;border-radius:8px;"></textarea>
            </div>
            <br>
            <input type="hidden" name="op" id="op" value="save">
            <input type="hidden" name="contact_id" id="contact_id" value="">
            <input type="hidden" name="contact_uid" id="contact_uid" value="0">
            <!--<div class="g-recaptcha" data-bs-sitekey="<{$recaptchakey}>" data-bs-callback="YourOnSubmitFn"></div>-->
            <div class="center">
                <input type="submit" class="btn btn-primary center" name="submit" id="submit" value="<{$smarty.const._MD_MYTHEME_SUBMIT}>" title="<{$smarty.const._MD_MYTHEME_SUBMIT}>" style="margin:10px 0;border:1px solid black;border-radius:8px;">
            </div>
        </form>
        <!-- Start Form Validation JavaScript //-->
        <script type="text/javascript">
            <!--//
            function xoopsFormValidate_save() {
                var myform = window.document.save;
                if (myform.contact_name.value == "") {
                    window.alert("<{$smarty.const._MD_MYTHEME_NAME_INFO}>");
                    myform.contact_name.focus();
                    return false;
                }

                if (myform.contact_mail.value == "") {
                    window.alert("<{$smarty.const._MD_MYTHEME_MAIL_INFO}>");
                    myform.contact_mail.focus();
                    return false;
                }

                if (myform.contact_subject.value == "") {
                    window.alert("<{$smarty.const._MD_MYTHEME_SUBJECT_INFO}>");
                    myform.contact_subject.focus();
                    return false;
                }

                if (myform.contact_message.value == "") {
                    window.alert("<{$smarty.const._MD_MYTHEME_MESSAGE_INFO}>");
                    myform.contact_message.focus();
                    return false;
                }
                return true;
            }

            //--></script>
    </div>
</div>
