<?php

namespace Application\EntityRepository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class Periphery extends EntityRepository
{
    public function findByBit($deviceId, $bankId, $bit)
    {
        return $this->findOneBy(
            Criteria::create()
                ->where(Criteria::expr()->eq('device_id', $deviceId))
                ->andWhere(Criteria::expr()->eq('bank_id', $bankId))
                ->andWhere(Criteria::expr()->eq('bit', $bit))
        );
    }

}
