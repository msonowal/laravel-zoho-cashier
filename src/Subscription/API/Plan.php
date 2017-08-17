<?php

namespace Msonowal\Laravel\Zoho\Cashier\Subscription\API;

/**
 * Plan.
 *
 * @author Tristan Perchec <tristan.perchec@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#plans
 */
class Plan extends Base
{
    public static $addonTypes = [
        'recurring',
        'one_time',
    ];

    /**
     * Returns all plans.
     *
     * @param array       $filters associative array of filters
     *
     * @param bool        $withAddons
     * @param string|null $addonType
     *
     * @return array
     */
    public function listPlans(array $filters = [], bool $withAddons = true, string $addonType = null): array
    {
        $cacheKey = 'plans';
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', 'plans');

            $plans = $response;
            $hit   = $plans['plans'];

            $this->saveToCache($cacheKey, $hit);
        }

        $hit = $this->filterPlans($hit, $filters);

        if ($withAddons) {
            $hit = $this->getAddonsForPlan($hit, $addonType);
        }

        return $hit;
    }

    /**
     * Returns a Plan by its identifier.
     *
     * @param string $planCode
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getPlan(string $planCode): array
    {
        $cacheKey = sprintf('plan_%s', $planCode);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('plans/%s', $planCode));

            $data = $response;
            $plan = $data['plan'];

            $this->saveToCache($cacheKey, $plan);

            return $plan;
        }

        return $hit;
    }

    /**
     * get reccurent addons for given plan.
     *
     * @param array       $plans
     * @param string|null $addonType
     *
     * @return array
     */
    public function getAddonsForPlan(array $plans, string $addonType = null): array
    {
        $addonApi = new Addon($this->token, $this->organizationId, $this->Cache, $this->ttl);

        foreach ($plans as &$plan) {
            $addons = [];

            foreach ($plan['addons'] as $planAddon) {
                $addon = $addonApi->getAddon($planAddon['addon_code']);

                if (null !== $addonType) {
                    if (($addon['type'] == $addonType) && (in_array($addonType, self::$addonTypes))) {
                        $addons[] = $addon;
                    }
                } else {
                    $addons[] = $addon;
                }
            }

            $plan['addons'] = $addons;
        }

        return $plans;
    }

    /**
     * filter given plans with given filters.
     *
     * @param array $plans
     * @param array $filters
     *
     * @return array
     */
    public function filterPlans(array $plans, array $filters): array
    {
        foreach ($filters as $key => $filter) {
            if (array_key_exists($key, current($plans))) {
                $plans = array_filter($plans, function ($element) use ($key, $filter) {
                    return $element[$key] == $filter;
                });
            }
        }

        return $plans;
    }

    /**
     * get price by planCode
     *
     */
    public function getPriceByPlanCode(string $planCode): float
    {
        $plan = $this->getPlan($planCode);

        return (array_key_exists('recurring_price', $plan)) ? $plan['recurring_price'] : 0;
    }
}
