<?php

namespace Application\Command\Adapter\Exception;

use Application\Command\Exception;

/**
 * Class InvalidArgumentException
 * @package Application\Command\Adapter\Exception
 */
class InvalidArgumentException extends Exception\InvalidArgumentException implements  ExceptionInterface
{
}
