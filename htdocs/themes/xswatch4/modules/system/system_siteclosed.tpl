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

    <{include file="$theme_name/tpl/xswatchCss.tpl" assign="xswatchCss"}>
    <{include file="$theme_name/tpl/xswatchDarkCss.tpl" assign="xswatchDarkCss"}>
    <{if isset($xswatchDarkCss) && $xswatchDarkCss == ''}>
        <link rel="stylesheet" type="text/css" href="<{$xoops_imageurl}><{$xswatchCss}>/xoops.css">
        <link rel="stylesheet" type="text/css" href="<{$xoops_imageurl}><{$xswatchCss}>/bootstrap.min.css">
    <{else}>
        <link rel="stylesheet" media="(prefers-color-scheme: light)" href="<{$xoops_imageurl}><{$xswatchCss}>/xoops.css">
        <link rel="stylesheet" media="(prefers-color-scheme: light)" href="<{$xoops_imageurl}><{$xswatchCss}>/bootstrap.min.css">
        <link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<{$xoops_imageurl}><{$xswatchDarkCss}>/xoops.css">
        <link rel="stylesheet" media="(prefers-color-scheme: dark)" href="<{$xoops_imageurl}><{$xswatchDarkCss}>/bootstrap.min.css">
    <{/if}>

    <link rel="stylesheet" type="text/css" href="<{xoAppUrl 'media/font-awesome/css/font-awesome.min.css'}>">
    <link rel="stylesheet" type="text/css" media="all" href="<{$xoops_themecss}>">
    <script src="<{$xoops_url}>/browse.php?Frameworks/jquery/jquery.js"></script>
    <script src="<{$xoops_imageurl}>js/bootstrap.bundle.min.js"></script>
    <link rel="alternate" type="application/rss+xml" title="" href="<{xoAppUrl 'backend.php'}>">

    <title><{if isset($xoops_dirname) && $xoops_dirname == "system"}><{$xoops_sitename}><{if !empty($xoops_pagetitle)}> - <{$xoops_pagetitle}><{/if}><{else}><{if !empty($xoops_pagetitle)}><{$xoops_pagetitle}> - <{$xoops_sitename}><{/if}><{/if}></title>
    <{$xoops_module_header|default:''}>
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
                <{if !empty($redirect_message)}>
                    <p class="text-warning"><{$redirect_message}></p>
                <{/if}>
                <form action="<{xoAppUrl 'user.php'}>" method="post" role="form" class="form-horizontal">
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
