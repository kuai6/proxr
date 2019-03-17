<?php

namespace Application\EntityRepository;

use Application\Entity\Periphery\PeripheryType as PeripheryTypeEntity;
use Doctrine\ORM\EntityRepository;

class PeripheryType extends EntityRepository
{
    public function save(PeripheryTypeEntity $peripheryType): PeripheryTypeEntity
    {
        $em = $this->getEntityManager();
        $em->persist($peripheryType);
        $em->flush($peripheryType);

        return $peripheryType;
    }

}
