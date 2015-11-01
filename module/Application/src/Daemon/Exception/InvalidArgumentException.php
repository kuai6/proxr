<?php
namespace Application\Daemon\Exception;

/**
 * Exception class raised when invalid arguments are discovered
 *
 * Class InvalidArgumentException
 * @package Application\Daemon\Exception
 */
class InvalidArgumentException
    extends \InvalidArgumentException
    implements ExceptionInterface
{
}
