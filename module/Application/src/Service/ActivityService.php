<?php

namespace Application\Service;

use Application\Entity\Activity;
use Application\Entity\Bank;
use Application\EntityRepository\Device;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zend\EventManager\EventManagerInterface;

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
        /** @var EntityRepository $statusRepository */
        $statusRepository = $this->entityManager->getRepository(\Application\Entity\Status\Activity::class);
        /** @var \Application\Entity\Status\Activity $activityStatus */
        $activityStatus = $statusRepository->find(2);
        $activity->setStatus($activityStatus);


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