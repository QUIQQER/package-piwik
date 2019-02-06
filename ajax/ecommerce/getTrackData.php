<?php

/**
 * This file contains package_quiqqer_piwik_ajax_ecommerce_getTrackData
 */

use QUI\ERP\Products\Handler\Fields;
use QUI\ERP\Products\Handler\Products;

use QUI\ERP\Order\Handler as OrderHandler;

/**
 * Return the data from the watchlist
 *
 * @param integer $watchlistId
 * @return array
 */
QUI::$Ajax->registerFunction(
    'package_quiqqer_piwik_ajax_ecommerce_getTrackData',
    function ($basketId) {
        try {
            $Basket = OrderHandler::getInstance()->getBasketById($basketId);
        } catch (QUI\Exception $Exception) {
            return [];
        }

        $Locale = QUI::getLocale();
        $List   = $Basket->getProducts();;

        $list     = $List->toArray();
        $products = $list['products'];

        // generate result
        $result = [];

        foreach ($products as $product) {
            $Product = Products::getProduct($product['id']);

            // categories
            $Category   = $Product->getCategory();
            $categories = $Product->getCategories();

            $category   = '';
            $categoryId = '';

            if ($Category) {
                $category   = $Category->getTitle($Locale);
                $categoryId = $Category->getId();
            }

            $categories = array_map(function ($Category) use ($Locale) {
                /* @var $Category \QUI\ERP\Products\Category\Category */
                return [
                    'id'    => $Category->getId(),
                    'title' => $Category->getTitle($Locale)
                ];
            }, $categories);

            // price
            $price = 0;

            if (!QUI\ERP\Products\Utils\Package::hidePrice()) {
                $price = $Product->getPrice()->getPrice();
            }

            $result['products'][] = [
                'id'         => $Product->getId(),
                'category'   => $category,
                'categoryId' => $categoryId,
                'categories' => $categories,
                'title'      => $Product->getTitle($Locale),
                'productNo'  => $Product->getField(Fields::FIELD_PRODUCT_NO)->getValue(),
                'price'      => $price
            ];
        }

        $result['sum']          = $list['sum'];
        $result['subSum']       = $list['subSum'];
        $result['nettoSum']     = $list['nettoSum'];
        $result['nettoSubSum']  = $list['nettoSubSum'];
        $result['vatArray']     = $list['vatArray'];
        $result['vatText']      = $list['vatText'];
        $result['isEuVat']      = $list['isEuVat'];
        $result['isNetto']      = $list['isNetto'];
        $result['currencyData'] = $list['currencyData'];

        return $result;

    },
    ['basketId']
);
