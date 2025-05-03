<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class PawoonService
{
    protected $client;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('PAWOON_API_URL', 'https://open-api.pawoon.com'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ]
        ]);

        $this->authenticate();
    }

    protected function authenticate()
    {
        $clientId = env('PAWOON_CLIENT_ID');
        $clientSecret = env('PAWOON_CLIENT_SECRET');

        $body = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ];

        $response = $this->client->post('/oauth/token', [
            'json' => $body
        ]);

        $data = json_decode($response->getBody(), true);
        $this->accessToken = $data['access_token'];
    }

    protected function authHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->accessToken,
            'Accept' => 'application/json'
        ];
    }

    public function getOutlets()
    {
        $response = $this->client->get('/outlets', [
            'headers' => $this->authHeaders()
        ]);
        return json_decode($response->getBody(), true);
    }


    public function getProducts($page = 1, $perPage = 25, $categoryId = null)
    {
        $query = [
            'outlet_id' => '130a3a60-ee98-11ef-8e7e-3ffdc9e6f717',
            'page' => $page,
            'per_page' => $perPage,
        ];
    
        if ($categoryId) {
            $query['product_category_id'] = $categoryId;
        }
    
        $response = $this->client->get('/products?' . http_build_query($query), [
            'headers' => $this->authHeaders()
        ]);
    
        return json_decode($response->getBody(), true);
    }
    

    public function getCategories()
    {
        $response = $this->client->get('/products/categories', [
            'headers' => $this->authHeaders()
        ]);
        return json_decode($response->getBody(), true);
    }
    
    public function getProductsByCategory($categoryId = null)
    {
        $url = '/products';
        if ($categoryId) {
            $url .= '?category_id=' . $categoryId;
        }
    
        $response = $this->client->get($url, [
            'headers' => $this->authHeaders()
        ]);
        return json_decode($response->getBody(), true);
    }
    
    public function getProductDetail($id)
    {
        $response = $this->client->get('/products?id=' . $id, [
            'headers' => $this->authHeaders()
        ]);
    
        $data = json_decode($response->getBody(), true);
    
        return $data['data'][0] ?? null;
    }

    public function getTaxes()
    {
        $response = $this->client->get('/company-payment-methods', [
            'headers' => $this->authHeaders()
        ]);
        return json_decode($response->getBody(), true);
    }
    


    public function createOrder(array $orderDetails)
    {
        $response = $this->client->post('/orders', [
            'headers' => array_merge([
                'Content-Type' => 'application/json'
            ], $this->authHeaders()),
            'json' => [
                'data' => $orderDetails
            ]
        ]);
    
        return json_decode($response->getBody(), true);
    }
    

    public function updateOrderStatus($orderId, $status)
    {
        $response = $this->client->put("/orders/{$orderId}/status", [
            'headers' => $this->authHeaders(),
            'json' => ['status' => $status]
        ]);
        return json_decode($response->getBody(), true);
    }

    public function processPayment($orderId, $paymentData)
    {
        $response = $this->client->post("/orders/{$orderId}/payment", [
            'headers' => $this->authHeaders(),
            'json' => $paymentData
        ]);
        return json_decode($response->getBody(), true);
    }
}
