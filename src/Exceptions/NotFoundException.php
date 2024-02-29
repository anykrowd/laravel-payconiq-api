<?php

namespace Anykrowd\PayconiqApi\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('Resource not found.');
    }
}
