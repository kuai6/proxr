<?php

namespace Application\Service;

use Application\Entity\Periphery\PeripheryUnit;
use Application\EntityRepository\Periphery as UnitRepo;
use Application\EntityRepository\PeripheryType as TypeRepo;
use Doctrine\ORM\EntityManager;
use Application\Entity\Periphery\PeripheryType;

class PeripheryService
{
    /**
     * @var DeviceService
     */
    private $deviceService;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /** @var TypeRepo */
    private $typesRepository;

    /** @var UnitRepo */
    private $unitsRepository;

    /**
     * PeripheryService constructor.
     * @param DeviceService $deviceService
     * @param EntityManager $entityManager
     * @param TypeRepo $typesRepository
     * @param UnitRepo $unitsRepository
     */
    public function __construct(DeviceService $deviceService, EntityManager $entityManager, TypeRepo $typesRepository, UnitRepo $unitsRepository)
    {
        $this->deviceService = $deviceService;
        $this->entityManager = $entityManager;
        $this->typesRepository = $typesRepository;
        $this->unitsRepository = $unitsRepository;
    }

    public function listTypes(): array
    {
        return $this->typesRepository->findAll();
    }

    public function createType(PeripheryType $peripheryType): PeripheryType
    {
        return $this->typesRepository->save($peripheryType);
    }

    public function findUnit(int $device_id, int $bank_id, int $bit): PeripheryUnit
    {
        return $this->unitsRepository->findByBit($device_id, $bank_id, $bit);
    }

    public function getUnit(int $id): PeripheryUnit
    {
        return $this->unitsRepository->find($id);
    }

    public function listAllUnits(): array
    {
        return $this->unitsRepository->findAll();
    }

    public function listDeviceUnits(int $device_id): array
    {
        return $this->unitsRepository->findByDevice($device_id);
    }

    public function registerUnit(string $device_id, string $periphery_type): PeripheryUnit
    {
        $device = $this->deviceService->get($device_id);
        /** @var PeripheryType $peripheryType */
        $peripheryType = $this->typesRepository->find($periphery_type);

        //find bank $peripheryType
        $bank = $device->getBanks()->first();
        //find available bit
        $bit = 0;

        $unit = new PeripheryUnit();
        $unit->setName('');
        $unit->setDescription('');
        $unit->setType($peripheryType);
        $unit->setDevice($device);
        $unit->setBank($bank);
        $unit->setBit($bit);

        return $this->unitsRepository->save($unit);
    }
}
