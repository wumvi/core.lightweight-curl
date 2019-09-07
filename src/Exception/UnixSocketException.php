<?php
declare(strict_types=1);

namespace LightweightCurl\Exception;

/**
 * Exception
 */
class UnixSocketException extends CurlException
{
    public const COULD_NOT_CONNECT = 1;
}
