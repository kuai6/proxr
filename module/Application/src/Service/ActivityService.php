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

    public function create($deviceId, $bit, $metadata)
    {
        $activity = new Activity();
        $activity->setStatus(ActivityStatus::STATUS_ACTIVE);


        /** @var Device $deviceRepository */
        $deviceRepository = $this->entityManager->getRepository(\Application\Entity\Device::class);
        /** @var \Application\Entity\Device $device */
        $device = $deviceRepository->find($deviceId);
        /** @var Bank $bank */
        $bank = $device->getBanks()->first();

        $activity->setDevice($device);
        $activity->setBank($bank);
        $activity->setBit($bit);

        $activity->setEvent('event.contactClosure');

        $activity->setMetadata($metadata);
        $activity->setOn('rise');

        $this->entityManager->persist($activity);
        $this->entityManager->flush($activity);
    }

    /**
     * @return array
     */
    public function list()
    {
        $repository = $this->entityManager->getRepository(Activity::class);
        return $repository->findAll();
    }
}