<{* avatar selector popup *}>
<{if isset($closeHead) ? $closeHead : true}>
<{$headContents|default:''}>
<script>window.resizeTo(600, 400)</script>
</head>
<body>
<{/if}>
<h4 class="text-center"><{$lang_avavatars}></h4>

<{if isset($closeButton) ? $closeButton : true}>
    <div class="text-center m-3"><input class="btn btn-primary btn-block" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>

<{counter name=avatarid start=-1 print=false}>
<{counter name=loopid start=0 print=false}>
<{assign var=tdcnt value=1}>

<div class="d-flex flex-wrap align-items-end justify-content-center">
<{foreach item=name from=$avatars|default:null key=file}>
    <div class="px-1">  
        <figure class="figure">
            <img src="<{$upload_url}><{$file}>" alt="<{$name}>" title="<{$name}>" class="figure-img img-fluid rounded">
            <figcaption class="figure-caption text-center">
                <{$name}><br>
                <{counter name=avatarid assign=avatarid}>
                <{* counter name=loopid assign=loopid *}>
                <button class="btn btn-outline-primary btn-sm" name="myimage" type="button" onclick="myimage_onclick(<{$avatarid}>)"><{$lang_select}></button>
            </figcaption>
        </figure>
        </div>
<{/foreach}>
</div>

<{if isset($closeButton) ? $closeButton : true}>
    <div class="text-center mx-3 mb-3"><input class="btn btn-primary btn-block" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>
