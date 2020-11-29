<form  name="save" id="save" action="index.php" onsubmit="return xoopsFormValidate_save();" method="post" enctype="multipart/form-data">
	<{if $docivility == 1}>
    <div class="form-group">
        <label for="Civility"><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY}> <{if $recivility ==1}><span style="color: red;">*</span><{/if}></label>
		<select class="form-control" id="civility" name="civility" value="<{$request.civility}>">
			<option></option>
			<option><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY_OPT1}></option>
			<option><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY_OPT2}></option>
			<option><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY_OPT3}></option>
		</select>
    </div>
	<{/if}>
	<{if $doname == 1}>
    <div class="form-group">
        <label for="Name"><{$smarty.const._MD_XMCONTACT_INDEX_NAME}> <{if $rename == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_NAME_PH}>" value="<{$request.name}>" <{if $rename == 1}>required<{/if}>>
    </div>
	<{/if}>
    <div class="form-group">
        <label for="Email"><{$smarty.const._MD_XMCONTACT_INDEX_EMAIL}> <span style="color: red;">*</span></label>
        <input type="email" class="form-control" id="email" name="email" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_EMAIL_PH}>" value="<{$request.email}>" required>
    </div>
	<{if $dophone == 1}>
    <div class="form-group">
        <label for="Phone"><{$smarty.const._MD_XMCONTACT_INDEX_PHONE}> <{if $rephone == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="tel" class="form-control" id="phone" name="phone" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_PHONE_PH}>" value="<{$request.phone}>" <{if $rephone == 1}>required<{/if}>>
    </div>
	<{/if}>
	<{if $doaddress == 1}>
    <div class="form-group">
        <label for="Address"><{$smarty.const._MD_XMCONTACT_INDEX_ADDRESS}> <{if $readdress == 1}><span style="color: red;">*</span><{/if}></label>
		<textarea class="form-control" id="address" name="address" rows="2" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_ADDRESS_PH}>" <{if $readdress == 1}>required<{/if}>><{$request.address}></textarea>
    </div>
	<{/if}>
	<{if $dourl == 1}>
    <div class="form-group">
        <label for="Url"><{$smarty.const._MD_XMCONTACT_INDEX_URL}> <{if $reurl == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="text" class="form-control" id="url" name="url" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_URL_PH}>" value="<{$request.url}>" <{if $reurl == 1}>required<{/if}>>
    </div>
	<{/if}>
	<{if $dosubject == 1}>
    <div class="form-group">
        <label for="Subject"><{$smarty.const._MD_XMCONTACT_INDEX_SUBJECT}> <{if $resubject == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_SUBJECT_PH}>" value="<{$request.subject}>" <{if $resubject == 1}>required<{/if}>>
    </div>
	<{/if}>
    <div class="form-group">
        <label for="Message"><{$smarty.const._MD_XMCONTACT_INDEX_MESSAGE}> <span style="color: red;">*</span></label>
        <textarea class="form-control" id="message" name="message" rows="5" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_MESSAGE_PH}>" required><{$request.message}></textarea>
    </div>
    <{if $captcha|default:false}>
    <label for="Message"><{$captcha_caption}> <span style="color: red;">*</span></label>
    <{$block.captcha}>
    <{/if}>
    <div class="form-group text-center">
        <input type="hidden" name="op" id="op" value="save">
        <input type="hidden" name="cat_id" id="cat_id" value="<{$cat_id}>">
		<input type="hidden" name="XOOPS_TOKEN_REQUEST" id="XOOPS_TOKEN_REQUEST" value="<{$token}>" />
        <button type="submit" class="btn btn-primary"><{$smarty.const._MD_XMCONTACT_INDEX_SUBMIT}></button>
    </div>
</form>