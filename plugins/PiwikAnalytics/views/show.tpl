{strip}
  {if $PLUGIN_PIWIKANALYTICS_SITEURL !== '' && $PLUGIN_PIWIKANALYTICS_SITEID !== '' && 
          ($WEBSITE_MODE == 'production' || $WEBSITE_MODE == 'staging')}
    <!-- Piwik --> <script type='text/javascript'>
      var siteId = '{$PLUGIN_PIWIKANALYTICS_SITEID}';
      var siteURL = '{$PLUGIN_PIWIKANALYTICS_SITEURL}';
      {literal}
        var _paq = _paq || [];
        (function(){ var u=(("https:" == document.location.protocol) ? "https://"+siteURL+"/" : "http://"+siteURL+"/");
          _paq.push(['setSiteId', siteId]);
          _paq.push(['setTrackerUrl', u+'piwik.php']);
          _paq.push(['trackPageView']);
          _paq.push(['enableLinkTracking']);
          var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0]; g.type='text/javascript'; g.defer=true; g.async=true; g.src=u+'piwik.js';
          s.parentNode.insertBefore(g,s);
        })();
      {/literal}
    </script> <!-- End Piwik Code -->
  {/if}
{/strip}