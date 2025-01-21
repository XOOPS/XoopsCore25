<{if isset($xoops_rsscss)}>
<{*<?xml-stylesheet type="text/css" href="<{$xoops_themecss}>"?>*}>
<{/if}>
<rss version="2.0">
    <channel>
        <title><{$channel_title}>
        </title>
        <link><{$channel_link}>
        
        <description><{$channel_desc}>
        </description>
        <lastbuilddate><{$channel_lastbuild}>
        </lastbuilddate>
        <docs>https://backend.userland.com/rss/</docs>
        <generator><{$channel_generator}>
        </generator>
        <category><{$channel_category}>
        </category>
        <managingeditor><{$channel_editor}>
        </managingeditor>
        <webmaster><{$channel_webmaster}>
        </webmaster>
        <language><{$channel_language}>
        </language>
        <{if $image_url|default:'' != ''}>
            <img>
                <title><{$channel_title}>
                </title>
                <url><{$image_url}>
                </url>
                <link><{$channel_link}>
                
                <width><{$image_width}>
                </width>
                <height><{$image_height}>
                </height>
            
        <{/if}>
        <{foreach item=item from=$items|default:null}>
            <item>
                <title><{$item.title}>
                </title>
                <link><{$item.link}>
                
                <description><{$item.description}>
                </description>
                <pubdate><{$item.pubdate}>
                </pubdate>
                <guid><{$item.guid}>
                </guid>
            </item>
        <{/foreach}>
    </channel>
</rss>
