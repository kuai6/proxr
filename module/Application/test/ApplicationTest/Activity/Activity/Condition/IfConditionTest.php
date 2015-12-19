<?php

namespace ApplicationTest\Activity\Condition;

use Application\Activity\Activity\Condition\IfCondition;
use Application\Activity\ActivityManager;
use Application\Activity\Context;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class IfConditionTest
 * @package ApplicationTest\Activity\Condition
 */
class IfConditionTest extends AbstractHttpControllerTestCase
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

    public function testIfCondition()
    {
        /** @var ActivityManager $activityManager */
        $activityManager = $this->getApplicationServiceLocator()->get(ActivityManager::class);

        $testCase = [
            '<if variable="NullContextVariable" isNull="true" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
            '<if variable="NotNullContextVariable" isNull="false" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
            '<if variable="3ContextVariable" operand="eq" value="3" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
            '<if variable="3ContextVariable" operand="gt" value="2" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
            '<if variable="3ContextVariable" operand="gte" value="3" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
            '<if variable="3ContextVariable" operand="lt" value="5" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
            '<if variable="3ContextVariable" operand="lte" value="3" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
            '<if variable="3ContextVariable" operand="neq" value="5" ><assign name="resultContextVariable" value="true" /></if>' => 'true',
        ];

        foreach ($testCase as $metadata => $expectedResult) {
            $context = new Context();
            $context->set('NullContextVariable', null);
            $context->set('NotNullContextVariable', 'abc');
            $context->set('3ContextVariable', 3);

            /** @var IfCondition $ifCondition */
            $ifCondition = $activityManager->get('if');
            static::assertInstanceOf(IfCondition::class, $ifCondition);
            $ifCondition->fromMetadata(new \SimpleXMLElement($metadata));
            $ifCondition->execute($context);
            $result = $context->get('resultContextVariable');
            static::assertEquals($result, $expectedResult);
            unset($context);
        }
    }
}