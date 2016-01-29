<?php

namespace Application\Entity\Status;

use Application\Entity\AbstractStatus;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 *
 * Class Status
 * @package Application\Entity\Status
 */
class Activity extends AbstractStatus
{
    const STATUS_ACTIVE = 'activity.active';
}
