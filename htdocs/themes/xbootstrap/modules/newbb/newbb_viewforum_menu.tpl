<select class="form-control" name="forumoption" id="forumoption" onchange="if(this.options[this.selectedIndex].value.length >0 ) { window.location=this.options[this.selectedIndex].value;}">
    <option value=""><{$smarty.const._MD_FORUMOPTION}></option>
    <option value="<{$mark_read}>"><{$smarty.const._MD_MARK_ALL_TOPICS}>&nbsp;<{$smarty.const._MD_MARK_READ}></option>
    <option value="<{$mark_unread}>"><{$smarty.const._MD_MARK_ALL_TOPICS}>
        &nbsp;<{$smarty.const._MD_MARK_UNREAD}></option>
    <option value="">--------</option>
    <option value="<{$post_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALLPOSTS}></option>
    <option value="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_NEWPOSTS}></option>
    <option value="<{$all_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALL}></option>
    <option value="<{$digest_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_DIGEST}></option>
    <option value="<{$unreplied_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_UNREPLIED}></option>
    <option value="<{$unread_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_UNREAD}></option>
</select>

<{if $typeOptions}>
    <select class="form-control" name="type" id="type" onchange="if(this.options[this.selectedIndex].value.length >0 )    { window.location=this.options[this.selectedIndex].value;}">
        <option value=""><{$smarty.const._MD_NEWBB_TYPE}></option>
        <{foreachq item=opt from=$typeOptions}>
        <option value="<{$opt.link}>"><{$opt.title}></option>
        <{/foreach}>
    </select>
<{/if}>
