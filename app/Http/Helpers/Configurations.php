<?php


namespace App\Http\Helpers;


use Exception;
use App\Http\Enums\Currency;
use App\Http\Enums\Language;

final class Configurations
{

    /**
     * @param string $lang
     * @return array
     * @throws Exception
     */
    public static function getProductConfigurations(string $lang): array
    {
        $productFilePath = storage_path('app/csv/product.csv');
        $langFilePath = storage_path('app/csv/product_%s.csv');

        $config = [
            'productFilePath' => $productFilePath,
            'productKeys' => ['code', 'price', 'size', 'image'],
        ];
        switch (strtolower($lang)) {
            case Language::US:
                $config['currencyCode'] = Currency::USD;
                $config['langSpecificKeys'] = [1 => 'name', 2 => 'category', 3 => 'subcategory'];
                $config['langFilePath'] = sprintf($langFilePath, 'us');
                return $config;
            case Language::FRANCE:
                $config['currencyCode'] =  Currency::EUR;
                $config['langSpecificKeys'] = [1 => 'subcategory', 2 => 'name', 3 => 'category'];
                $config['langFilePath'] = sprintf($langFilePath, 'france');
                return $config;
            case Language::BAHASA:
                $config['currencyCode'] = Currency::IDR;
                $config['langSpecificKeys'] = [1 => 'category', 2 => 'quantity', 3 => 'subcategory', 4 => 'name'];
                $config['langFilePath'] = sprintf($langFilePath, 'indonesia');
                return $config;
            default:
                throw new Exception('Unsupported language '.$lang, 400);
        }

    }

}