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
    'package_quiqqer_piwik_ajax_ecommerce_getTrackDataForOrderProcess',
    function ($orderHash) {
        try {
            $OrderProcess = new QUI\ERP\Order\OrderProcess([
                'orderHash' => $orderHash
            ]);

            $Order = $OrderProcess->getOrder();

            if (!$Order) {
                return [];
            }

            $Articles = $Order->getArticles();

            if (!$Articles) {
                return [];
            }
        } catch (QUI\Exception $Exception) {
            return [];
        }

        $Locale   = QUI::getLocale();
        $list     = $Articles->toArray();
        $articles = $list['articles'];

        // generate result
        $result = [];

        foreach ($articles as $article) {
            $category   = '';
            $categoryId = '';
            $categories = '';

            try {
                $Product = Products::getProduct((int)$article['id']);

                // categories
                $Category   = $Product->getCategory();
                $categories = $Product->getCategories();

                if ($Category) {
                    $category   = $Category->getTitle($Locale);
                    $categoryId = $Category->getId();
                }

                $categories = \array_map(function ($Category) use ($Locale) {
                    /* @var $Category \QUI\ERP\Products\Category\Category */
                    return [
                        'id'    => $Category->getId(),
                        'title' => $Category->getTitle($Locale)
                    ];
                }, $categories);
            } catch (QUI\Exception $Exception) {
                // nothing
                QUI\System\Log::addDebug($Exception->getMessage());
            }


            $result['products'][] = [
                'id'         => $article['id'],
                'category'   => $category,
                'categoryId' => $categoryId,
                'categories' => $categories,
                'title'      => $article['title'],
                'productNo'  => $article['articleNo'],
                'price'      => $article['sum']
            ];
        }

        $result['sum']          = $list['calculations']['sum'];
        $result['subSum']       = $list['calculations']['subSum'];
        $result['nettoSum']     = $list['calculations']['nettoSum'];
        $result['nettoSubSum']  = $list['calculations']['nettoSubSum'];
        $result['vatArray']     = $list['calculations']['vatArray'];
        $result['vatText']      = $list['calculations']['vatText'];
        $result['isEuVat']      = $list['calculations']['isEuVat'];
        $result['isNetto']      = $list['calculations']['isNetto'];
        $result['currencyData'] = $list['calculations']['currencyData'];

        return $result;
    },
    ['orderHash']
);
