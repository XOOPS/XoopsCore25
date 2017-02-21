
<ol class="breadcrumb">
  <li><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a></li>
  <li><{$smarty.const._MD_LEXIKON_SEARCHHEAD}></li>
</ol>

<div class="row">
  <div class="col-md-12">
    <div class="panel panel-info">
      <div class="panel-heading">
        <h4><{$smarty.const._MD_LEXIKON_SEARCHHEAD}></h4>
      </div>
      <div class="panel-body">
        <p><{$intro}></p>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-6 col-xs-12">
    <h3><{$smarty.const._MD_LEXIKON_WEHAVE}></h3>

    <{$smarty.const._MD_LEXIKON_DEFS}><{$publishedwords}><br>
    <{if $multicats == 1}><{$smarty.const._MD_LEXIKON_CATS}><{$totalcats}><br/><{/if}>
    <br/>
    <input class="btn btn-success btn-sm" type="button" value="<{$smarty.const._MD_LEXIKON_SUBMITENTRY}>" onclick="location.href = 'submit.php'"/>
    <input class="btn btn-info btn-sm" type="button" value="<{$smarty.const._MD_LEXIKON_REQUESTDEF}>" onclick="location.href = 'request.php'"/>
  </div>
  <div class="col-md-6 col-xs-12">
    <hr class="visible-sm">
    <h3><{$smarty.const._MD_LEXIKON_SEARCHENTRY}></h3>
    <{$searchform}>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <{foreach item=eachresult from=$resultset.match}>
        <h4><img src="<{$xoops_url}>/modules/<{$eachresult.dir}>/assets/images/lx.png"/>&nbsp;
          <a href="<{$xoops_url}>/modules/<{$eachresult.dir}>/entry.php?entryID=<{$eachresult.id}><{if $highlight == 1}><{$eachresult.keywords}><{/if}>">
            <{$eachresult.term}>
          </a>
          <{if $multicats == 1}>
            <a href="<{$xoops_url}>/modules/<{$eachresult.dir}>/category.php?categoryID=<{$eachresult.categoryID}>">
                [<{$eachresult.catname}>]
            </a>
          <{/if}>
        </h4>
        <p><{$eachresult.definition}></p>
        <{if $eachresult.ref}>
            <i><{$eachresult.ref}></i>
        <{/if}>
    <{/foreach}>
    <div><{$resultset.navbar}></div>
  </div>
</div>
<script type="text/javascript">
$('select').each(function(){
  $( this ).addClass( "form-control" );
  $( this ).css("margin-bottom", "5px");
});
$('input[type=text]').each(function(){
  $( this ).addClass( "form-control" );
});
$( "input[name*='term']" ).css("background-position","1px 8px");
$('.btnDefault').addClass( "btn btn-success btn-sm" );
</script>