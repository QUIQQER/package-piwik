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
class SesCookie implements CookieInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return '_pk_ses.*';
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
        return QUI::getLocale()->get('quiqqer/piwik', 'cookie.ses.purpose');
    }

    /**
     * @inheritDoc
     */
    public function getLifetime(): string
    {
        return \sprintf(
            '%d %s',
            30,
            QUI::getLocale()->get('quiqqer/quiqqer', 'minutes')
        );
    }

    /**
     * @inheritDoc
     */
    public function getCategory(): string
    {
        return CookieUtils::getCookieCategorySetting();
    }
}
