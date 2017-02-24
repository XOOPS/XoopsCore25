<ul class="list-group">
    <{foreach item=file from=$block.files}>
        <li class="list-group-item">
            <{$file.link}>
            <span style="padding-left: 16px;">
                <small><span class="glyphicon glyphicon-calendar"></span>&nbsp;<{$file.new}></small>
            </span>
        </li>
    <{/foreach}>
</ul>
