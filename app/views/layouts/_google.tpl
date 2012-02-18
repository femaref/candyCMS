{strip}
  {if $WEBSITE_TRACKING_CODE && $WEBSITE_MODE == 'production'}
    <script type='text/javascript'>
      var sTrackingCode = '{$WEBSITE_TRACKING_CODE}';
      {literal}
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', sTrackingCode]);
        _gaq.push (['_gat._anonymizeIp']);
        _gaq.push(['_trackPageview']);

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
      {/literal}
    </script>
  {/if}
{/strip}