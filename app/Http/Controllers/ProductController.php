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
            $currencyCode = $this->getCurrencyCode($langCode);
            $products = $this->getProducts(1);
            $products = $this->fillAdditionalData($products, $langCode, $currencyCode);
        } catch (\Exception $e) {
            $statusCode = ($e->getCode() === 0) ? 500 : $e->getCode();
            $message = $e->getMessage() ?: 'Sorry, something went wrong.';
            return response()->json([
                'error' => $message
            ], $statusCode);
        }

        return response()->json($products, 200);
    }

    private function getCurrencyCode($lang)
    {
        switch (strtolower($lang)) {
            case 'us':
                return 'USD';
            case 'france':
                return 'EUR';
            case 'indonesia':
                return 'IDR';
        }

        throw new Exception('Unsupported language', 400);
    }

    /**
     * @param int $page
     * @return array|string[]
     * @throws Exception
     */
    private function getProducts($page = 1)
    {
        $file = storage_path('app/csv/product.csv');
        $itemsPerPage = 100;
        $offset = ($page - 1) * $itemsPerPage + 1;
        $keys = ['code', 'price', 'size', 'image'];
        return CvsHandler::getAll($file, $keys, $itemsPerPage, $offset);
    }

    /**
     * @param array $products
     * @param string $langCode
     * @param string $lang
     * @return array
     * @throws Exception
     */
    private function fillAdditionalData($products, $langCode, $lang)
    {
        $file = storage_path('app/csv/product_'.$langCode.'.csv');
        $keys = [1 => 'name', 2 => 'category', 3 => 'subcategory'];
        $exchangeRate = CurrencyManipulator::getExchangeRate($lang);
        foreach ($products as $key => $product) {
            $convertedPrice = CurrencyManipulator::convertTo($exchangeRate, $product['price']);
            $formattedPrice = CurrencyManipulator::formatPrice($lang, $convertedPrice);

            $productAdditionalData = [];
            try {
                $productAdditionalData = CvsHandler::findRecord($file, [0 => $product['code']], $keys);
            } catch (Exception $e) {
            }
            $productAdditionalData['price'] = $convertedPrice;
            $productAdditionalData['displayPrice'] = $formattedPrice;

            $products[$key] = array_merge($products[$key], $productAdditionalData);
        }
        return $products;
    }
}
