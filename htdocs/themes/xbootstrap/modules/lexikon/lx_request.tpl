<ol class="breadcrumb">
  <li><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a></li>
  <li><{$smarty.const._MD_LEXIKON_ASKFORDEF}></li>
</ol>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h4><{$smarty.const._MD_LEXIKON_ASKFORDEF}></h4>
      </div>
      <div class="panel-body">
        <p><{$smarty.const._MD_LEXIKON_INTROREQUEST}></p>
      </div>
    </div>
  </div>
</div>
<div class="row" >
  <div class="col-md-6 col-sm-12">
    <{$requestform.javascript}>
    <h3><{$requestform.title}></h3>
    <form id="sub-lex" name="<{$requestform.name}>" action="<{$requestform.action}>" method="<{$requestform.method}>" <{$requestform.extra}>>
      <{foreach item=element from=$requestform.elements}>
        <{if $element.hidden != true}>
        <div class="form-group">
          <label><{$element.caption}></label>
          <{$element.body}>
        </div>
        <{else}>
          <{$element.body}>
        <{/if}>
      <{/foreach}>


    </form>
  </div>
</div>
<script type="text/javascript">
$('#sub-lex input[type=text]').each(function(){
  $( this ).addClass( "form-control" );
});
$('input[type=submit]').addClass( "btn btn-success btn-sm" );
</script>
