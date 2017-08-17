<?php

namespace Msonowal\Laravel\Zoho\Cashier\Subscription\API;

class HostedPage extends Base
{
    public function getHostedPageById(string $hostedPageId): array
    {
        $response = $this->sendRequest('GET', sprintf('hostedpages/%s', $hostedPageId));

        return $response;
    }

    public function listHostedPages(): array
    {
        $response = $this->sendRequest('GET', 'hostedpages', ['content-type' => 'application/json']);

        return $response;
    }

    public function createSubscription(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/newsubscription', ['content-type' => 'application/json'], json_encode($data));

        return $response;
    }

    public function updateSubscription(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/updatesubscription', ['content-type' => 'application/json'], json_encode($data));

        return $response;
    }

    public function updateCard(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/updatecard', ['content-type' => 'application/json'], json_encode($data));

        return $response;
    }

    public function buyOneTimeAddon(array $data): array
    {
        $response = $this->sendRequest('POST', 'hostedpages/buyonetimeaddon', ['content-type' => 'application/json'], json_encode($data));

        return $response;
    }

    public function retrieveHostedPageFromSubscriptionId(string $subscriptionId): array
    {
        $hostedPages = $this->listHostedPages(['subscription_id' => $subscriptionId]);

        foreach ($hostedPages as $hostedPage) {
            if (!empty($hostedPage['data'])) {
                if ($hostedPage['data']['subscription']['subscription_id'] == $subscriptionId) {
                    return $hostedPage;
                }
            }
        }

        return null;
    }
}
