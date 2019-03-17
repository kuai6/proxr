<?php

namespace Application\Service;

use Application\Entity\Periphery\PeripheryUnit;
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

    public function listTypes(): array
    {
        $typesRepo = $this->entityManager->getRepository(PeripheryType::class);
        return $typesRepo->findAll();
    }

    public function createType(PeripheryType $peripheryType): PeripheryType
    {
        return $this->saveType($peripheryType);
    }

    private function saveType(PeripheryType $newType): PeripheryType
    {
        $this->entityManager->persist($newType);
        $this->entityManager->flush($newType);
        return $newType;
    }

    public function findUnit(int $device_id, int $bank_id, int $bit): PeripheryUnit
    {

    }

    public function getUnit(int $id): PeripheryUnit
    {}

    public function listAllUnits(): array
    {
        $typesRepo = $this->entityManager->getRepository(PeripheryUnit::class);
        return $typesRepo->findAll();
    }

    public function listDeviceUnits(int $device_id): array
    {}

    public function registerUnit(string $device_id, string $periphery_type): PeripheryUnit
    {
    }
}
