<{* New Header block *}>
<ol class="breadcrumb">
  <li><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php"><{$pageinitial}></a></li>
</ol>


<{* Alphabet block *}>
<{* Alphabet block *}>
<div class="row" style="margin-bottom: 20px">
  <div class="col-md-12">
    <h3><{$smarty.const._MD_LEXIKON_BROWSELETTER}></h3>

    <ul class="pagination pagination-sm">
      <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php" title="[ <{$publishedwords}> ]"><{$smarty.const._MD_LEXIKON_ALL}></a></li>
      <{foreach item=letterlinks from=$alpha.initial}>
          <{if $letterlinks.total > 0}>
            <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php?init=<{$letterlinks.id}>" title="[ <{$letterlinks.total}> ]" >
              <{$letterlinks.linktext}>
            </a></li>
          <{else}>
            <li><a href="#"><{$letterlinks.linktext}></a></li>
          <{/if}>
      <{/foreach}>

      <{if $totalother > 0}>
        <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php?init=<{$smarty.const._MD_LEXIKON_OTHER}>" title="[ <{$totalother}> ]">
          <{$smarty.const._MD_LEXIKON_OTHER}>
        </a></li>
      <{else}>
        <li><a href="#"><{$smarty.const._MD_LEXIKON_OTHER}></a></li>
      <{/if}>
    </ul>
  </div>
</div>

<hr>

<div class="row" style="margin-bottom: 20px">
  <div class="col-md-12">

    <{if $pagetype == '0'}>
       <h2 style="text-align: center"><{$smarty.const._MD_LEXIKON_ALL}></h2>
        <div class="letters"><{$smarty.const._MD_LEXIKON_WEHAVE}> <{$totalentries}> <{$smarty.const._MD_LEXIKON_INALLGLOSSARIES}></div>
        <br>
        <{foreach item=eachentry from=$entriesarray.single}>
            <h4 class="term" style="clear:both;">
                <a href="<{$xoops_url}>/modules/<{$eachentry.dir}>/entry.php?entryID=<{$eachentry.id}>"><{$eachentry.term}></a> 
                <{if $multicats == 1}>
                <a style="color: #456;" href="<{$xoops_url}>/modules/<{$eachentry.dir}>/category.php?categoryID=<{$eachentry.catid}>">
                    [<{$eachentry.catname}>]
                </a>
                <{/if}>
                <{$eachentry.microlinks}>
            </h4>
            <p><{$eachentry.definition}></p>
            <{if $eachentry.comments }><{$eachentry.comments}><br><{/if}>
            <br>
            <br>
        <{/foreach}>
        <div align='left'><{$entriesarray.navbar}></div>
        <div class="letters"> [ <a href='javascript:history.go(-1)'><{$smarty.const._MD_LEXIKON_RETURN}></a><b> | </b><a
                    href='./index.php'><{$smarty.const._MD_LEXIKON_RETURN2INDEX}></a> ]
        </div>
    <{elseif $pagetype == '1'}>
        <h2 style="text-align: center"><{$firstletter}></h2>
        <div class="letters"><{$smarty.const._MD_LEXIKON_WEHAVE}> <{$totalentries}> <{$smarty.const._MD_LEXIKON_BEGINWITHLETTER}></div>
        <br>
        <{foreach item=eachentry from=$entriesarray2.single}>
            <h4 class="term" style="clear:both;">
                <a href="<{$xoops_url}>/modules/<{$eachentry.dir}>/entry.php?entryID=<{$eachentry.id}>"><{$eachentry.term}></a> 
                <{if $multicats == 1}>
                <a style="color: #456;" href="<{$xoops_url}>/modules/<{$eachentry.dir}>/category.php?categoryID=<{$eachentry.catid}>">
                    [<{$eachentry.catname}>]</A><{/if}>
             <{$eachentry.microlinks}>
             </h4>
            <p><{$eachentry.definition}></p>
            <{if $eachentry.comments }><{$eachentry.comments}><br><{/if}>
            <br>
            <br>
        <{/foreach}>
        <div align="left"><{$entriesarray2.navbar}></div>
        <div class='letters'> [ <a href='javascript:history.go(-1)'><{$smarty.const._MD_LEXIKON_RETURN}></a><b> | </b><a
                    href='./index.php'><{$smarty.const._MD_LEXIKON_RETURN2INDEX}></a> ]
        </div>
    <{/if}>

</div>
</div>
