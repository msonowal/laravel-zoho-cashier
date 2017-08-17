<?php
declare(strict_types=1);

namespace Msonowal\Laravel\Zoho\Cashier\Subscription\API;

/**
 * @author Hang Pham <thi@yproximite.com>
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 *
 * @link https://www.zoho.com/subscriptions/api/v1/#invoices
 */
class Invoice extends Base
{
    /**
     * @param string $customerId The customer's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function listInvoicesByCustomer(string $customerId): array
    {
        $cacheKey = sprintf('zoho_invoices_%s', $customerId);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('invoices?customer_id=%s', $customerId));

            $result = $response;

            $invoices = $result['invoices'];

            $this->saveToCache($cacheKey, $invoices);

            return $invoices;
        }

        return $hit;
    }

    /**
     * @param string $invoiceId The invoice's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getInvoice(string $invoiceId)
    {
        $cacheKey = sprintf('zoho_invoice_%s', $invoiceId);
        $hit      = $this->getFromCache($cacheKey);

        if (false === $hit) {
            $response = $this->sendRequest('GET', sprintf('invoices/%s', $invoiceId));

            $result = $response;

            $invoice = $result['invoice'];

            $this->saveToCache($cacheKey, $invoice);

            return $invoice;
        }

        return $hit;
    }

    /**
     * @param string $invoiceId The invoice's id
     *
     * @throws \Exception
     *
     * @return array
     */
    public function getInvoicePdf(string $invoiceId)
    {
        $response = $this->sendRequest('GET', sprintf('invoices/%s', $invoiceId), [
            'query' => ['accept' => 'pdf'],
        ]);

        return $response;
    }
}
