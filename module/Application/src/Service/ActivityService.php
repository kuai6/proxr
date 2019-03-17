<?php

namespace Application\Service;

use Application\Entity\Activity;
use Application\Entity\Bank;
use Application\Entity\Status\Activity as ActivityStatus;
use Application\EntityRepository\Device;
use Doctrine\ORM\EntityManager;


/**
 * Class ActivityService
 * @package Application\Service
 */
class ActivityService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var BankService
     */
    private $bankService;

    /**
     * ActivityService constructor.
     * @param EntityManager $entityManager
     * @param BankService $bankService
     */
    public function __construct(EntityManager $entityManager, BankService $bankService)
    {
        $this->entityManager = $entityManager;
        $this->bankService = $bankService;
    }

    public function create(int $deviceId, int $bankId, int $bit): Activity
    {
        $activity = new Activity();
        $activity->setStatus(ActivityStatus::STATUS_ACTIVE);


        /** @var Device $deviceRepository */
        $deviceRepository = $this->entityManager->getRepository(\Application\Entity\Device::class);
        /** @var \Application\Entity\Device $device */
        $device = $deviceRepository->find($deviceId);
        /** @var Bank $bank */
        $bank = $device->getBanks()->filter(function ($bank) use($bankId) {
            /** @var Bank $bank */
            return $bank->getId() == $bankId;
        })->first();

        $activity->setDevice($device);
        $activity->setBank($bank);
        $activity->setBit($bit);

        $activity->setEvent('event.contactClosure');
        $activity->setOn('rise');

        $this->entityManager->persist($activity);
        $this->entityManager->flush($activity);

        return $activity;
    }

    public function list(): array
    {
        $repository = $this->entityManager->getRepository(Activity::class);
        return $repository->findAll();
    }

    public function get(int $id): Activity
    {
        $repository = $this->entityManager->getRepository(Activity::class);
        return $repository->find($id);
    }

    public function update(int $id, Activity $activity): Activity
    {
        $repository = $this->entityManager->getRepository(Activity::class);
        $entity = $repository->find($id);

        $entity->setName($activity->getName());
        $entity->setDescription($activity->getDescription());
        $entity->setLinks($activity->getLinks());
        $entity->setNodes($activity->getNodes());
        $entity->setMetadata($activity->getMetadata());

        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);

        return $entity;
    }
}
