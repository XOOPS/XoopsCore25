<{* New Header block *}>
<ol class="breadcrumb">
  <li><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a></li>
  <li>
      <{if $pagetype == '0'}>
        <{$smarty.const._MD_LEXIKON_ALLCATS}>
      <{elseif $pagetype == '1'}>
        <{$singlecat.name}>
      <{/if}>
  </li>
</ol>

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


<{if $pagetype == '0'}>
<div class="row" style="margin-bottom: 20px">
<div class="col-md-12">
<{* Category block *}>
<!-- $layout 0 and 1 are the same. if you want to change first change CONFIG_CATEGORY_LAYOUT_PLAIN in inlcude/common.inc.php -->
<{if $layout == '0'}> 
    <{if $multicats == 1 && count($block0.categories) gt 0 }>
        <div class="row" style="margin-bottom: 20px">
            <div class="col-md-12">
                <h3> <{$smarty.const._MD_LEXIKON_BROWSECAT}> </h3>
            </div>
        </div>
        <table class="table table-bordered table-responsive">
          <tbody>
              <tr>
                  <td>
                      <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php" title="[ <{$publishedwords}> ]">
                          <{$smarty.const._MD_LEXIKON_ALLCATS}>
                      </a>
                      [<{$publishedwords}>]
                   </td>
                   <!-- Start category loop -->
                   <{foreach item=catlinks from=$block0.categories}>
                   <td>
                    <{if $catlinks.image != "" && $show_screenshot == true}>
                        <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$catlinks.id}>" target="_parent">
                            <img src="<{$xoops_url}>/uploads/<{$lang_moduledirname}>/categories/images/<{$catlinks.image}>" 
                                width="<{$logo_maximgwidth}>" align="left" class="floatLeft"
                                alt="[<{$catlinks.name}>]&nbsp;[<{$catlinks.total}>]"/>
                         </a>
                     <{/if}>
                     <{if $catlinks.count > 0}>
                        <{if $catlinks.total > 0}>
                            <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$catlinks.id}>" title="[<{$catlinks.total}>]">
                        <{/if}>
                        <{$catlinks.linktext}>
                        <{if $catlinks.total > 0}>
                            </a> 
                        <{/if}>
                            [<{$catlinks.total}>]
                        <{/if}>
                     </td>
                    <{if $catlinks.count is div by 4}>
                    </tr>
                    <tr>
                     <{/if}>
                    <{/foreach}>
                    <!-- End category loop -->
            </tr>
        </tbody>
        </table>
    <{/if}>
<{else}>
    <{if $multicats == 1}>
      <div class="row" style="margin-bottom: 20px">
            <div class="col-md-12">
                <h3> <{$smarty.const._MD_LEXIKON_BROWSECAT}> </h3>
            </div>
        </div>
        <table class="table table-bordered table-responsive">
          <tbody>
              <tr>
                   <!-- Start category loop -->
                   <{foreach item=catlinks from=$block0.categories}>
                   <td>
                    <{if $catlinks.image != "" && $show_screenshot == true}>
                        <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$catlinks.id}>" target="_parent">
                            <img src="<{$xoops_url}>/uploads/<{$lang_moduledirname}>/categories/images/<{$catlinks.image}>" 
                                width="<{$logo_maximgwidth}>" align="left" class="floatLeft"
                                alt="[<{$catlinks.name}>]&nbsp;[<{$catlinks.total}>]"/>
                         </a>
                     <{/if}>
                     <{if $catlinks.count > 0}>
                        <{if $catlinks.total > 0}>
                            <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$catlinks.id}>" title="[<{$catlinks.total}>]">
                        <{/if}>
                        <{$catlinks.linktext}>
                        <{if $catlinks.total > 0}>
                            </a> 
                        <{/if}>
                            [<{$catlinks.total}>]
                        <{/if}>
                     </td>
                    <{if $catlinks.count is div by 4}>
                    </tr>
                    <tr>
                     <{/if}>
                    <{/foreach}>
                    <!-- End category loop -->
            </tr>
        </tbody>
        </table>
        
        <div  style="text-align: right">
        <a class="btn btn-success btn-sm" role="button" href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php" title="[ <{$publishedwords}> ]">
            <{$smarty.const._MD_LEXIKON_ALLCATS}>&nbsp;[<{$publishedwords}>]
        </a>
        </div>
    <{/if}>
