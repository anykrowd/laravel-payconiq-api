<?php

namespace Anykrowd\PayconiqApi\Api;

class Payment extends ApiEntity
{
    /**
     * Get payment.
     *
     * @return object
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $id)
    {
        return json_decode(
            $this->payconiqApi->get('payments/' . $id)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * Search payments.
     *
     * @param  array  $search
     * @param  int  $page
     * @param  int  $size
     * @return object
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function search($search = ['from' => null], $page = 0, $size = 50)
    {
        $data = [
            'headers' => ['content-type' => 'application/json'],
            'json' => $search,
        ];

        return json_decode(
            $this->payconiqApi->post('payments/search?' . http_build_query(['page' => $page, 'size' => $size]), $data)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * Create payment.
     *
     * @param  array  $parameters
     * @return object
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create($parameters)
    {
        $data = [
            'headers' => ['content-type' => 'application/json'],
            'body' => json_encode($parameters),
        ];

        return json_decode(
            $this->payconiqApi->post('payments', $data)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * Cancel payment.
     *
     * @param  string  $id
     * @return object
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function cancel(string $id)
    {
        return json_decode(
            $this->payconiqApi->delete('payments/' . $id)
                ->getBody()
                ->getContents()
        );
    }

    /**
     * Refund payment.
     *
     * @param  string  $id
     * @return object
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refund(string $id)
    {
        return json_decode(
            $this->payconiqApi->get('payments/' . $id . '/debtor/refundIban')
                ->getBody()
                ->getContents()
        );
    }

    /**
     * Create refund for payment.
     *
     * @param  string  $id
     * @return object
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refunds(string $id, int $amount, string $idempotencyKey)
    {
        $data = [
            'headers' => [
                'content-type' => 'application/json',
                'Signature' => 'Bearer ' . $this->payconiqApi->getAccessToken(),
                'Idempotency-Key' => $idempotencyKey,
            ],
            'body' => json_encode([
                'amount' => $amount,
            ]),
        ];

        return json_decode(
            $this->payconiqApi->post('payments/' . $id . '/refunds', $data)
                ->getBody()
                ->getContents()
        );
    }
}
