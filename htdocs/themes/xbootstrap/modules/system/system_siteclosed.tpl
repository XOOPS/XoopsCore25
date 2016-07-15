<!doctype html>
<html class="no-js" lang="<{$xoops_langcode}>">
<head>
    <{assign var=theme_name value=$xoTheme->folderName}>
    <meta charset="<{$xoops_charset}>">
    <meta name="keywords" content="<{$xoops_meta_keywords}>">
    <meta name="description" content="<{$xoops_meta_description}>">
    <meta name="robots" content="<{$xoops_meta_robots}>">
    <meta name="rating" content="<{$xoops_meta_rating}>">
    <meta name="author" content="<{$xoops_meta_author}>">
    <meta name="generator" content="XOOPS">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- disable zoom in mobile devices:
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    -->
    <link href="<{$xoops_url}>/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="<{$xoops_imageurl}>css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<{$xoops_imageurl}>css/reset.css">
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">
    <{*<script src="<{$xoops_imageurl}>js/jquery-1.10.2.js"></script>*}>
    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{$xoops_imageurl}>js/bootstrap.min.js"></script>
    <script src="<{$xoops_imageurl}>js/modernizr.custom.95845.js"></script>
    <script src="<{$xoops_imageurl}>js/js.js"></script>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl backend.php}>">
    <title><{if $xoops_dirname == "system"}><{$xoops_sitename}><{if $xoops_pagetitle !=''}> - <{$xoops_pagetitle}><{/if}><{else}><{if $xoops_pagetitle
        !=''}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>
    <{includeq file="$theme_name/tpl/shareaholic-script.tpl"}>
    <{$xoops_module_header}>
</head>
<body class="site-closed-body">
<div class="container">
    <div class="row">
        <div class="xoops-site-closed col-md-6 col-md-offset-3">

            <div class="aligncenter site-closed-logo">
                <img src="<{$xoops_imageurl}>images/logo.png" alt="<{$lang_login}>">
            </div>

            <div class="xoops-site-closed-container">
                <blockquote><p class="text-muted"><{$lang_siteclosemsg}></p></blockquote>
                <form action="<{xoAppUrl user.php}>" method="post" role="form" class="form-horizontal">

                    <label class="control-label"><{$lang_username}></label>

                    <div class="input-container">
                        <input type="text" name="uname" class="form-control" placeholder="<{$smarty.const.THEME_LOGIN}>">
                        <span class="glyphicon glyphicon-user"></span>
                    </div>


                    <label class="control-label"><{$lang_password}></label>

                    <div class="input-container">
                        <input type="password" name="pass" class="form-control" placeholder="<{$smarty.const.THEME_PASS}>">
                        <span class="glyphicon glyphicon-lock"></span>
                    </div>

                    <input type="hidden" name="xoops_redirect" value="<{$xoops_requesturi}>">
                    <input type="hidden" name="xoops_login" value="1">

                    <div class="aligncenter">
                        <input type="submit" value="<{$lang_login}>" class="btn btn-warning">
                    </div>

                </form>
            </div><!-- .xoops-site-closed-container -->
        </div><!-- .xoops-site-closed -->
    </div><!-- .row -->
</div><!-- .container -->
</body>
</html>
