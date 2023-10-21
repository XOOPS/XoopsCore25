<{* tell a friend, recommend us popup *}>
<{if isset($closeHead) ? $closeHead : true}>
<{$headContents|default:''}>
<script>window.resizeTo(360, 560)</script>
</head>
<body>
<{/if}>
<{if !empty($successMessage)}><h4><{$successMessage}></h4>
<{else}>
<{if !empty($errorMessage)}><div class='errorMsg'><{$errorMessage}></div><{/if}>

<{$recommendus.rendered|default:''}>
<{/if}>
<{if isset($closeButton) ? $closeButton : true}>
    <div style="text-align:center;"><input class="btn btn-secondary btn-default formButton" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>
