<ol class="breadcrumb">
  <li><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a></li>
  <li><{$smarty.const._MD_LEXIKON_SYNDICATION}></li>
</ol>

<div class="row" >
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
          <h4><{$smarty.const._MD_LEXIKON_SYNDICATION}></h4>
      </div>
      <div class="panel-body">
          <{$introcontentsyn}>
      </div>
    </div>
    
    
    <h3><{$yform.title}></h3>
    
    <div class="alert alert-success" role="alert">
        <{*$smarty.const._MD_LEXIKON_SYNEXPLAIN}><br>*}>
        <{$smarty.const._INFO}>:<br>
        <ul>
            <li style='list-style-type:disc;' ;><{$smarty.const._MD_LEXIKON_SYNEXPLAIN1}></li>
            <li style='list-style-type:disc;' ;><{$smarty.const._MD_LEXIKON_SYNEXPLAIN2}></li>
            <li style='list-style-type:disc;' ;><{$smarty.const._MD_LEXIKON_SYNEXPLAIN3}></li>
        </ul>
    </div>
   
    <form name="<{$yform.name}>" action="" method="<{$yform.method}>" <{$yform.extra}> >
        <label><{$yform.elements.txt.caption}></td></label>
        <{$yform.elements.txt.body}>
        <input type=button value="select" style="margin-top: 5px" class="btn btn-success btn-sm" onClick="this.form.txt.focus();this.form.txt.select(); document.execCommand('Copy')" />
    </form>

        <div align="center">
            <h4><{$smarty.const._PREVIEW}></h4><br>
            <IFRAME style="background-color: #FFFFFF;" ; src="<{$xoops_url}>/modules/<{$lang_moduledirname}>/syndication.php"
                    frameborder="0" width="240" height="280" allowtransparency="true" topmargin="0"
                    leftmargin="0" scrolling='no' marginwidth="0" marginheight="0"/>
            [Your user agent does not support frames or is currently configured not to display frames.]
            </IFRAME>
        </div>
           
   
   </div>
</div>

<script type="text/javascript">
$('#txt').addClass( "form-control" );
</script>