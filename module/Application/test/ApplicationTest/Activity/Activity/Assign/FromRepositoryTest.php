<?php

namespace ApplicationTest\Activity\Assign;

use Application\Activity\Activity\Assign\FromRepository;
use Application\Activity\ActivityManager;
use Application\Activity\Context;
use Application\Entity\Device;
use Doctrine\ORM\EntityManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class FromRepositoryTest
 * @package ApplicationTest\Activity\Assign
 */
class FromRepositoryTest extends AbstractHttpControllerTestCase
{
    /**
     * Reset the application for isolation
     */
    protected function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../../../config/application.config.php'
        );
        parent::setUp();
    }

    /**
     *
     */
    public function testCreate()
    {
        /** @var ActivityManager $activityManager */
        $activityManager = $this->getApplicationServiceLocator()->get(ActivityManager::class);
        /** @var EntityManager $entityManager */
        $entityManager = $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');

        $repository = $entityManager->getRepository(Device::class);

        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService('Application\EntityRepository\SomeRepository', $repository);

        $testCase = [
            '<assignFromRepository name="contextVariable" repository="Application\EntityRepository\SomeRepository" id="contestIdVariable" />' => $repository->findOneBy(['id' => 1]),
            '<assignFromRepository name="contextVariable" repository="Application\EntityRepository\SomeRepository" action="create" />' => new Device(),
        ];

        foreach($testCase as $metadata => $expectedValue) {
            /** @var FromRepository $assignFromRepository */
            $assignFromRepository = $activityManager->get('assignFromRepository');
            static::assertInstanceOf(FromRepository::class, $assignFromRepository);
            $assignFromRepository->fromMetadata(new \SimpleXMLElement($metadata));
            $context = new Context();
            $context->set('contestIdVariable', 1);
            $context->set('serviceLocator', $this->getApplicationServiceLocator());
            $assignFromRepository->execute($context);
            static::assertTrue($context->has('contextVariable'));
            static::assertEquals($expectedValue, $context->get('contextVariable'));
        }
    }
}
