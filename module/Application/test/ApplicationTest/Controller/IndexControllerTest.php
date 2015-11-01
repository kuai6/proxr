<?php
namespace ApplicationTest\Application\Controller;

use Application\Controller\IndexController;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Zend\View\Model\ViewModel;

/**
 * Class IndexControllerTest
 * @package Application\PhpUnit\Test\Controller
 */
class IndexControllerTest extends AbstractHttpControllerTestCase
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

    public function testIndexAction()
    {
        $indexController = new IndexController();
        static::assertInstanceOf(ViewModel::class, $indexController->indexAction());
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Application');
        $this->assertControllerName('Application\Controller\Index');
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('home');
    }
}
