<?php

namespace ApplicationTest\Entity;

use Application\Entity\Bank;
use Application\Entity\Device;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class DeviceTest
 * @package ApplicationTest\Entity
 */
class DeviceTest extends AbstractHttpControllerTestCase
{
    /**
     * Reset the application for isolation
     */
    protected function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testDevice()
    {
        $serviceLocator = $this->getApplicationServiceLocator();

        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $entityManager->getRepository(Device::class);
        static::assertInstanceOf(\Application\EntityRepository\Device::class, $deviceRepository);

        /** @var \Application\Entity\Status\Device $status */
        $status = $entityManager->find(\Application\Entity\Status\Device::class, 1);
        static::assertInstanceOf(\Application\Entity\Status\Device::class, $status);

        $device = new Device();

        $device->setName(2);
        $device->setStatus($status);
        $device->setBanks(new ArrayCollection([
            new Bank\ContactClosure(),
            new Bank\Relay()
        ]));
        /** @var Bank $bank */
        foreach ($device->getBanks() as $bank) {
            $bank->setDevice($device);
        }

        $entityManager->beginTransaction();
        try {
            $entityManager->persist($device);
            $entityManager->flush($device);
            $entityManager->commit();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
