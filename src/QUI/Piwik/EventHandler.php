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
    public static function onTemplateSiteFetch($Template, $Site)
    {
        $Project     = $Site->getProject();
        $piwikUrl    = $Project->getConfig('piwik.settings.url');
        $piwikSideId = $Project->getConfig('piwik.settings.id');

        $langSettings = $Project->getConfig('piwik.settings.langdata');

        if ($langSettings) {
            $ettings = json_decode($langSettings, true);
            $lang    = $Project->getLang();

            if (isset($ettings[$lang])) {
                if (isset($ettings[$lang]['url'])) {
                    $piwikUrl = $ettings[$lang]['url'];
                }

                if (isset($ettings[$lang]['id'])) {
                    $piwikSideId = $ettings[$lang]['id'];
                }
            }
        }

        if (empty($piwikUrl) || empty($piwikSideId)) {
            return;
        }

        $Engine = QUI::getTemplateManager()->getEngine();

        $Engine->assign(array(
            'piwikUrl' => $piwikUrl,
            'piwikSideId' => $piwikSideId
        ));

        $Template->extendFooter(
            $Engine->fetch(dirname(__FILE__) . '/piwik.html')
        );
    }
}
