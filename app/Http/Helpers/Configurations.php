<?php


namespace App\Http\Helpers;


use Exception;

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
            case 'us':
                $config['currencyCode'] = 'USD';
                $config['langSpecificKeys'] = [1 => 'name', 2 => 'category', 3 => 'subcategory'];
                $config['langFilePath'] = sprintf($langFilePath, 'us');
                return $config;
            case 'france':
                $config['currencyCode'] = 'EUR';
                $config['langSpecificKeys'] = [1 => 'subcategory', 2 => 'name', 3 => 'category'];
                $config['langFilePath'] = sprintf($langFilePath, 'france');
                return $config;
            case 'bahasa':
                $config['currencyCode'] = 'IDR';
                $config['langSpecificKeys'] = [1 => 'category', 2 => 'quantity', 3 => 'subcategory', 4 => 'name'];
                $config['langFilePath'] = sprintf($langFilePath, 'indonesia');
                return $config;
        }

        throw new Exception('Unsupported language', 400);
    }

}