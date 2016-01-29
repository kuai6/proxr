<?php

namespace ApplicationTest\Activity;

use Application\Activity\Activity\Activity;
use Application\Activity\ActivityManager;
use Application\Activity\Invoker;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class InvokerTest
 * @package ApplicationTest\Activity
 */
class InvokerTest extends AbstractHttpControllerTestCase
{
    protected $testXmlFile = __DIR__ .'/../../.data/Activity/activity.xml';


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

    public function testInvoke()
    {
        /** @var Invoker $activityInvoker */
        $activityInvoker = $this->getApplicationServiceLocator()->get(Invoker::class);
        static::assertInstanceOf(Invoker::class, $activityInvoker);

        /** @var ActivityManager $activityManager */
        $activityManager = $this->getApplicationServiceLocator()->get(ActivityManager::class);
        /** Firs time create */
        $this->getApplicationServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var Activity $activity */
        $activity = $activityManager->get('activity');
        static::assertInstanceOf(Activity::class, $activity);
        /** @var string $metadata */
        $metadata = file_get_contents($this->testXmlFile);
        $activityInvoker->invoke($activity, [], $metadata);
        /** @var \SimpleXMLElement $metadata */
        $metadata = new \SimpleXMLElement($metadata);
        $attributes = $metadata->attributes();
        static::assertEquals((string)$attributes['event'], $activity->getEvent());
        static::assertEquals((string)$attributes['bit'], $activity->getBit());
        static::assertEquals((string)$attributes['on'], $activity->getOn());
    }
}
