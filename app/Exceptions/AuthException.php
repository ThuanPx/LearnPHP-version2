<?php

namespace App\Exceptions;

use Exception;

class AuthException extends Exception
{
    public function __construct($errorCode, $errorMessage)
    {
        $this->code = $errorCode;
        $this->message = $errorMessage;
    }
}
