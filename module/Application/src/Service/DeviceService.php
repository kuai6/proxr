<?php

namespace Application\Service;

use Application\Entity\Bank;
use Application\Entity\Device;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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
            ->setPort($port);
        $device->setStatus($status);

        $this->save($device);
        $bank->setDevice($device);
        $this->bankService->save($bank);
        $device->setBanks(new ArrayCollection([$bank]));
        return $device;
    }

    public function ping($serial, $ip, $port)
    {

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
}