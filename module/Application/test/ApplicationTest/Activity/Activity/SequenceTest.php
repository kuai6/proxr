<?php

namespace ApplicationTest\Activity\Activity;

use Application\Activity\Activity\Sequence;
use Application\Activity\ActivityManager;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class SequenceTest
 * @package ApplicationTest\Activity\Activity
 */
class SequenceTest extends AbstractHttpControllerTestCase
{
    protected $testXml = '';

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

    public function testCreate()
    {
        /** @var ActivityManager $activityManager */
        $activityManager = $this->getApplicationServiceLocator()->get(ActivityManager::class);

        /** @var Sequence $sequence */
        $sequence = $activityManager->get('sequence');
        static::assertInstanceOf(Sequence::class, $sequence);
    }
}
