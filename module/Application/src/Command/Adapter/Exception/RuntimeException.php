<?php

namespace Application\Command\Adapter\Exception;

use Application\Command\Exception;

/**
 * Class RuntimeException
 * @package Application\Command\Adapter\Exception
 */
class RuntimeException extends Exception\RuntimeException implements ExceptionInterface
{
}
