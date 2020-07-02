<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * get products without any parameter
     * @return void
     */
    public function testProductsWithoutLanguageHeader()
    {
        $response = $this->getJson('/api/products');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'code',
                        'price',
                        'size',
                        'image',
                        'subcategory',
                        'name',
                        'category',
                        'displayPrice'
                    ]
                ]
            ]);
    }

    /**
     * call products with 'bahas' lang header code
     * response should contains 'quantity' as well
     * @return void
     */
    public function testProductsWithLanguageHeader()
    {
        $response = $this->getJson('/api/products', ['lang'=>'bahasa']);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'code',
                        'price',
                        'size',
                        'image',
                        'subcategory',
                        'name',
                        'category',
                        'displayPrice',
                        'quantity'
                    ]
                ]
            ]);
    }
}
