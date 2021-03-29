<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">
<head>
    <script type="application/javascript">
        var tplUrl = '<{$xoops_url}>/modules/system/themes/transition';
    </script>
    <{includeq file="$theme_tpl/xo_metas.tpl"}>
    <{includeq file="$theme_tpl/xo_scripts.tpl"}>
    <style>
        .darken-please {
            background: black;
            filter: invert(1) hue-rotate(180deg);
        }
        .darken-please img {
            filter: invert(1) hue-rotate(180deg) brightness(75%);
        }
        #xo-nav-options img {
            filter: grayscale(100%) brightness(75%);
        }
        .CPbigTitle[style^="background-image:"] {
            filter: invert(1) hue-rotate(180deg) grayscale(1);
        }
        .xct-chart { filter: invert(1) hue-rotate(180deg); }
        .xct-labels { filter: invert(1) hue-rotate(180deg); }
        .ct-label { color: #0A246A;}
        #xo-logger-tabs { background: black; }
        #xo-body-containx {
            background: black;
        }
        #xo-nav-options {
            background: #303030;
        }
        body {
            background: #080808;
        }
        #choosestyle { display: none; }
        input[type='submit'], button[type='submit'] {
            background-color: #666666;
            color: #fff;
        }
        #xo-headnav>li>a {
            background-color: #666666;
            border-left: 1px solid #444444;
            color: #FFF;
        }
        input[type="image"].donate_button {
            filter: invert(1) hue-rotate(180deg);
        }
        .xo-thumbimg {
            background-color: #DDD;
        }
        .tips {
            color: #666;
            border: 1px solid #cccccc;
        }
        #xo-logger-tabs>a {
            background-color: #666666;
            color: #000;
        }
        #xo-logger-output>table {
            background-color: #AAA;
            color: #000;
        }
    </style>
</head>
<body id="<{$xoops_dirname}>" class="<{$xoops_langcode}>">

<{includeq file="$theme_tpl/xo_head.tpl"}>
<{includeq file="$theme_tpl/xo_globalnav.tpl"}>
<{includeq file="$theme_tpl/xo_toolbar.tpl"}>
<{includeq file="$theme_tpl/xo_page.tpl" }>
<{includeq file="$theme_tpl/xo_footer.tpl"}>
</body>
</html>
