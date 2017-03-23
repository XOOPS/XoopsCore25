<{* To enable Shareaholic, create an account and register your site at https://shareaholic.com/
    Then copy the Site ID that shareaholic assigned to your site in place of the n/a in the next line. *}>
<{assign var='siteId' value='n/a'}>
<{if $siteId != 'n/a'}>
    <script type='text/javascript'
            data-cfasync='false'
            src='//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js'
            data-shr-siteid='<{$siteId}>'
            async='async'>
    </script>
<{/if}>
