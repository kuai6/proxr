<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * Class DeviceStatus
 * @package Application\Entity
 */
class DeviceStatus extends AbstractStatus
{
    const STATUS_ACTIVE = 'device.active';
}
