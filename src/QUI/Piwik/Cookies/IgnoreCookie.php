<?php

/**
 * @author PCSG (Jan Wennrich)
 */
namespace QUI\Piwik\Cookies;

use QUI;
use QUI\GDPR\CookieInterface;
use QUI\Piwik\CookieUtils;

/**
 * Class QuiqqerSessionCookie
 *
 * @package QUI\GDPR\Cookies
 */
class IgnoreCookie implements CookieInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'matomo_ignore';
    }

    /**
     * @inheritDoc
     */
    public function getOrigin(): string
    {
        try {
            return QUI::getRewrite()->getProject()->getConfig('piwik.settings.url');
        } catch (QUI\Exception $Exception) {
            return QUI::getRequest()->getHost();
        }
    }

    /**
     * @inheritDoc
     */
    public function getPurpose(): string
    {
        return QUI::getLocale()->get('quiqqer/piwik', 'cookie.ignore.purpose');
    }

    /**
     * @inheritDoc
     */
    public function getLifetime(): string
    {
        return QUI::getLocale()->get('quiqqer/piwik', 'cookie.ignore.lifetime');
    }

    /**
     * @inheritDoc
     */
    public function getCategory(): string
    {
        return CookieUtils::getCookieCategorySetting();
    }
}
