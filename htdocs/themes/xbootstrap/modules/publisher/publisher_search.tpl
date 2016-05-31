<{if $search_info}>
    <div class="alert alert-success">
        <{$search_info}>
    </div>
    <{if $results}>
        <{foreachq item=result from=$results}>
        <div class="item" style="font-size: 12px;">
            <h4 style="margin-bottom: 1px; padding-bottom: 0;"><a href="<{$result.link}>"><{$result.title}></a></h4>
            <{$result.author}> <{$result.datesub}>
            <{if $result.text}>
                <br>
                <{$result.text}>
            <{/if}>
        </div>
        <hr>
        <div class="clear"></div>
    <{/foreach}>
    <{/if}>
<{/if}>

<form name="search" action="search.php" method="post" class="form-horizontal" role="form">
    <div class="form-group">
        <label class="col-sm-2 control-label"><{$smarty.const._SR_KEYWORDS}></label>

        <div class="col-sm-10">
            <input type="text" name="term" class="form-control" value="<{$search_term}>" placeholder="<{$smarty.const._SR_KEYWORDS}>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><{$smarty.const._SR_TYPE}></label>

        <div class="col-sm-10">
            <{$type_select}>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><{$smarty.const._CO_PUBLISHER_CATEGORY}></label>

        <div class="col-sm-10">
            <{$category_select}>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><{$smarty.const._SR_SEARCHIN}></label>

        <div class="col-sm-10">
            <{$searchin_select}>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><{$smarty.const._CO_PUBLISHER_UID}></label>

        <div class="col-sm-10">
            <input type="text" name="uname" class="form-control" value="<{$search_user}>" placeholder="<{$smarty.const._CO_PUBLISHER_UID}>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label"><{$smarty.const._CO_PUBLISHER_SORTBY}></label>

        <div class="col-sm-10">
            <{$sortby_select}>
        </div>
    </div>
    <{if $search_rule}>
        <div class="form-group">
            <label class="col-sm-2 control-label"><{$smarty.const._SR_SEARCHRULE}></label>

            <div class="col-sm-10">
                <p class="help-block"><{$search_rule}></p>
            </div>
        </div>
    <{/if}>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary"><{$smarty.const._SUBMIT}></button>
            <button type="reset" name="cancel" class="btn btn-primary"><{$smarty.const._CANCEL}></button>
        </div>
    </div>

    <!--
  <table class="outer" border="0" cellpadding="1" cellspacing="0" align="center" width="95%">
      <tr>
          <td>
              <table border="0" cellpadding="1" cellspacing="1" width="100%" class="head">

                  <tr>
                      <td class="head" width="10%" align="right">
                          <strong><{$smarty.const._SR_KEYWORDS}></strong></td>
                      <td class="even">
                          <input type="text" name="term" value="<{$search_term}>" size="50"/>
                      </td>
                  </tr>

                  <tr>
                      <td class="head" align="right">
                          <strong><{$smarty.const._SR_TYPE}></strong></td>
                      <td class="even"><{$type_select}></td>
                  </tr>

                  <tr>
                      <td class="head" align="right">
                          <strong><{$smarty.const._CO_PUBLISHER_CATEGORY}></strong>
                      </td>
                      <td class="even"><{$category_select}></td>
                  </tr>

                  <tr>
                      <td class="head" align="right">
                          <strong><{$smarty.const._SR_SEARCHIN}></strong></td>
                      <td class="even"><{$searchin_select}></td>
                  </tr>

                  <tr>
                      <td class="head" align="right">
                          <strong><{$smarty.const._CO_PUBLISHER_UID}></strong>&nbsp;
                      </td>
                      <td class="even">
                          <input type="text" name="uname" value="<{$search_user}>"/>
                      </td>
                  </tr>

                  <tr>
                      <td class="head" align="right">
                          <strong><{$smarty.const._CO_PUBLISHER_SORTBY}></strong>&nbsp;
                      </td>
                      <td class="even"><{$sortby_select}></td>
                  </tr>

                  <{if $search_rule}>
                  <tr>
                      <td class="head" align="right">
                          <strong><{$smarty.const._SR_SEARCHRULE}></strong>&nbsp;
                      </td>
                      <td class="even"><{$search_rule}></td>
                  </tr>
                  <{/if}>

                  <tr>
                      <td class="head" align="right">&nbsp;</td>
                      <td class="even">
                          <input type="submit" name="submit" value="<{$smarty.const._SUBMIT}>"/>&nbsp;
                          <input type="reset" name="cancel" value="<{$smarty.const._CANCEL}>"/>
                      </td>
              </table>
          </td>
      </tr>
  </table>
  -->
</form><!-- end module contents -->
