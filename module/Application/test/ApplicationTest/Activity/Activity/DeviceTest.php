<?php

namespace ApplicationTest\Activity\Activity;

use Application\Activity\Activity\Device;
use Application\Activity\ActivityManager;
use Application\Activity\Context;
use Doctrine\ORM\EntityManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class DeviceTest
 * @package ApplicationTest\Activity\Activity
 */
class DeviceTest extends AbstractHttpControllerTestCase
{
    /** @var string  */
    protected $setXmlOn = '<device device="DeviceVariableFromContest" action="set" bank="1" bit="1" value="1" />';
    protected $setXmlOff = '<device device="DeviceVariableFromContest" action="set" bank="1" bit="1" value="0"  />';

    /** @var string  */
    protected $getXml = '<device device="DeviceVariableFromContest" action="get" bank="2" bit="1" out="DeviceBitValue" />';

    /**
     * Reset the application for isolation
     */
    protected function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../config/application.config.php'
        );
        parent::setUp();
    }

    public function testSet()
    {
        /** @var ActivityManager $activityManager */
        $activityManager = $this->getApplicationServiceLocator()->get(ActivityManager::class);
        /** @var Device $deviceActivity */
        $deviceActivity = $activityManager->get('device');
        static::assertInstanceOf(Device::class, $deviceActivity);
        /** @var EntityManager $entityManager */
        $entityManager = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');

        $testCase = [
            $this->setXmlOn,
            $this->setXmlOff
        ];

        foreach ($testCase as $xmlMetadata) {
            $metadata = new \SimpleXMLElement($xmlMetadata);
            $context = new Context();
            $context->setMetadata($metadata);
            $context->set('activity', $deviceActivity);
            $context->set('serviceLocator', $this->getApplicationServiceLocator());

            try {
                $deviceActivity->fromMetadata($metadata);
            } catch (\Exception $e) {
                throw $e;
            }
            $attributes = $metadata->attributes();

            static::assertEquals((string)$attributes['device'], $deviceActivity->getDeviceVariable());
            static::assertEquals((string)$attributes['action'], $deviceActivity->getAction());
            static::assertEquals((string)$attributes['bank'], $deviceActivity->getBank());
            static::assertEquals((string)$attributes['bit'], $deviceActivity->getBit());
            static::assertEquals((string)$attributes['value'], $deviceActivity->getValue());
            static::assertEquals((string)$attributes['out'], $deviceActivity->getOut());

            /** @var \Application\Entity\Device $deviceEntity */
            $deviceEntity = $entityManager->getRepository(\Application\Entity\Device::class)->findOneBy(['id' => 3]);
            $context->set('DeviceVariableFromContest', $deviceEntity);
            $deviceActivity->execute($context);
        }
    }

    public function testGet()
    {
        /** @var ActivityManager $activityManager */
        $activityManager = $this->getApplicationServiceLocator()->get(ActivityManager::class);
        /** @var Device $deviceActivity */
        $deviceActivity = $activityManager->get('device');
        static::assertInstanceOf(Device::class, $deviceActivity);
        /** @var EntityManager $entityManager */
        $entityManager = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');

        $metadata = new \SimpleXMLElement($this->getXml);
        $context = new Context();
        $context->setMetadata($metadata);
        $context->set('activity', $deviceActivity);
        $context->set('serviceLocator', $this->getApplicationServiceLocator());

        try {
            $deviceActivity->fromMetadata($metadata);
        } catch (\Exception $e) {
            throw $e;
        }
        $attributes = $metadata->attributes();

        static::assertEquals((string)$attributes['device'], $deviceActivity->getDeviceVariable());
        static::assertEquals((string)$attributes['action'], $deviceActivity->getAction());
        static::assertEquals((string)$attributes['bank'], $deviceActivity->getBank());
        static::assertEquals((string)$attributes['bit'], $deviceActivity->getBit());
        static::assertEquals((string)$attributes['out'], $deviceActivity->getOut());

        /** @var \Application\Entity\Device $deviceEntity */
        $deviceEntity = $entityManager->getRepository(\Application\Entity\Device::class)->findOneBy(['id' => 3]);
        $context->set('DeviceVariableFromContest', $deviceEntity);
        $deviceActivity->execute($context);
    }
}
