<?php

namespace Anykrowd\PayconiqApi\HeaderCheckers;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

/**
 * Class PayconiqSubChecker
 */
final class PayconiqSubChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/sub';

    /**
     * @var string
     */
    private $paymentProfileId;

    /**
     * PayconiqSubChecker constructor.
     */
    public function __construct(string $paymentProfileId)
    {
        $this->paymentProfileId = $paymentProfileId;
    }

    /**
     * {@inheritdoc}
     */
    public function checkHeader($value): void
    {
        if (! is_string($value)) {
            throw new InvalidHeaderException(
                sprintf('"%s" must be a string.', self::HEADER_NAME),
                self::HEADER_NAME,
                $value
            );
        }

        if ($value !== $this->paymentProfileId) {
            throw new InvalidHeaderException(
                sprintf('"%s" should match the Payment profile ID', self::HEADER_NAME),
                self::HEADER_NAME,
                $value
            );
        }
    }

    public function supportedHeader(): string
    {
        return self::HEADER_NAME;
    }

    public function protectedHeaderOnly(): bool
    {
        return false;
    }
}
