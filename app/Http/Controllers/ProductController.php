<?php

namespace App\Http\Controllers;

use App\Http\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $langCode = strtolower($request->header('lang'));
        if (empty($langCode)) {
            $langCode = 'us';
        }

        $page = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit', 100);

        try {
            $products = (new ProductService())->getProducts($langCode, $page, $limit);
        } catch (\Exception $e) {
            $statusCode = ($e->getCode() === 0) ? 500 : $e->getCode();
            $message = $e->getMessage() ?: 'Sorry, something went wrong.';
            return response()->json([
                'error' => $message
            ], $statusCode);
        }

        return response()->json(['data' => $products], 200);
    }
}
