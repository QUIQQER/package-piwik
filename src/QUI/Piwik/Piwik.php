<?php

namespace QUI\Piwik;

use QUI;
use QUI\Projects\Project;

/**
 * Piwik Helper
 *
 * @package QUI\Piwik
 *
 * @author PCSG (Jan Wennrich)
 */
class Piwik
{
    const LOCALE_KEY_SITE_IDS = 'matomo.siteID';

    /**
     * Return the piwik client
     *
     * @param Project $Project
     * @return \PiwikTracker
     */
    public static function getPiwikClient(Project $Project)
    {
        $piwikUrl    = $Project->getConfig('piwik.settings.url');
        $piwikSideId = $Project->getConfig('piwik.settings.id');

        $Piwik = new \PiwikTracker($piwikSideId, $piwikUrl);

        if ($Project->getConfig('piwik.settings.token')) {
            $Piwik->setTokenAuth($Project->getConfig('piwik.settings.token'));
        }

        return $Piwik;
    }


    /**
     * Returns the site ID for a given project and language
     *
     * @param Project $Project
     * @param $language
     * @return string
     */
    public static function getSiteId(Project $Project, $language = null)
    {
        $group = self::getLocaleGroup($Project);

        if (is_null($language)) {
            $language = $Project->getLang();
        }


        /**
         * Doesn't work at the moment because of a bug (I guess?)
         * @see https://dev.quiqqer.com/quiqqer/quiqqer/issues/791
         */
        /*
        if (!QUI::getLocale()->exists($group, self::LOCALE_KEY_SITE_IDS)) {
            return $Project->getConfig('piwik.settings.id');
        }
        */

        $siteId = QUI::getLocale()->getByLang(
            $language,
            $group,
            self::LOCALE_KEY_SITE_IDS
        );

        // No value set for this language, therefore return the general ID
        // TODO: replace with the code above, if the mentioned bug is fixed.
        if (empty($siteId) || $siteId == '[' . $group . '] ' . self::LOCALE_KEY_SITE_IDS) {
            return null;
        }

        return $siteId;
    }


    /**
     * Stores the given site IDs in the system (as locale variables).
     *
     * @param array $siteIds - e.g.: ['de' => 40, 'en' => 41, 'fr' => 42]
     * @param Project $Project
     */
    public static function setSiteIds($siteIds, Project $Project)
    {
        $localeKey   = self::LOCALE_KEY_SITE_IDS;
        $localeGroup = self::getLocaleGroup($Project);

        try {
            QUI\Translator::add(
                $localeGroup,
                $localeKey,
                $localeGroup
            );
        } catch (QUI\Exception $Exception) {
            // Throws error if lang var already exists
        }

        try {
            QUI\Translator::edit(
                $localeGroup,
                $localeKey,
                $localeGroup,
                $siteIds
            );
            QUI\Translator::publish($localeGroup);
        } catch (QUI\Exception $Exception) {
            QUI\System\Log::writeException($Exception);
        }
    }

    /**
     * Returns the name of the locale group used to store the site IDs.
     *
     * @param Project $Project
     * @return string
     */
    private static function getLocaleGroup(Project $Project): string
    {
        return 'project/' . $Project->getName();
    }
}
