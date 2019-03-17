<?php

namespace Application\EntityRepository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

class Periphery extends EntityRepository
{
    public function findByBit($deviceId, $bankId, $bit)
    {
        return $this->findOneBy(['device_id' => $deviceId, 'bank_id' => $bankId, 'bit' => $bit]);
    }

    public function findByDevice($device_id)
    {
        return $this->findBy(['device_id', $device_id]);
    }

    public function save($entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush($entity);
        return $entity;
    }
}
