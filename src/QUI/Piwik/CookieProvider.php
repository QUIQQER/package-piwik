<?php

/**
 * @author PCSG (Jan Wennrich)
 */
namespace QUI\Piwik;

use QUI\GDPR\CookieCollection;
use QUI\GDPR\CookieProviderInterface;
use QUI\Piwik\Cookies\ConsentCookie;
use QUI\Piwik\Cookies\CvarCookie;
use QUI\Piwik\Cookies\HsrCookie;
use QUI\Piwik\Cookies\IdCookie;
use QUI\Piwik\Cookies\IgnoreCookie;
use QUI\Piwik\Cookies\RefCookie;
use QUI\Piwik\Cookies\SesCookie;
use QUI\Piwik\Cookies\SessIdCookie;
use QUI\Piwik\Cookies\TestCookie;

/**
 * Class QuiqqerCookieProvider
 *
 * @package QUI\Piwik
 */
class CookieProvider implements CookieProviderInterface
{
    public static function getCookies(): CookieCollection
    {
        return new CookieCollection([
            new ConsentCookie(),
            new CvarCookie(),
            new HsrCookie(),
            new IdCookie(),
            new IgnoreCookie(),
            new RefCookie(),
            new SesCookie(),
            new SessIdCookie(),
            new TestCookie()
        ]);
    }
}
