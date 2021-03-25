<{* tell a friend, recommend us popup *}>
<{if $closeHead|default:true}>
<{$headContents|default:''}>
<script>window.resizeTo(360, 560)</script>
</head>
<body>
<{/if}>
<{if $successMessage|default:false}><h4><{$successMessage}></h4>
<{else}>
<{if $errorMessage|default:false}><div class='errorMsg'><{$errorMessage}></div><{/if}>

<{$recommendus.rendered|default:''}>
<{/if}>
<{if $closeButton|default:true}>
    <div style="text-align:center;"><input class="btn btn-secondary btn-default formButton" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>
