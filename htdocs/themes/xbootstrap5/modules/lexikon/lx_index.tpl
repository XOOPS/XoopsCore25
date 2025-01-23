<{* New Header block *}>
<ol class="breadcrumb">
    <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>"><{$smarty.const._MD_LEXIKON_HOME}></a></li>
    <li><{$lang_modulename}></li>
</ol>

<{if $empty|default:false == 1}>
    <div class="alert alert-warning" role="alert"><{$smarty.const._MD_LEXIKON_STILLNOTHINGHERE}></div>
<{/if}>

<{if $teaser == true}>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <{$teaser}>
                </div>
            </div>
        </div>
    </div>
<{/if}>

<!--SEARCH-->
<div class="row" style="margin-bottom: 20px">
    <div class="col-md-6">
        <h3 style="padding-bottom: 10px;"><{$smarty.const._MD_LEXIKON_SEARCHENTRY}></h3>
        <{$searchform}>
    </div>
    <hr class="visible-sm visible-xs">
    <div class="col-md-6">
        <h3 style="padding-bottom: 10px;"><{$smarty.const._MD_LEXIKON_WEHAVE}></h3>
        <{$smarty.const._MD_LEXIKON_DEFS}><{$publishedwords}><br>
        <{if $multicats == 1}><{$smarty.const._MD_LEXIKON_CATS}><{$totalcats}><br><{/if}>
        <div style="padding-top: 10px">
            <input class="btn btn-primary btn-sm form-control" type="button" value="<{$smarty.const._MD_LEXIKON_SUBMITENTRY}>" onclick="location.href = 'submit.php'">
            <input class="btn btn-info btn-sm form-control" type="button" value="<{$smarty.const._MD_LEXIKON_REQUESTDEF}>" onclick="location.href = 'request.php' ">
        </div>
    </div>
</div>

<hr>

<{* Alphabet block *}>
<div class="row" style="margin-bottom: 20px">
    <div class="col-md-12">
        <h3><{$smarty.const._MD_LEXIKON_BROWSELETTER}></h3>

        <ul class="pagination pagination-sm">
            <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php" title="[ <{$publishedwords}> ]"><{$smarty.const._MD_LEXIKON_ALL}></a></li>
            <{foreach item=letterlinks from=$alpha.initial|default:null}>
                <{if $letterlinks.total > 0}>
                    <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php?init=<{$letterlinks.id}>" title="[ <{$letterlinks.total}> ]">
                            <{$letterlinks.linktext}>
                        </a></li>
                <{else}>
                    <li class="nav-item"><a class="nav-link" href="#"><{$letterlinks.linktext}></a></li>
                <{/if}>
            <{/foreach}>

            <{if $totalother > 0}>
                <li class="nav-item"><a class="nav-link" href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/letter.php?init=<{$smarty.const._MD_LEXIKON_OTHER}>" title="[ <{$totalother}> ]">
                        <{$smarty.const._MD_LEXIKON_OTHER}>
                    </a></li>
            <{else}>
                <li class="nav-item"><a class="nav-link" href="#"><{$smarty.const._MD_LEXIKON_OTHER}></a></li>
            <{/if}>
        </ul>
    </div>
</div>

<hr>

<{* Category block *}>
<!-- $layout 0 and 1 are the same. if you want to change first change CONFIG_CATEGORY_LAYOUT_PLAIN in inlcude/common.inc.php -->
<{if $layout == '0'}>
    <{if $multicats == 1 && count($block0.categories) >= 0 }>
        <div class="row" style="margin-bottom: 20px">
            <div class="col-md-12">
                <h3> <{$smarty.const._MD_LEXIKON_BROWSECAT}> </h3>
            </div>
        </div>
        <{foreach item=catlinks from=$block0.categories|default:null}>

            <{if $catlinks.count is div by 4}>

            <{/if}>
        <{/foreach}>
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
                <td>
                    <{if $catlinks.image != "" && $show_screenshot == true}>
                        <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$catlinks.id}>" target="_parent">
                            <img src="<{$xoops_url}>/uploads/<{$lang_moduledirname}>/categories/images/<{$catlinks.image}>" width="<{$logo_maximgwidth}>" align="left" class="floatLeft" alt="[<{$catlinks.name}>]&nbsp;[<{$catlinks.total}>]">
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
            </tr>
            <tr>
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
        <{foreach item=catlinks from=$block0.categories|default:null}>

            <{if $catlinks.count is div by 4}>

            <{/if}>
        <{/foreach}>
        <table class="table table-bordered table-responsive">
            <tbody>
            <tr>
                <!-- Start category loop -->
                <td>
                    <{if $catlinks.image != "" && $show_screenshot == true}>
                        <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$catlinks.id}>" target="_parent">
                            <img src="<{$xoops_url}>/uploads/<{$lang_moduledirname}>/categories/images/<{$catlinks.image}>" width="<{$logo_maximgwidth}>" align="left" class="floatLeft" alt="[<{$catlinks.name}>]&nbsp;[<{$catlinks.total}>]">
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
            </tr>
            <tr><!-- End category loop -->
            </tr>
            </tbody>
        </table>
        <div style="text-align: right">
            <a class="btn btn-primary btn-sm" role="button" href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php" title="[ <{$publishedwords}> ]">
                <{$smarty.const._MD_LEXIKON_ALLCATS}>&nbsp;[<{$publishedwords}>]
            </a>
        </div>
    <{/if}>
