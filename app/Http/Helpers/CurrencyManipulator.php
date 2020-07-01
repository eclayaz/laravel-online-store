<?php


namespace App\Http\Helpers;


use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyManipulator
{
    /**
     * @param string $currencyCode
     * @return float
     * @throws Exception
     */
    public static function getExchangeRate(string $currencyCode) : float
    {
        if ($currencyCode === 'EUR') {
            return 1;
        }

        try {
            $client = new Client();
            $api_url = 'https://api.exchangeratesapi.io/latest';
            $response = $client->request('GET', $api_url)->getBody()->getContents();
            $response = json_decode($response);
            foreach ($response->rates as $currency => $rate) {
                if ($currency === $currencyCode) {
                    return $rate;
                }
            }
            return .0;
        } catch (GuzzleException $exception) {
            throw new Exception('Sorry something went wrong while fetching exchange rates.', 500);
        }
    }

    /**
     * @param float $exchangeRate
     * @param float $price
     * @return float
     */
    public static function convertTo(float $exchangeRate, float $price) : float
    {
        return number_format($price * $exchangeRate, 2, '.', '');
    }

    /**
     * @param string $lang
     * @param float $price
     * @return string
     */
    public static function formatPrice(string $lang, float $price) : string
    {
        switch ($lang) {
            case 'USD':
                return '$ ' . number_format($price, 2, '.', ',');
            case 'EUR':
                return '€ ' . number_format($price, 2, '.', ',');
            case 'IDR':
                return 'Rp ' . number_format($price, 0, '.', '.');
        }

        return money_format('%.2n', $price);
    }
}