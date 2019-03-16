<?php

namespace Application\Service;

use Application\Entity\Bank;
use Application\Entity\Device;
use Application\Entity\EventLog;
use Application\Entity\Status\Device as DeviceStatus;
use Application\Event\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Zend\EventManager\EventManagerInterface;
use Zend\Log\Logger;

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
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @var BankService
     */
    private $bankService;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * DeviceService constructor.
     * @param EntityManager $entityManager
     * @param BankService $bankService
     * @param EventManagerInterface $eventManager
     * @param Logger $logger
     */
    public function __construct(
        EntityManager $entityManager,
        BankService $bankService,
        EventManagerInterface $eventManager,
        Logger $logger)
    {
        $this->entityManager = $entityManager;
        $this->bankService = $bankService;
        $this->eventManager = $eventManager;
        $this->logger = $logger;
    }

    /**
     * @param $serial
     * @param $ip
     * @param $port
     * @param $banks
     * @return Device
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function registerDevice($serial, $ip, $port, $banks)
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

        $device = new Device();
        $device->setName($serial);
        $device->setIp($ip)
            ->setPort($port)
            ->setLastPing(new \DateTime())
            ->setStatus(DeviceStatus::STATUS_ACTIVE);
        $device = $this->save($device);

        $banksCollection = new ArrayCollection();
        foreach($banks as $b) {
            switch (strtoupper($b['type'])) {
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
            $bank->setAvailableBitsCount($b['p_cnt']);
            $bank->setName($b['id']);
            $bank->setDevice($device);
            $bank = $this->bankService->save($bank);
            $banksCollection->add($bank);
        }

        $device->setBanks($banksCollection);
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

    /**
     * @param $serial
     * @param $ip
     * @param $port
     * @param $data
     * @return null
     */
    public function handle($serial, $ip, $port, $data)
    {
        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $this->entityManager->getRepository(Device::class);
        /** @var Device $device */
        $device = $deviceRepository->findOneBy(['name' => $serial]);
        if (null === $device) {
            return null;
        }

        $banksCount = ord($data{0});
        if ($banksCount == 0) {
            return null;
        }
        /** @var \Application\EntityRepository\Bank $bankRepository */
        $bankRepository = $this->entityManager->getRepository(Bank::class);
        $pos = 2;
        do {
            $bankId = ord(substr($data, $pos, 1 ));

            /** @var Bank $bank */
            $bank = $bankRepository->findOneBy(['name' => $bankId, 'device' => $device->getId()]);
            if (null === $bank) {
                return null;
            }

            $bitLength = 1;
            if ($bank instanceof Bank\Adc || $bank instanceof Bank\Dac) {
                $bitLength = 2;
            }

            $dLen = $bank->getAvailableBitsCount() * $bitLength;

            $this->handleBank($device, $bank, strpos($data, $pos+1, $dLen ));
            $pos = $pos + 1 + $dLen;
            $banksCount--;
        } while($banksCount > 0);

        return null;
    }


    private function handleBank(Device $device, Bank $bank, $data)
    {
        /** @var \Application\EntityRepository\Bank $bankRepository */
        $bankRepository = $this->entityManager->getRepository(Bank::class);

        $bankBits = [];
        if($bank instanceof Bank\Adc) {

            $bits = str_split($data, $bank->getAvailableBitsCount()*2);

            for ($i = 0; $i < sizeof($bits); $i ++ ) {
                $bankBits[$i] = ord($bits[$i]) ;
            }
        } else {
            $bankBits = [];
            for ($i = 0; $i < 8; $i++) {
                $bankBits[$i] = (!empty($value{$i})) ? $value{$i} : 0;
            }
        }

        //remove bit direction
        $bankRepository->saveBitsDBAL($bank->getDevice()->getId(), $bank->getName(), $bankBits);

        /** @var \Application\EntityRepository\EventLog $eventLogEntityRepository */
        $eventLogEntityRepository = $this->entityManager->getRepository(EventLog::class);
        /** @var EventLog $eventLog */
        $eventLog = $eventLogEntityRepository->saveLog($bank->getDevice()->getId(), $bank->getName(), $bankBits);
        $event = new Event();
        switch(true) {
            case $bank instanceof Bank\Adc:
                $event->setName(Event::EVENT_ADC);
                break;
            case $bank instanceof Bank\ContactClosure:
                $event->setName(Event::EVENT_CONTACT_CLOSURE);
                break;
        }

        $event->setDevice($bank->getDevice()->getId())
            ->setBank($bank->getName())
            ->setBits($bankBits)
            ->setEventLog($eventLog);
        $this->eventManager->trigger($event);
    }

    /**
     * @return array
     */
    public function devices()
    {
        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $this->entityManager->getRepository(Device::class);

        return $deviceRepository->findAll();

    }
}