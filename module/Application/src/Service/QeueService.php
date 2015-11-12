<?php

namespace Application\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class QeueService
 * @package Application\Service
 */
class QeueService extends AbstractService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
}
