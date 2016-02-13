<?php

/**
 * This file contains QUI\Piwik\Piwik
 */
namespace QUI\Piwik;

use QUI;
use QUI\Projects\Project;

/**
 * Piwik Helper
 *
 * @package QUI\Piwik
 */
class Piwik
{
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

        return $Piwik;
    }
}
