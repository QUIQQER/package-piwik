<?php

/**
 * This file contains package_quiqqer_piwik_ajax_ecommerce_getProductData
 */

use QUI\ERP\Products\Handler\Fields;
use QUI\ERP\Products\Handler\Products;

/**
 * Return the data from the watchlist
 *
 * @param integer $watchlistId
 * @return array
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_piwik_ajax_ecommerce_getProductData',
    function ($productId) {
        try {
            $Product = Products::getProduct($productId);

            return [
                'productNo' => $Product->getField(Fields::FIELD_PRODUCT_NO)->getValue(),
                'title'     => $Product->getTitle(),
                'category'  => $Product->getCategory()->getTitle(),
                'price'     => $Product->getPrice()->getPrice()
            ];
        } catch (QUI\Exception $Exception) {
            return '';
        }
    },
    ['project', 'siteId']
);
