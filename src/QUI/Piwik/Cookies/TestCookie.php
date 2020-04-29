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
class TestCookie implements CookieInterface
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return '_pk_testcookie.*';
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
        return QUI::getLocale()->get('quiqqer/piwik', 'cookie.test.purpose');
    }

    /**
     * @inheritDoc
     */
    public function getLifetime(): string
    {
        return \sprintf(
            '%d %s',
            1,
            QUI::getLocale()->get('quiqqer/quiqqer', 'second')
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