<{/if}>
</div>
</div>
<hr>
<{/if}>

<!-- Category -->
<div class="row" style="margin-bottom: 20px">
<div class="col-md-12">
<{if $pagetype == '0'}>
    <h3><{$smarty.const._MD_LEXIKON_ALLCATS}></h3>
    <{foreach item=eachcat from=$catsarray.single}>
        <table class="table table-responsive">
            <thead>
                <tr><th><a href="<{$xoops_url}>/modules/<{$eachcat.dir}>/category.php?categoryID=<{$eachcat.id}>"><{$eachcat.name}></a></th></tr>
            </thead>
            <tbody>
                <tr>
                <td>
                    <{if $eachcat.image != "" && $show_screenshot == '1'}>
                       <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$eachcat.id}>" target="_parent">
                        <img src="<{$xoops_url}>/uploads/lexikon/categories/images/<{$eachcat.image}>"
                                 width="<{$imgcatwd}>" align="bottom" vspace="2" hspace="2" border="0"
                                 alt="[<{$eachcat.name}>]"/>
                       </a>
                     <{/if}>
                    <{$eachcat.description}>
                    <span style="display:block;  font-size: 80%; margin-top: 5px;"><{$smarty.const._MD_LEXIKON_WEHAVE}> <{$eachcat.total}> <{$smarty.const._MD_LEXIKON_ENTRIESINCAT}></span>
                </td>
                </tr>
             </tbody>
         </table>
      <{/foreach}>
    <div align="left"><{$catsarray.navbar}></div>

    <div align="center"> [ <a href='javascript:history.go(-1)'><{$smarty.const._MD_LEXIKON_RETURN}></a><b> | </b>
        <a href='./index.php'><{$smarty.const._MD_LEXIKON_RETURN2INDEX}></a> ]
    </div>

    <{* syndication *}>
    <{if $syndication == true}>
        <div align="center" style="padding: 4px;"><br><br>
            <a href="rss.php" title="recent entries"><img src="assets/images/rss.gif" border="0"/></a>
        </div>
    <{/if}>

<{elseif $pagetype == '1'}>
    <h3><{$singlecat.name}></h3>
    <p>
        <{if $singlecat.image != "" && $show_screenshot == '1'}>
            <img src="<{$xoops_url}>/uploads/lexikon/categories/images/<{$singlecat.image}>" width="<{$imgcatwd}>" align="center" vspace="2" hspace="2" border="0" alt="[<{$singlecat.name}>]"/>
                <{$singlecat.name}>
        <{/if}>
        <{$singlecat.description}>
     </p>
        <span style="display:block;  font-size: 80%; margin-top: 5px;">
            <{$smarty.const._MD_LEXIKON_WEHAVE}> <{$singlecat.total}> <{$smarty.const._MD_LEXIKON_ENTRIESINCAT}>
        </span>

<hr>


    <{foreach item=eachentry from=$entriesarray.single}>
    <span style="display:block; margin-bottom: 15px;">
            <h4>
                <a href="<{$xoops_url}>/modules/<{$eachentry.dir}>/entry.php?entryID=<{$eachentry.id}>"><{$eachentry.term}></a>
            </h4>
            <p><{$eachentry.definition}></p>
            <{if $eachentry.comments }><{$eachentry.comments}><br><{/if}>
    </span>
    <{/foreach}>

<{/if}>
</div>
</div><!-- END ROW -->
<div align='left'><{$entriesarray.navbar}></div>

<div align='center'> 
    [ <a href='javascript:history.go(-1)'><{$smarty.const._MD_LEXIKON_RETURN}></a><b> | </b>
    <a href='./index.php'><{$smarty.const._MD_LEXIKON_RETURN2INDEX}></a> ]
</div>

        <{* syndication *}>
        <{if $syndication == true}>
            <div align="center" style="padding: 4px;"><br><br>
                <a href="rss.php?categoryID=<{$singlecat.id}>" title="Recent terms in this category"><img
                            src="assets/images/rss.gif" border="0"/></a>
            </div>
        <{/if}>


        <br>
<{include file='db:system_notification_select.tpl'}>