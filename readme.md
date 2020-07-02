## Online Store
REST API to get product details.
 
### To install the dependencies run 
    composer install

### To run the project 
	php artisan serve

### To run the tests
	php artisan test


### To get the products 
	curl --location --request GET 'http://127.0.0.1:8000/api/products?page=1&limit=50' \
	--header 'Content-Type: application/json' \
	--header 'lang: us'

`lang` header code can be either `us`(default), `france`, `bahasa`

`page` and `limit` also accept as query params
	
