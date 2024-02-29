<?php

namespace Anykrowd\PayconiqApi\HeaderCheckers;

use Jose\Component\Checker\HeaderChecker;
use Jose\Component\Checker\InvalidHeaderException;

/**
 * Class PayconiqJtiChecker
 */
final class PayconiqJtiChecker implements HeaderChecker
{
    private const HEADER_NAME = 'https://payconiq.com/jti';

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
