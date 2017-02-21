<style type="text/css">
    <!--
    .entryfooter {
        width: 98%;
        padding: 4px;
        margin: 5px;
        border-top: 1px solid silver;
        border-bottom: 1px solid silver;
    }

    .standard {
        font-size: 11px;
        line-height: 15px;
    }

    -->
</style>
<{* needed for baloon tips*}>
<{if $balloontips}>
    <div id="bubble_tooltip">
        <div class="bubble_top"><span></span></div>
        <div class="bubble_middle">
            <span id="bubble_tooltip_content">Content is coming here as you probably can see.</span>
        </div>
        <div class="bubble_bottom"></div>
    </div>
<{/if}>

<ol class="breadcrumb">
  <li><a href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/index.php"><{$lang_modulename}></a></li>
  <li><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php?init=<{$thisterm.init}>"><{$thisterm.init}></a></li>
  <li><{$thisterm.term}></li>
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

<div class="row">
    <div class="col-md-12">
        <div style="margin-bottom: 15px">
        <h4><{$thisterm.term}><{$microlinks}></h4>
        <{if $multicats == 1}>
            <a href="<{$xoops_url}>/modules/<{$thisterm.dir}>/category.php?categoryID=<{$thisterm.categoryID}>"><span class="label label-info"><{$thisterm.catname}></span></a>
        <{/if}>
        </div>
        <p style="margin-bottom: 10px"><b><{$smarty.const._MD_LEXIKON_ENTRYDEFINITION}></b>
        <span style="display: block"><{$thisterm.definition}></span>
        </p>
        
        <{if $thisterm.ref}>
        <p style="margin-bottom: 10px"><b><{$smarty.const._MD_LEXIKON_ENTRYREFERENCE}></b><{$thisterm.ref}></p>
        <{/if}>
        
        <{if $thisterm.url}>
        <p style="margin-bottom: 10px"><b><{$smarty.const._MD_LEXIKON_ENTRYRELATEDURL}></b><{$thisterm.url}></p>
        <{/if}>
        
    </div>
    
    <div class="col-md-12">
        <div align="right" style="margin:0 1.0em 0 0;">
            <br>
            <span class="standard">
                <span style="color: #4e505c; ">
                    <{$smarty.const._MD_LEXIKON_SUBMITTED}>
                    <{if $showsubmitter }><{$submitter}><{/if}> <{$submittedon}><br>
                    <{$counter}> 
                </span>
            </span>
        </div>        
    </div>
    <div class="col-md-12">
        <div class="entryfooter">
            <span class="standard">
                <{$microlinksnew}>
            <{if $bookmarkme == 3}>
                &nbsp; <!-- AddThis Bookmark Button -->
                <a href="http://www.addthis.com/bookmark.php" onclick="addthis_url = location.href; addthis_title = document.title; return addthis_click(this);" target="_blank">
                    <img src="assets/images/addthis_button1-bm.gif" align="absmiddle" width="125" height="16" border="0" alt="AddThis Social Bookmark Button"/>
                </a>
                <script type="text/javascript">var addthis_pub = 'JJXUY2C9CQIWTKI1';</script>
                <script type="text/javascript" src="http://s9.addthis.com/js/widget.php?v=10"></script>
            <{elseif $bookmarkme == 4}>
                &nbsp; <!-- AddThis Bookmark dropdown -->
                <script type="text/javascript">
                  addthis_url = location.href;
                  addthis_title = document.title;
                  addthis_pub = 'JJXUY2C9CQIWTKI1';
                </script>
                <script type="text/javascript" src="http://s7.addthis.com/js/addthis_widget.php?v=12"></script>
            <{/if}>
            </span>
        </div>        
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <{if $bookmarkme == 2}>
            <{include file="db:lx_bookmark.tpl"}>
        <{/if}>
        <{if $tagbar}>
            <div class="letters">
                <{include file="db:lx_tag_bar.tpl"}>
            </div>
        <{/if}>        
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <!-- start comments -->
        <div style="text-align: center; padding: 3px; margin: 3px;">
            <{$commentsnav}>
            <{$lang_notice}>
        </div>

        <div style="margin: 3px; padding: 3px;">
            <!-- start comments loop -->
            <{if $comment_mode == "flat"}>
                <{include file="db:system_comments_flat.tpl"}>
            <{elseif $comment_mode == "thread"}>
                <{include file="db:system_comments_thread.tpl"}>
            <{elseif $comment_mode == "nest"}>
                <{include file="db:system_comments_nest.tpl"}>
            <{/if}>
            <!-- end comments loop -->
            <!-- end comments -->
        </div>        
    </div>
</div>

<{include file='db:system_notification_select.tpl'}>
