<?php

namespace ApplicationTest\Activity\Activity;

use Application\Activity\Activity\Assign;
use Application\Activity\ActivityManager;
use Application\Activity\Context;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AssignTest
 * @package ApplicationTest\Activity\Activity
 */
class AssignTest extends AbstractHttpControllerTestCase
{
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

        $testCase = [
            '<assign name="contextVariable" value="testValue" />' => 'testValue',
            '<assign name="contextVariable" />' => null,
        ];

        foreach($testCase as $metadata => $expectedValue) {
            /** @var Assign $assign */
            $assign = $activityManager->get('assign');
            static::assertInstanceOf(Assign::class, $assign);
            $assign->fromMetadata(new \SimpleXMLElement($metadata));
            $context = new Context();
            $assign->execute($context);
            static::assertTrue($context->has('contextVariable'));
            static::assertEquals($expectedValue, $context->get('contextVariable'));
        }
    }
}
