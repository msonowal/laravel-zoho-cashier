<?php

namespace Msonowal\Laravel\Zoho\Cashier\Subscription\API;

/**
 * Addon.
 *
 * @author Tristan Perchec <tristan.perchec@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#addons
 */
class Addon extends Base
{
    /**
     * @param array $filters associative array of filters
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listAddons(array $filters = []): array
    {
        $cacheKey = 'addons';
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', $cacheKey);

            $addons = $response;
            $hit    = $addons['addons'];

            $this->saveToCache($cacheKey, $hit);
        }

        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, current($hit))) {
                $hit = array_filter($hit, function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $hit;
    }

    /**
     * @param string $addonCode
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getAddon(string $addonCode): array
    {
        $cacheKey = sprintf('addon_%s', $addonCode);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('addons/%s', $addonCode));

            $data  = $response;
            $addon = $data['addon'];

            $this->saveToCache($cacheKey, $addon);

            return $addon;
        }

        return $hit;
    }
}
