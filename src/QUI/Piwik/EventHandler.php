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
