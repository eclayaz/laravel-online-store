<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CurrencyManipulator;
use App\Http\Helpers\CvsHandler;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $langCode = strtolower($request->lang);
        if (empty($langCode)) {
            $langCode = 'us';
        }

        try {
            $languageSpecificDetails = $this->getLanguageSpecificDetails($langCode);
            $products = $this->getProducts(1);
            $products = $this->fillAdditionalData($products, $langCode, $languageSpecificDetails);
        } catch (\Exception $e) {
            $statusCode = ($e->getCode() === 0) ? 500 : $e->getCode();
            $message = $e->getMessage() ?: 'Sorry, something went wrong.';
            return response()->json([
                'error' => $message
            ], $statusCode);
        }

        return response()->json($products, 200);
    }

    /**
     * @param string $lang
     * @return array
     * @throws Exception
     */
    private function getLanguageSpecificDetails(string $lang) : array
    {
        $filePath = storage_path('app/csv/product_%s.csv');
        switch (strtolower($lang)) {
            case 'us':
                return [
                    'currencyCode' => 'USD',
                    'keys' => [1 => 'name', 2 => 'category', 3 => 'subcategory'],
                    'filePath' => sprintf($filePath, 'us'),
                ];
            case 'france':
                return [
                    'currencyCode' => 'EUR',
                    'keys' => [1 => 'subcategory', 2 => 'name', 3 => 'category'],
                    'filePath' => sprintf($filePath, 'france'),
                ];
            case 'indonesia':
                return [
                    'currencyCode' => 'IDR',
                    'keys' => [1 => 'category', 2 => 'quantity', 3 => 'subcategory', 4 => 'name'],
                    'filePath' => sprintf($filePath, 'indonesia'),
                ];
        }

        throw new Exception('Unsupported language', 400);
    }

    /**
     * @param int $page
     * @return array|string[]
     * @throws Exception
     */
    private function getProducts(int $page = 1) : array
    {
        $file = storage_path('app/csv/product.csv');
        $itemsPerPage = 100;
        $offset = ($page - 1) * $itemsPerPage + 1;
        $keys = ['code', 'price', 'size', 'image'];
        return CvsHandler::getAll($file, $keys, $itemsPerPage, $offset);
    }

    /**
     * @param array $products
     * @param array $languageSpecificDetails
     * @return array
     * @throws Exception
     */
    private function fillAdditionalData(array $products, array $languageSpecificDetails) : array
    {
        $exchangeRate = CurrencyManipulator::getExchangeRate($languageSpecificDetails['currencyCode']);
        foreach ($products as $key => $product) {
            $convertedPrice = CurrencyManipulator::convertTo($exchangeRate, $product['price']);
            $formattedPrice = CurrencyManipulator::formatPrice($languageSpecificDetails['currencyCode'], $convertedPrice);

            $productAdditionalData = [];
            try {
                $productAdditionalData = CvsHandler::findRecord($languageSpecificDetails['filePath'], [0 => $product['code']], $languageSpecificDetails['keys']);
            } catch (Exception $e) {
            }
            $productAdditionalData['price'] = $convertedPrice;
            $productAdditionalData['displayPrice'] = $formattedPrice;

            $products[$key] = array_merge($products[$key], $productAdditionalData);
        }
        return $products;
    }
}
