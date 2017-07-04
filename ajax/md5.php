<?php

/**
 * This file contains package_quiqqer_piwik_ajax_md5
 */

/**
 * Convert a string to md5
 *
 * @param string $str - String to convert
 * @return string
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_piwik_ajax_md5',
    function ($str) {
        return md5($str);
    },
    array('str'),
    'Permission::checkAdminUser'
);
