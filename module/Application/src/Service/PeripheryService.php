<?php

namespace Application\Service;

use Doctrine\ORM\EntityManager;
use Application\Entity\Periphery\PeripheryType;

class PeripheryService
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * PeripheryService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array|object[]
     */
    public function listTypes()
    {
        $typesRepo = $this->entityManager->getRepository(PeripheryType::class);
        return $typesRepo->findAll();
    }

    /**
     * @param $peripheryType
     * @return PeripheryType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createType($peripheryType)
    {
        return $this->save($peripheryType);
    }

    /**
     * @param PeripheryType $newType
     * @return PeripheryType
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function save(PeripheryType $newType)
    {
        $this->entityManager->persist($newType);
        $this->entityManager->flush($newType);
        return $newType;
    }

}
