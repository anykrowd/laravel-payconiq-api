<?php

namespace Anykrowd\PayconiqApi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Anykrowd\PayconiqApi\Skeleton\SkeletonClass
 */
class PayconiqApiFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payconiq-api';
    }
}
