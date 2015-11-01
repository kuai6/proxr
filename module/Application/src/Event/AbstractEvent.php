<?php
namespace Application\Event;

use Zend\EventManager\Event;
use Zend\EventManager\EventInterface as BaseEventInterface;

/**
 * Class AbstractEvent
 * @package Application\Event
 */
class AbstractEvent extends Event
    implements EventInterface, BaseEventInterface
{
}