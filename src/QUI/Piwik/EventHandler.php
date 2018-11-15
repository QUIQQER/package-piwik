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
            $settings = json_decode($langSettings, true);
            $lang     = $Project->getLang();

//            var_dump($piwikUrl);
//            exit;

            if (isset($settings[$lang])) {
                if (isset($settings[$lang]['url'])
                    && !empty($settings[$lang]['url'])
                    && empty($piwikUrl)) {
                    $piwikUrl = $settings[$lang]['url'];
                }

                if (isset($settings[$lang]['id']) && !empty($settings[$lang]['id'])) {
                    $piwikSideId = $settings[$lang]['id'];
                }
            }
        }

        if (empty($piwikUrl) || empty($piwikSideId)) {
            return;
        }

        try {
            $Engine = QUI::getTemplateManager()->getEngine();
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::addDebug($Exception->getMessage());

            return;
        }

        $Engine->assign(array(
            'piwikUrl'    => $piwikUrl,
            'piwikSideId' => $piwikSideId
        ));

        $Template->extendFooter(
            $Engine->fetch(dirname(__FILE__) . '/piwik.html')
        );
    }
}
