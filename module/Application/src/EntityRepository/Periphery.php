<?php

namespace Application\EntityRepository;

use Doctrine\ORM\EntityRepository;

class Periphery extends EntityRepository
{
    public function findByBit($deviceId, $bankId, $bit)
    {
        return $this->findOneBy(['device' => $deviceId, 'bank' => $bankId, 'bit' => $bit]);
    }

    public function findByDevice($device_id)
    {
        return $this->findBy(['device'=> $device_id]);
    }

    public function save($entity)
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush($entity);
        return $entity;
    }
}
