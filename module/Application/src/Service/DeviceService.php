<?php

namespace Application\Service;

use Application\Entity\Bank;
use Application\Entity\Device;
use Application\Entity\EventLog;
use Application\Event\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Zend\EventManager\EventManager;

/**
 * Class DeviceService
 * @package Application\Service
 */
class DeviceService
{
    const TYPE_CONTACT_CLOSURE  = 'COCL';
    const TYPE_RELAY            = 'RELY';
    const TYPE_DAC              = 'TDAC';
    const TYPE_ADC              = 'TADC';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var BankService
     */
    private $bankService;

    /**
     * DeviceService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, BankService $bankService)
    {
        $this->entityManager = $entityManager;
        $this->bankService = $bankService;
    }

    /**
     * @param $type
     * @param $serial
     * @param $ip
     * @param $port
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     */
    public function registerDevice($type, $serial, $ip, $port)
    {
        $bank = null;

        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $this->entityManager->getRepository(Device::class);
        /** @var Device $device */
        $device = $deviceRepository->findOneBy(['name' => $serial]);
        if(null !== $device) {
            $device->setIp($ip);
            $device->setPort($port);

            return $this->save($device);
        }

        switch (strtoupper($type)) {
            case self::TYPE_RELAY;
                $bank = new Bank\Relay();
                break;

            case self::TYPE_CONTACT_CLOSURE:
                $bank = new Bank\ContactClosure();
                break;

            case self::TYPE_ADC:
                $bank = new Bank\Adc();
                break;

            case self::TYPE_DAC:
                $bank = new Bank\Dac();
                break;
            default;
                throw new \RuntimeException('Unknown type');
        }

        /** @var EntityRepository $statusRepository */
        $statusRepository = $this->entityManager->getRepository(\Application\Entity\Status\Device::class);
        /** @var \Application\Entity\Status\Device $status */
        $status = $statusRepository->find(1);
        $device = new Device();
        $device->setName($serial);
        $device->setIp($ip)
            ->setPort($port)
            ->setLastPing(new \DateTime())
            ->setStatus($status);

        $this->save($device);
        $bank->setDevice($device);
        $this->bankService->save($bank);
        $device->setBanks(new ArrayCollection([$bank]));
        return $device;
    }

    /**
     * @param $serial
     * @param $ip
     * @param $port
     * @return null
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function ping($serial, $ip, $port)
    {
        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $this->entityManager->getRepository(Device::class);
        /** @var Device $device */
        $device = $deviceRepository->findOneBy(['name' => $serial]);
        if(null == $device) {
            return null;
        }

        $device->setLastPing(new \DateTime());

        $this->save($device);

        return null;
    }

    /**
     * @param Device $device
     * @return Device
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Device $device)
    {
        $this->entityManager->persist($device);
        $this->entityManager->flush($device);

        return $device;
    }


    public function handle($serial, $ip, $port, $data)
    {
        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $this->entityManager->getRepository(Device::class);
        /** @var Device $device */
        $device = $deviceRepository->findOneBy(['name' => $serial]);
        if (null == $device) {
            return null;
        }

        $bank = $data{0};
        $value = substr($data, 1);

        /** @var \Application\EntityRepository\Bank $bankRepository */
        $bankRepository = $this->entityManager->getRepository(Bank::class);
        /** @var Bank $bank */
        $bank = $bankRepository->findOneBy(['name' => $bank]);

        if (null == $bank) {
            return false;
        }

        $bankBits = [];
        for ($i = 0; $i < 8; $i++) {
            if (!empty($value{$i})) {
                $bankBits[$i] = $value{$i};
            }
        }

        //remove bit direction
        $bankRepository->saveBitsDBAL($bank->getDevice(), $bank->getName(), $bankBits);

        /** @var \Application\EntityRepository\EventLog $eventLogEntityRepository */
        $eventLogEntityRepository = $this->entityManager->getRepository(EventLog::class);
        /** @var EventLog $eventLog */
        $eventLog = $eventLogEntityRepository->saveLog($bank->getDevice(), $bank->getName(), $bankBits);
        $event = new Event();
        switch(true) {
            case $bank instanceof Bank\Dac:
                $event->setName(Event::EVENT_ADC);
                break;
            case $bank instanceof Bank\ContactClosure:
                $event->setName(Event::EVENT_CONTACT_CLOSURE);
                break;
        }

        $event->setDevice($bank->getDevice())
            ->setBank($bank->getName())
            ->setBits($bankBits)
            ->setEventLog($eventLog);
        $this->eventManager->trigger($event);
    }


    public function handleContactClosure($device, $data)
    {

    }
}