<link rel="stylesheet" href="<{xoAppUrl 'modules/contact/assets/css/contact.css'}>" type="text/css" />

<{if !empty($recaptcha)}>
<script src='https://www.google.com/recaptcha/api.js'></script>
<{/if}>

<{if !empty($show_breadcrumbs)}>
	<ol class="breadcrumb">
		<li class="breadcrumb-item active"><a href="<{$xoops_url}>/modules/<{$xoops_dirname}>/index.php"><{$smarty.const._MI_CONTACT_NAME}></a></li>
    </ol>
<{/if}>

<{if !empty($info)}>
<div id="about" class="center bg-contact" style="padding-bottom: 20px; padding-top: 5px;">
	<{$info}>
</div>
<{/if}>

<{if !empty($contact_default)}>
<div id="contact-default" class="col">
	<{$contact_default}>
</div>
 <{/if}>

<div class="container">
<div class="row">
	<div class="col">
		<div id="contact-form" class="col">

	<form name="save" id="save" action="<{xoAppUrl 'modules/contact/send.php'}>" onsubmit="return xoopsFormValidate_save();" method="post" enctype="multipart/form-data">
        <{!empty(securityToken)}><{*//mb*}>
		<div class="form-group">
	    	<label for="contact_name"><{$lng_username|default:''}></label>
	    	<input type="text" class="form-control" id="contact_name" name="contact_name" placeholder="<{$lng_username_info|default:''}>">
	  	</div>
	  	<div class="form-group">
	    	<label for="contact_mail"><{$lng_email|default:''}></label>
	    	<input type="text" class="form-control" id="contact_mail" name="contact_mail" placeholder="<{$lng_email_info|default:''}>">
	  	</div>
	  	<{if !empty($url)}>
	  	<div class="form-group">
	    	<label for="contact_url"><{$lng_url|default:''}></label>
	    	<input type="text" class="form-control" id="contact_url" name="contact_url" placeholder="<{$lng_url_info|default:''}>">
	  	</div>
	  	<{/if}>
	  	<{if !empty($company)}>
	  	<div class="form-group">
	    	<label for="contact_company"><{$lng_company|default:''}></label>
	    	<input type="text" class="form-control" id="contact_company" name="contact_company" placeholder="<{$lng_company_info|default:''}>">
	  	</div>
	  	<{/if}>
	  	<{if !empty($address)}>
	  	<div class="form-group">
	    	<label for="contact_address"><{$lng_address|default:''}></label>
	    	<input type="text" class="form-control" id="contact_address" name="contact_address" placeholder="<{$lng_address_info|default:''}>">
	  	</div>
	  	<{/if}>
	  	<{if !empty($location)}>
	  	<div class="form-group">
	    	<label for="contact_location"><{$lng_location|default:''}></label>
	    	<input type="text" class="form-control" id="contact_location" name="contact_location" placeholder="<{$lng_location_info|default:''}>">
	  	</div>
	  	<{/if}>
	  	<{if !empty($phone)}>
	  	<div class="form-group">
	    	<label for="contact_phone"><{$lng_phone|default:''}></label>
	    	<input type="text" class="form-control" id="contact_phone" name="contact_phone" placeholder="<{$lng_phone_info|default:''}>">
	  	</div>
	  	<{/if}>
        <{if !empty($icq)}>
	  	<div class="form-group">
	    	<label for="contact_icq"><{$lng_icq|default:''}></label>
	    	<input type="text" class="form-control" id="contact_icq" name="contact_icq" placeholder="<{$lng_icq_info|default:''}>">
	  	</div>
	  	<{/if}>
	  	<{if !empty($skype)}>
	  	<div class="form-group">
	    	<label for="contact_skype"><{$lng_skypename|default:''}></label>
	    	<input type="text" class="form-control" id="contact_skype" name="contact_skype" placeholder="<{$lng_skypename_info|default:''}>">
	  	</div>
	  	<{/if}>
	  	<{if !empty($depart)}>
	  	<div class="form-group">
	  	<label for="contact_department"><{$lng_department}></label>
	  	<select type="text" class="form-control" name="contact_department">
	  		<{foreach item=department from=$departments|default:null}>
	  			<{html_options values=$department output=$department selected=$department}>
			<{/foreach}>
		</select>
		</div>
	  	<{/if}>
	  	<div class="form-group">
	    	<label for="contact_subject"><{$lng_subject}></label>
	    	<input type="text" class="form-control" id="contact_subject" name="contact_subject" placeholder="<{$lng_subject_info}>">
	  	</div>
	  	<div class="form-group">
	    	<label for="contact_message"><{$lng_message}></label>
	  		<textarea name="contact_message" id="contact_message" class="form-control" rows="3" placeholder="<{$lng_message_info}>"></textarea>
	  	</div>

		<input type="hidden" name="op" id="op" value="save">
	  	<input type="hidden" name="contact_id" id="contact_id" value="">
	  	<input type="hidden" name="contact_uid" id="contact_uid" value="<{$contact_uid}>">

	  	<{if !empty($recaptcha)}>
	  	<div class="g-recaptcha" data-sitekey="<{$recaptchakey}>"></div>
	  	<{/if}>
	  	<div class="center">
            <input type="submit" class="btn btn-primary center" name="submit" id="submit" value="<{$lng_submit}>" title="<{$lng_submit}>" style="margin: 10px 0;" >
        </div>
	</form>

	</div>
	</div>
	<{if !empty($map)}>
	<div class="w-100 d-lg-none"></div>
	<div class="col">
		<{$map}>
	</div>
<{/if}>
</div>
</div>
<!-- Start Form Validation JavaScript //-->
<script type='text/javascript'>

<!--//
function xoopsFormValidate_save() { var myform = window.document.save;
if (myform.contact_name.value == "") { window.alert("<{$lng_username_info|default:''}>"); myform.contact_name.focus(); return false; }

if (myform.contact_mail.value == "") { window.alert("<{$lng_email_info|default:''}>"); myform.contact_mail.focus(); return false; }

if (myform.contact_subject.value == "") { window.alert("<{$lng_subject_info|default:''}>"); myform.contact_subject.focus(); return false; }

if (myform.contact_message.value == "") { window.alert("<{$lng_message_info|default:''}>"); myform.contact_message.focus(); return false; }
return true;
}
//--></script>
