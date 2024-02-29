<?php

namespace Anykrowd\PayconiqApi;

use Anykrowd\PayconiqApi\Api\Payment;
use GuzzleHttp\Client as GuzzleClient;

class PayconiqApiClient
{
    use Request;

    /**
     * The api endpoint.
     */
    private ?string $apiEndpoint;

    /**
     * The bearer token.
     */
    private ?string $bearerToken;

    /**
     * The client.
     */
    protected GuzzleClient $guzzle;

    /**
     * Create a new PayconiqApi instance.
     *
     * @return void
     */
    public function __construct(?string $apiEndpoint = null, ?string $bearerToken = null)
    {
        $this->apiEndpoint = $apiEndpoint;
        $this->bearerToken = $bearerToken;

        $this->setClient($this->apiEndpoint, $this->bearerToken);
    }

    /**
     * Create the HTTP client.
     *
     * @param  string  $uri
     * @param  string|null  $bearerToken
     * @return object
     */
    public function setClient($uri, $bearerToken)
    {
        $config = ['base_uri' => $uri];

        if ($bearerToken != null) {
            $config['headers'] = ['Authorization' => 'Bearer '.$bearerToken];
        }

        $this->guzzle = new GuzzleClient($config);

        return $this;
    }

    /**
     * Return the HTTP client.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        return $this->guzzle;
    }

    /**
     * Return the payment object.
     *
     * @return \Anykrowd\PayconiqApi\Api\Payment
     */
    public function payment()
    {
        return new Payment($this);
    }
}
