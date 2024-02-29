<?php

namespace Anykrowd\PayconiqApi;

use Anykrowd\PayconiqApi\Exceptions\PayconiqCallbackSignatureVerificationException;
use Anykrowd\PayconiqApi\Exceptions\PayconiqJWKSetException;
use Anykrowd\PayconiqApi\HeaderCheckers\PayconiqIssChecker;
use Anykrowd\PayconiqApi\HeaderCheckers\PayconiqIssuedAtChecker;
use Anykrowd\PayconiqApi\HeaderCheckers\PayconiqJtiChecker;
use Anykrowd\PayconiqApi\HeaderCheckers\PayconiqPathChecker;
use Anykrowd\PayconiqApi\HeaderCheckers\PayconiqSubChecker;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use Jose\Component\Checker\AlgorithmChecker;
use Jose\Component\Checker\HeaderCheckerManager;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSLoader;
use Jose\Component\Signature\JWSTokenSupport;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class PayconiqCallbackSignatureVerifier
{
    private const TIMEOUT = 10;

    private const CONNECT_TIMEOUT = 2;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var JWSLoader
     */
    private $jwsLoader;

    public string $certificatesUrl;

    public string $certificatesCacheKey;

    /**
     * PayconiqCallbackSignatureVerifier constructor.
     */
    public function __construct(
        string $paymentProfileId,
        string $certificatesUrl,
        string $certificatesCacheKey,
        ?ClientInterface $httpClient = null,
        ?AdapterInterface $cache = null,
    ) {
        $this->certificatesUrl = $certificatesUrl;
        $this->certificatesCacheKey = $certificatesCacheKey;

        if ($httpClient === null) {
            $httpClient = new Client([
                RequestOptions::TIMEOUT => self::TIMEOUT,
                RequestOptions::CONNECT_TIMEOUT => self::CONNECT_TIMEOUT,
            ]);
        }

        if ($cache === null) {
            $cache = new FilesystemAdapter();
        }

        $this->httpClient = $httpClient;
        $this->cache = $cache;

        $this->jwsLoader = $this->initializeJwsLoader($paymentProfileId);
    }

    public function isValid(string $token, ?string $payload = null, ?int $signature = 0): bool
    {
        try {
            $this->jwsLoader->loadAndVerifyWithKeySet($token, $this->getJWKSet(), $signature, $payload);
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws PayconiqCallbackSignatureVerificationException
     */
    public function loadAndVerifyJWS(string $token, ?string $payload = null, ?int $signature = 0): JWS
    {
        try {
            return $this->jwsLoader->loadAndVerifyWithKeySet($token, $this->getJWKSet(), $signature, $payload);
        } catch (\Throwable $e) {
            throw new PayconiqCallbackSignatureVerificationException(
                sprintf('Something went wrong while loading and verifying the JWS. Error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws PayconiqJWKSetException
     */
    private function getJWKSet(): JWKSet
    {
        try {
            $url = $this->certificatesUrl;
            $cacheKey = $this->certificatesCacheKey;

            $JWKSetJson = $this->cache->get(
                $cacheKey,
                function (ItemInterface $item) use ($url) {
                    $item->expiresAfter(CarbonInterval::hour(12));

                    $response = $this->httpClient->get($url);

                    return $response->getBody()->getContents();
                }
            );

            return JWKSet::createFromJson($JWKSetJson);
        } catch (\Throwable $e) {
            throw new PayconiqJWKSetException(
                sprintf('Something went wrong while fetching the JWK Set. Error: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }
    }

    private function initializeJwsLoader(string $paymentProfileId): JWSLoader
    {
        return new JWSLoader(
            new JWSSerializerManager([
                new CompactSerializer(),
            ]),
            new JWSVerifier(
                new AlgorithmManager([
                    new ES256(),
                ])
            ),
            new HeaderCheckerManager(
                [
                    new AlgorithmChecker(['ES256']),
                    new PayconiqSubChecker($paymentProfileId),
                    new PayconiqIssChecker(),
                    new PayconiqIssuedAtChecker(),
                    new PayconiqJtiChecker(),
                    new PayconiqPathChecker(),
                ],
                [
                    new JWSTokenSupport(),
                ]
            )
        );
    }
}
