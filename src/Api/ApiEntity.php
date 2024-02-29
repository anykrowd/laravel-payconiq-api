<?php

namespace Anykrowd\PayconiqApi\Api;

class ApiEntity
{
    /**
     * The payconiqApi object.
     */
    public object $payconiqApi;

    /**
     * Create a new object instance.
     *
     * @return void
     */
    public function __construct(object $payconiqApi)
    {
        $this->payconiqApi = $payconiqApi;
    }
}
