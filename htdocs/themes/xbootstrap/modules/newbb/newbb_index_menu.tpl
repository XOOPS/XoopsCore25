<select name="mainoption" id="mainoption" class="form-control pull-right" style="max-width: 250px" 
        onchange="if(this.options[this.selectedIndex].value.length >0 ) { window.document.location=this.options[this.selectedIndex].value;}">
    <option value=""><{$smarty.const._MD_MAINFORUMOPT}></option>
    <option value="<{$mark_read}>"><{$smarty.const._MD_MARK_ALL_FORUMS}>&nbsp;<{$smarty.const._MD_MARK_READ}></option>
    <option value="<{$mark_unread}>"><{$smarty.const._MD_MARK_ALL_FORUMS}>
        &nbsp;<{$smarty.const._MD_MARK_UNREAD}></option>
    <option value="">--------</option>
    <option value="<{$post_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALLPOSTS}></option>
    <option value="<{$newpost_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_NEWPOSTS}></option>
    <option value="<{$all_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_ALL}></option>
    <option value="<{$digest_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_DIGEST}></option>
    <option value="<{$unreplied_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_UNREPLIED}></option>
    <option value="<{$unread_link}>"><{$smarty.const._MD_VIEW}>&nbsp;<{$smarty.const._MD_UNREAD}></option>
    <{if $forum_index_cpanel}>
        <option value="">--------</option>
        <option value="<{$forum_index_cpanel.link}>"><{$forum_index_cpanel.name}></option>
    <{/if}>
</select>
