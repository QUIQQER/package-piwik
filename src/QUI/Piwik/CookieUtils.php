<?php

/**
 * @author PCSG (Jan Wennrich)
 */
namespace QUI\Piwik;

use QUI\Exception;
use QUI\System\Log;

/**
 * Class CookieUtils
 *
 * @package QUI\Piwik
 */
class CookieUtils
{
    /**
     * Returns the category that should be used for the Matomo cookies.
     *
     * @return string
     */
    public static function getCookieCategorySetting(): string
    {
        try {
            return \QUI::getRewrite()->getProject()->getConfig('piwik.settings.cookiecategory');
        } catch (Exception $Exception) {
            Log::writeException($Exception);
        }

        return 'statistics';
    }
}
