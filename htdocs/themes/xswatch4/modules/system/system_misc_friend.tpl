<{* tell a friend, recommend us popup *}>
<{if isset($closeHead) ? $closeHead : true}>
<{$headContents|default:''}>
<script>window.resizeTo(360, 660)</script>
</head>
<body>
<{/if}>
<{if !empty($successMessage)}>
    <h4 class="text-center"><{$successMessage}></h4>
<{else}>
    <{if !empty($errorMessage)}>
        <div class='errorMsg'><{$errorMessage}></div>
    <{/if}>
    <div class="mx-2"><{$recommendus.rendered|default:''}></div>
<{/if}>
<{if isset($closeButton) ? $closeButton : true}>
    <div class="text-center mx-3 mb-3"><input class="btn btn-primary btn-block" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>
