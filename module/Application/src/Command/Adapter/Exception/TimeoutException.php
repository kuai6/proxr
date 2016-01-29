<?php

namespace Application\Command\Adapter\Exception;

/**
 * Class TimeoutException
 * @package Application\Command\Adapter\Exception
 */
class TimeoutException extends RuntimeException implements ExceptionInterface
{
    const READ_TIMEOUT = 1000;
}
