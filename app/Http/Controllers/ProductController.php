<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CurrencyManipulator;
use App\Http\Helpers\CvsHandler;
use Exception;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        try {
            $products = $this->getProducts(1);
            $products = $this->fillAdditionalData($products, 'IDR');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Sorry, something went wrong!'
            ], $e->getCode());
        }

        return response()->json($products, 200);
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

    private function fillAdditionalData($products, $lang)
    {
        $file = storage_path('app/csv/product_us.csv');
        $keys = [1 => 'name', 2 => 'category', 3 => 'subcategory'];
        $exchangeRate = CurrencyManipulator::getExchangeRate($lang);
        foreach ($products as $key => $product) {
            $convertedPrice = CurrencyManipulator::convertTo($exchangeRate, $product['price']);
            $formattedPrice = CurrencyManipulator::formatPrice($lang, $convertedPrice);

            $productAdditionalData = [];
            try {
                $productAdditionalData = CvsHandler::findRecord($file, [0 => $product['code']], $keys);
            } catch (Exception $e) {}
            $productAdditionalData['price'] = $convertedPrice;
            $productAdditionalData['displayPrice'] = $formattedPrice;
            
            $products[$key] = array_merge($products[$key], $productAdditionalData);
        }
        // add data

        return $products;
    }
}
