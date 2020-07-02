<?php

namespace Tests\Unit;

use App\Http\Services\ProductService;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testProductService()
    {
        $product = [
            [
                'code' => 'SKU-1000',
                'price' => '100',
                'size' => 'XS',
                'image' => 'http://sku1000-black.jpg'
            ]
        ];

        $productLang = [
            'name' => 'Lace Sleeve Formal Evening Gown',
            'category' => 'Ladies',
            'subcategory' => 'Party Dress',
        ];

        $expectedResponse = [
            [
                'code' => 'SKU-1000',
                'price' => 112.0,
                'size' => 'XS',
                'image' => 'http://sku1000-black.jpg',
                'name' => 'Lace Sleeve Formal Evening Gown',
                'category' => 'Ladies',
                'subcategory' => 'Party Dress',
                'displayPrice' => '$ 112.00',
            ]
        ];

        $externalMock = Mockery::mock('overload:App\Http\Helpers\CvsHandler');
        $externalMock->shouldReceive('getAll')
            ->once()
            ->andReturn($product);
        $externalMock->shouldReceive('findRecord')
            ->once()
            ->andReturn($productLang);

        $externalMock = \Mockery::mock('alias:App\Http\Helpers\CurrencyManipulator');
        $externalMock->shouldReceive('getExchangeRate')
            ->once()
            ->andReturn(1.12);
        $externalMock->shouldReceive('convertTo')
            ->once()
            ->andReturn(112.00);
        $externalMock->shouldReceive('formatPrice')
            ->once()
            ->andReturn('$ 112.00');

        $products = (new ProductService())->getProducts();
        $this->assertEquals($expectedResponse, $products);
    }
}
