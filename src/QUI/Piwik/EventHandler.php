<?php

namespace QUI\Piwik;

use QUI;

/**
 * Class EventHandler
 *
 * @package QUI\Piwik
 *
 * @author PCSG (Jan Wennrich)
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
        $piwikSiteId = Piwik::getSiteId($Project);

        $langSettings = $Project->getConfig('piwik.settings.langdata');

        if ($langSettings) {
            $settings = json_decode($langSettings, true);
            $lang     = $Project->getLang();

            if (isset($settings[$lang])) {
                if (isset($settings[$lang]['url'])
                    && !empty($settings[$lang]['url'])
                    && empty($piwikUrl)
                ) {
                    $piwikUrl = $settings[$lang]['url'];
                }
            }
        }

        if (empty($piwikUrl) || empty($piwikSiteId)) {
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
            'piwikSideId' => $piwikSiteId
        ));

        $Template->extendFooter(
            $Engine->fetch(dirname(__FILE__) . '/piwik.html')
        );
    }


    /**
     * Fired when updating the package
     *
     * @param QUI\Package\Package $Package
     */
    public static function onPackageUpdate(QUI\Package\Package $Package)
    {
        if ($Package->getName() == 'quiqqer/piwik') {
            Patches::moveSiteIdsToLocaleVariables();
        }
    }


    /**
     * Listens to project config save
     *
     * @param $project
     * @param array $config
     * @param array $params
     */
    public static function onProjectConfigSave($project, array $config, array $params)
    {
        try {
            $Project = QUI::getProject($project);
        } catch (QUI\Exception $Exception) {
            return;
        }

        $siteIds = json_decode($params['matomo.siteIds'], true);

        Piwik::setSiteIds($siteIds, $Project);
    }
}
