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
class ConsentCookie implements CookieInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'mtm_consent';
    }

    /**
     * @inheritDoc
     */
    public function getOrigin(): string
    {
        return QUI::getRequest()->getHost();
    }

    /**
     * @inheritDoc
     */
    public function getPurpose(): string
    {
        return QUI::getLocale()->get('quiqqer/piwik', 'cookie.consent.purpose');
    }

    /**
     * @inheritDoc
     */
    public function getLifetime(): string
    {
        return QUI::getLocale()->get('quiqqer/piwik', 'cookie.consent.lifetime');
    }

    /**
     * @inheritDoc
     */
    public function getCategory(): string
    {
        return CookieUtils::getCookieCategorySetting();
    }
}