<{/if}>

<hr>

<div class="row" style="margin-bottom: 20px">
    <div class="col-md-4 col-sm-12">
        <h3><{$smarty.const._MD_LEXIKON_RECENTENT}></h3>
        <ul>
            <{foreach item=newentries from=$block.newstuff|default:null}>
                <li>
                    <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/entry.php?entryID=<{$newentries.id}>"><{$newentries.linktext}></a> <{if $showdate == 1}>
                        <span style="font-size: xx-small; color: #456;">[<{$newentries.date}>]</span><{/if}>
                </li>
            <{/foreach}>
        </ul>
    </div>

    <div class="col-md-4 col-sm-12">
        <h3><{$smarty.const._MD_LEXIKON_POPULARENT}></h3>
        <ul>
            <{foreach item=popentries from=$block2.popstuff|default:null}>
                <li>
                    <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/entry.php?entryID=<{$popentries.id}>"><{$popentries.linktext}></a> <{if $showcount == 1}>
                        <span style="font-size: xx-small; color: #456;">[<{$popentries.counter}>
                        ]</span><{/if}></li>
            <{/foreach}>
        </ul>
    </div>

    <div class="col-md-4 col-sm-12">
        <h3><{$smarty.const._MD_LEXIKON_RANDOMTERM}></h3>
        <{if $multicats == 1}>
           <{if $empty|default:false != 1}>
                <div class="catname"><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/category.php?categoryID=<{$random.categoryID}>"><{$random.categoryname}></a>
                </div>
            <{/if}>
        <{/if}>
        <div class="pad4">
            <h5 class="term"><{$microlinks|default:null}><a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/entry.php?entryID=<{$random.id|default:null}>"><{$random.term|default:null}></a>
            </h5>

            <div class="nopadding"><{$random.definition|default:null}></div>
        </div>
    </div>
</div>

<hr>


<{if $userisadmin == 1}>
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-12">
            <h3><{$smarty.const._MD_LEXIKON_SUBANDREQ}></h3>

            <dl class="dl-horizontal">
                <dt><{$smarty.const._MD_LEXIKON_SUB}></dt>
                <{if $wehavesubs == '0'}>
                    <dd><{$smarty.const._MD_LEXIKON_NOSUB}></dd><{/if}>
                <dd>
            <{foreach item=subentries from=$blockS.substuff|default:null}>
                        <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/admin/entry.php?op=mod&entryID=<{$subentries.id}>"><{$subentries.linktext}></a>
                        &nbsp;
                    <{/foreach}>
                </dd>
            </dl>

            <dl class="dl-horizontal">
                <dt><{$smarty.const._MD_LEXIKON_REQ}></dt>
                <{if $wehavereqs == '0'}>
                    <dd><{$smarty.const._MD_LEXIKON_NOREQ}></dd><{/if}>
                <dd>
            <{foreach item=reqentries from=$blockR.reqstuff|default:null}>
                        <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/admin/entry.php?op=mod&entryID=<{$reqentries.id}>"><{$reqentries.linktext}></a>
                        &nbsp;
                    <{/foreach}>
                </dd>
            </dl>
        </div>
    </div>
<{else}>
    <div class="row" style="margin-bottom: 20px">
        <div class="col-md-12">
            <h3><{$smarty.const._MD_LEXIKON_REQ}></h3>
        </div>
    </div>
    <dl class="dl-horizontal">
        <dt><{$smarty.const._MD_LEXIKON_REQ}></dt>
        <{if $wehavereqs == '0'}>
        <dd><{$smarty.const._MD_LEXIKON_NOREQ}></dd>
        <{else}>
        <dd>
            <h5><{$smarty.const._MD_LEXIKON_REQUESTSUGGEST}></h5>
            <{/if}>
            <{foreach item=reqentries from=$blockR.reqstuff|default:null}>
                <a href="<{$xoops_url}>/modules/<{$lang_moduledirname}>/submit.php?suggest=<{$reqentries.id}>"><{$reqentries.linktext}></a>
                &nbsp;
            <{/foreach}>
        </dd>
    </dl>
<{/if}>

<{if $syndication == true}>
    <div align="center" class="clearer" style="padding: 4px;"><br><br>
        <a href="rss.php" title="recent glossary definitions">
            <img src="assets/images/rss.gif" alt="RSS" border="0">
        </a>
    </div>
<{/if}>
<br>
<br>
<script type="text/javascript">
    $("select[name*='type']").addClass("form-control");
    $("select[name*='type']").css({"width": "90%", "margin-bottom": "5px"});
    $("select[name*='categoryID']").addClass("form-control");
    $("select[name*='categoryID']").css({"width": "90%", "margin-bottom": "5px"});
    $("input[name*='term']").addClass("form-control");
    $("input[name*='term']").css({"width": "90%", "margin-bottom": "5px", "background-position": "1px 8px"});
    $('.btnDefault').addClass("btn btn-primary btn-sm");
</script>
<{include file='db:system_notification_select.tpl'}>
