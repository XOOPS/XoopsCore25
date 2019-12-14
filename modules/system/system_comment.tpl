<div class="xoops-comment-template" id="comment<{$comment.id}>">
	<div class="row">
		<div class="col-3 col-md-2 aligncenter"><{$comment.poster.uname}></div>
		<div class="col-5 col-md-4">
			<small class="text-muted"><strong><{$lang_posted}></strong> <{$comment.date_posted}></small>
		</div>
		<{if $comment.date_posted != $comment.date_modified}>
		<div class="col-4 col-md-6">
			<small class="text-muted"><strong><{$lang_updated}></strong> <{$comment.date_modified}></small>
		</div><!-- .col-md-3 -->
		<{else}>
			<div class="col-4 col-md-6">&nbsp;</div>
		<{/if}>
		<div class="w-100"></div>
		<div class="col-3 col-md-2 xoops-comment-author aligncenter">
			<{if $comment.poster.id != 0}>
				<img src="<{$xoops_upload_url}>/<{$comment.poster.avatar}>" class="img-fluid rounded image-avatar" alt="">
				<ul class="list-unstyled">
					<li><strong class="poster-rank hidden-xs"><{$comment.poster.rank_title}></strong></li>
					<li><img src="<{$xoops_upload_url}>/<{$comment.poster.rank_image}>" alt="<{$comment.poster.rank_title}>"
							 class="poster-rank img-responsive"></li>
				</ul>
				<ul class="list-unstyled poster-info hidden">
					<li><{$lang_joined}> <{$comment.poster.regdate}></li>
					<li><{$lang_from}> <{$comment.poster.from}></li>
					<li><{$lang_posts}> <{$comment.poster.postnum}></li>
					<li><{$comment.poster.status}></li>
				</ul>
			<{else}>
				&nbsp;
			<{/if}>
		</div>
		<div class="col-9 col-md-10 xoops-comment-text">
			<h4><{$comment.image}><{$comment.title}></h4>

			<p class="message-text"><{$comment.text}></p>
		</div>
		<div class="w-100"></div>
		<div class="col-12 col-md-12 alignright">
			<{if $xoops_iscommentadmin == true}>
				<a href="<{$editcomment_link}>&amp;com_id=<{$comment.id}>" title="<{$lang_edit}>" class="btn btn-secondary btn-sm">
					<span class="fa fa-edit"></span>
				</a>
				<a href="<{$replycomment_link}>&amp;com_id=<{$comment.id}>" title="<{$lang_reply}>" class="btn btn-secondary btn-sm">
					<span class="fa fa-comment"></span>
				</a>
				<a href="<{$deletecomment_link}>&amp;com_id=<{$comment.id}>" title="<{$lang_delete}>" class="btn btn-secondary btn-sm">
					<span class="fa fa-trash"></span>
				</a>
			<{elseif $xoops_isuser == true && $xoops_userid == $comment.poster.id}>
				<a href="<{$editcomment_link}>&amp;com_id=<{$comment.id}>" title="<{$lang_edit}>" class="btn btn-secondary btn-sm">
					<span class="fa fa-edit"></span>
				</a>
				<a href="<{$replycomment_link}>&amp;com_id=<{$comment.id}>" title="<{$lang_reply}>" class="btn btn-secondary btn-sm">
					<span class="fa fa-comment"></span>
				</a>
			<{elseif $xoops_isuser == true || $anon_canpost == true}>
				<a href="<{$replycomment_link}>&amp;com_id=<{$comment.id}>" class="btn btn-secondary btn-sm">
					<span class="fa fa-comment"></span>
				</a>
			<{else}>
				&nbsp;
			<{/if}>
		</div>	
	</div>	
</div>