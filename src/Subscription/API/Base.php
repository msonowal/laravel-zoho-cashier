<?php

namespace Msonowal\Laravel\Zoho\Cashier\Subscription\API;

use GuzzleHttp\Client;
//use Illuminate\Cache\Repository as Cache;
//use Iluminate\Cache\CacheManager as Cache;

class Base
{
    protected $token;

    protected $organizationId;

    protected $Cache;

    protected $Client;

    protected $ttl;

    public function __construct($token, $organizationId, $Cache, $ttl = 15)
    {
        $this->token          = $token;
        $this->organizationId = $organizationId;
        $this->ttl            = $ttl;
        $this->Cache          = $Cache;
        $this->Client         = new Client(['base_uri' => 'https://subscriptions.zoho.com/api/v1/', 'timeout' => 300]);
    }

    public function getFromCache($key)
    {
        if ($this->Cache->has($key)) {
            return $this->Cache->get($key);
        }

        return false;
    }

    public function saveToCache($key, $values)
    {
        return $this->Cache->add($key, $values, $this->ttl);
    }

    public function deleteCacheByKey($key)
    {
        $this->Cache->forget($key);
    }

    protected function sendRequest($method, $uri, array $headers = [], $body = null)
    {
        $response = $this->Client->request($method, $uri, ['headers' => $this->getRequestHeaders($headers), 'body' => $body, 'timeout' => 300]);

        $data = json_decode($response->getBody()->getContents(), true);

        if ($data['code'] != 0) {
            throw new \Exception('Zoho Api subscription error : ' . $data['message']);
        }

        return $data;
    }

    protected function getRequestHeaders(array $headers = [])
    {
        $defaultHeaders = [
            'Authorization'                           => 'Zoho-authtoken ' . $this->token,
            'X-com-zoho-subscriptions-organizationid' => $this->organizationId,
        ];

        return array_merge($defaultHeaders, $headers);
    }
}