<{if $show_categort_title == true}>
    <{$smarty.const._MD_XOOPSTUBE_CATEGORYC}> <{$video.category}>
<{/if}>

<a href="<{$xoops_url}>/modules/<{$video.module_dir}>/singlevideo.php?cid=<{$video.cid}>&lid=<{$video.id}>">
    <{$video.title}>
</a>
<{$video.icons}>

<{if isset($xoops_isadmin)}>
    <{$video.adminvideo}>
<{/if}>

<{if $video.published > 0 }>
    <a href="<{$xoops_url}>/modules/<{$video.module_dir}>/singlevideo.php?cid=<{$video.cid}>&lid=<{$video.id}>">
        <img src="<{$xoops_url}>/modules/<{$video.module_dir}>/assets/images/icon/play.png" alt="<{$smarty.const._MD_XOOPSTUBE_VIEWDETAILS}>" title="<{$smarty.const._MD_XOOPSTUBE_VIEWDETAILS}>">
    </a>
<{/if}>
<{if $video.showsubmitterx}>
    <{$smarty.const._MD_XOOPSTUBE_SUBMITTER}>: <{$video.submitter}>
<{/if}>
<{$smarty.const._MD_XOOPSTUBE_PUBLISHER}>: <{$video.publisher}>
<{$lang_subdate}>: <{$video.updated}>
<{$video.hits|wordwrap:50:"\n":true}>
<{$smarty.const._MD_XOOPSTUBE_TIMEB}> <{$video.time}>

<{if $video.showrating}>
    <br>
    <div class="xoopstube_infoblock">
        <span style="font-weight: bold;"><{$smarty.const._MD_XOOPSTUBE_RATINGC}></span>&nbsp;<img src="<{$xoops_url}>/modules/<{$video.module_dir}>/assets/images/icon/<{$video.rateimg}>" alt="" align="middle">&nbsp;&nbsp;(<{$video.votes}>)
    </div>
<{/if}>
