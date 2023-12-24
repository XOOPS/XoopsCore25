<{* tell a friend, recommend us popup *}>
<{if $closeHead|default:true}>
<{$headContents|default:''}>
<script>window.resizeTo(360, 660)</script>
</head>
<body>
<{/if}>
<{if $successMessage|default:false}>
    <h4 class="text-center"><{$successMessage}></h4>
<{else}>
    <{if $errorMessage|default:false}>
        <div class='errorMsg'><{$errorMessage}></div>
    <{/if}>
    <div class="mx-2"><{$recommendus.rendered|default:''}></div>
<{/if}>
<{if $closeButton|default:true}>
    <div class="text-center mx-3 mb-3"><input class="btn btn-primary btn-block" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>
