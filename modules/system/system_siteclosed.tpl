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
    <link rel="stylesheet" type="text/css" href="<{xoAppUrl media/font-awesome/css/font-awesome.min.css}>">
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">
    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{$xoops_imageurl}>js/bootstrap.bundle.min.js"></script>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl backend.php}>">

    <title><{if $xoops_dirname == "system"}><{$xoops_sitename}><{if $xoops_pagetitle !=''}> - <{$xoops_pagetitle}><{/if}><{else}><{if $xoops_pagetitle
        !=''}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>
    <{$xoops_module_header}>
</head>
<body class="site-closed-body">
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="xoops-site-closed col-lg-6">

            <div class="aligncenter site-closed-logo">
                <img class="img-fluid" src="<{$xoops_imageurl}>images/logo.png" alt="<{$lang_login}>">
            </div>

            <div class="xoops-site-closed-container">
                <p class="text-muted"><{$lang_siteclosemsg}></p>
                <form action="<{xoAppUrl user.php}>" method="post" role="form" class="form-horizontal">
                    <label for="xo-login-uname"><{$lang_username}></label>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fa fa-user"> </i></span>
                        </div>
                        <input class="form-control" type="text" name="uname" id="xo-login-uname" placeholder="<{$smarty.const.THEME_LOGIN}>" aria-label="<{$lang_username}>" aria-describedby="basic-addon1">
                    </div>

                    <label for="xo-login-pass"><{$lang_password}></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon2"><i class="fa fa-lock"> </i></span>
                        </div>
                        <input class="form-control" type="password" name="pass" id="xo-login-pass" placeholder="<{$smarty.const.THEME_PASS}>" aria-label="<{$lang_password}>" aria-describedby="basic-addon2">
                    </div>

                    <input type="hidden" name="xoops_redirect" value="<{$xoops_requesturi}>">
                    <input type="hidden" name="xoops_login" value="1">

                    <label for="xo-login-button"> </label>
                    <div class="aligncenter">
                        <button id="xo-login-button" type="submit" class="btn btn-secondary">
                            <span class="fa fa-sign-in" aria-hidden="true"></span>
                            <{$lang_login}>
                        </button>
                    </div>

                </form>
            </div><!-- .xoops-site-closed-container -->
        </div><!-- .xoops-site-closed -->
    </div><!-- .row -->
</div><!-- .container -->
</body>
</html>
