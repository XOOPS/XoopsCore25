<form  name="save" id="save" action="<{$xoops_url}>/modules/xmcontact/index.php" onsubmit="return xoopsFormValidate_save();" method="post" enctype="multipart/form-data">
	<{if $block.docivility == 1}>
    <div class="form-group">
        <label for="Civility"><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY}> <{if $block.recivility ==1}><span style="color: red;">*</span><{/if}></label>
		<select class="form-control" id="civility" name="civility" value="<{$block.request.civility}>">
			<option></option>
			<option><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY_OPT1}></option>
			<option><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY_OPT2}></option>
			<option><{$smarty.const._MD_XMCONTACT_INDEX_CIVILITY_OPT3}></option>
		</select>
    </div>
	<{/if}>
	<{if $block.doname == 1}>
    <div class="form-group">
        <label for="Name"><{$smarty.const._MD_XMCONTACT_INDEX_NAME}> <{if $block.rename == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="text" class="form-control" id="name" name="name" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_NAME_PH}>" value="<{$block.request.name}>" <{if $block.rename == 1}>required<{/if}>>
    </div>
	<{/if}>
    <div class="form-group">
        <label for="Email"><{$smarty.const._MD_XMCONTACT_INDEX_EMAIL}> <span style="color: red;">*</span></label>
        <input type="email" class="form-control" id="email" name="email" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_EMAIL_PH}>" value="<{$block.request.email}>" required>
    </div>
	<{if $block.dophone == 1}>
    <div class="form-group">
        <label for="Phone"><{$smarty.const._MD_XMCONTACT_INDEX_PHONE}> <{if $block.rephone == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="tel" class="form-control" id="phone" name="phone" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_PHONE_PH}>" value="<{$block.request.phone}>" <{if $block.rephone == 1}>required<{/if}>>
    </div>
	<{/if}>
	<{if $block.doaddress == 1}>
    <div class="form-group">
        <label for="Address"><{$smarty.const._MD_XMCONTACT_INDEX_ADDRESS}> <{if $block.readdress == 1}><span style="color: red;">*</span><{/if}></label>
		<textarea class="form-control" id="address" name="address" rows="2" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_ADDRESS_PH}>" <{if $block.readdress == 1}>required<{/if}>><{$block.request.address}></textarea>
    </div>
	<{/if}>
	<{if $block.dourl == 1}>
    <div class="form-group">
        <label for="Url"><{$smarty.const._MD_XMCONTACT_INDEX_URL}> <{if $block.reurl == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="text" class="form-control" id="url" name="url" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_URL_PH}>" value="<{$block.request.url}>" <{if $block.reurl == 1}>required<{/if}>>
    </div>
	<{/if}>
	<{if $block.dosubject == 1}>
    <div class="form-group">
        <label for="Subject"><{$smarty.const._MD_XMCONTACT_INDEX_SUBJECT}> <{if $block.resubject == 1}><span style="color: red;">*</span><{/if}></label>
        <input type="text" class="form-control" id="subject" name="subject" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_SUBJECT_PH}>" value="<{$block.request.subject}>" <{if $block.resubject == 1}>required<{/if}>>
    </div>
	<{/if}>
    <div class="form-group">
        <label for="Message"><{$smarty.const._MD_XMCONTACT_INDEX_MESSAGE}> <span style="color: red;">*</span></label>
        <textarea class="form-control" id="message" name="message" rows="5" placeholder="<{$smarty.const._MD_XMCONTACT_INDEX_MESSAGE_PH}>" required><{$block.request.message}></textarea>
    </div>
    <{if $block.captcha|default:false}>
    <label for="Message"><{$block.captcha_caption}> <span style="color: red;">*</span></label>
    <{$captcha}>
    <{/if}>
    <div class="form-group text-center">
        <input type="hidden" name="op" id="op" value="save">
        <input type="hidden" name="cat_id" id="cat_id" value="<{$block.cat_id}>">
        <input type="hidden" name="contact_redirect" id="contact_redirect" value="<{$xoops_url}><{$xoops_requesturi}>">
		<input type="hidden" name="XOOPS_TOKEN_REQUEST" id="XOOPS_TOKEN_REQUEST" value="<{$block.token}>" />
        <button type="submit" class="btn btn-primary"><{$smarty.const._MD_XMCONTACT_INDEX_SUBMIT}></button>
    </div>
</form>