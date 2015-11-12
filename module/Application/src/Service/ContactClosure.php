<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class ContactClosure
 * @package Application\Service
 */
class ContactClosure extends AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
}
