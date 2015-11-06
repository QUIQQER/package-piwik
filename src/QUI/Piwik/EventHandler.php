<?php

namespace QUI\Piwik;

use QUI;

/**
 * Class EventHandler
 *
 * @package QUI\Piwik
 */
class EventHandler
{
    /**
     * @param QUI\Template $Template
     * @param QUI\Projects\Site $Site
     */
    static function onTemplateSiteFetch($Template, $Site)
    {
        $Project     = $Site->getProject();
        $piwikUrl    = $Project->getConfig('piwik.settings.url');
        $piwikSideId = $Project->getConfig('piwik.settings.id');

        if (empty($piwikUrl) || empty($piwikSideId)) {
            return;
        }

        $piwik = '
        <!-- Piwik -->
        <script type="text/javascript">
          var _paq = _paq || [];
              _paq.push(["trackPageView"]);
              _paq.push(["enableLinkTracking"]);

          (function() {
              var u="//' . $piwikUrl . '/";
              _paq.push(["setTrackerUrl", u+"piwik.php"]);
              _paq.push(["setSiteId", ' . $piwikSideId . ']);

            var d=document, g=d.createElement("script"),
                s=d.getElementsByTagName("script")[0];

            g.type="text/javascript";
            g.async=true;
            g.defer=true;
            g.src=u+"piwik.js";

            s.parentNode.insertBefore(g,s);
          })();
        </script>
        <!-- End Piwik Code -->
        ';


        $Template->extendFooter($piwik);

    }
}
