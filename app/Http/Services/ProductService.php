<?php


namespace App\Http\Services;


use App\Http\Helpers\CurrencyManipulator;
use App\Http\Helpers\CvsHandler;
use App\Http\Helpers\Configurations;
use Exception;

final class ProductService
{
    /**
     * @param string $langCode
     * @param int $page
     * @param int $itemsPerPage
     * @return array|string[]
     * @throws Exception
     */
    public function getProducts(string $langCode = 'us', int $page = 1, int $itemsPerPage = 100): array
    {
        $offset = ($page - 1) * $itemsPerPage;
        $configurations = Configurations::getProductConfigurations($langCode);
        $products = CvsHandler::getAll(
            $configurations['productFilePath'],
            $configurations['productKeys'], $itemsPerPage, $offset
        );

        return $this->fillLangSpecificData($products, $configurations);
    }

    /**
     * @param array $products
     * @param array $configurations
     * @return array
     * @throws Exception
     */
    private function fillLangSpecificData(array $products, array $configurations): array
    {
        $exchangeRate = CurrencyManipulator::getExchangeRate($configurations['currencyCode']);
        foreach ($products as $key => $product) {
            $convertedPrice = CurrencyManipulator::convertTo($exchangeRate, $product['price']);
            $formattedPrice = CurrencyManipulator::formatPrice($configurations['currencyCode'], $convertedPrice);

            $langSpecificData = [];
            try {
                $langSpecificData = CvsHandler::findRecord(
                    $configurations['langFilePath'],
                    [0 => $product['code']], $configurations['langSpecificKeys']
                );
            } catch (Exception $e) {
            }
            $langSpecificData['price'] = $convertedPrice;
            $langSpecificData['displayPrice'] = $formattedPrice;

            $products[$key] = array_merge($products[$key], $langSpecificData);
        }
        return $products;
    }
}