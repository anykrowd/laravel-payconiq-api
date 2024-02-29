<?php

use Anykrowd\PayconiqApi\Api\Payment;
use Anykrowd\PayconiqApi\PayconiqApiClient;
use GuzzleHttp\Client as GuzzleClient;

it('can create PayconiqApiClient instance', function () {
    $api = new PayconiqApiClient();

    expect($api)->toBeInstanceOf(PayconiqApiClient::class);
});

it('can set client with bearer token', function () {
    $api = new PayconiqApiClient('your-bearer-token');

    expect($api->setClient('https://example.com', 'your-bearer-token'))->toBe($api);
    expect($api->getClient())->toBeInstanceOf(GuzzleClient::class);
});

it('can set client without bearer token', function () {
    $api = new PayconiqApiClient();

    expect($api->setClient('https://example.com', null))->toBe($api);
    expect($api->getClient())->toBeInstanceOf(GuzzleClient::class);
});

it('can create payment object', function () {
    $api = new PayconiqApiClient();
    $payment = $api->payment();

    expect($payment)->toBeInstanceOf(Payment::class);
});
