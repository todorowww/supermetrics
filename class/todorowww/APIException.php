<?php


namespace todorowww;

use Throwable;

/**
 * Class APIException
 * @package todorowww
 *
 * Custom exception for xCurl
 */
class APIException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
