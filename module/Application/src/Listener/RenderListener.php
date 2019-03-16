<?php

namespace Application\Listener;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\Http\PhpEnvironment\Response as HttpResponse;
use Zend\Log\Logger;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ModelInterface;

/**
 * Class ExceptionListener
 * @package Application\Listener
 */
class RenderListener extends AbstractListenerAggregate
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * RenderListener constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int $priority
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_RENDER, [$this, 'render'], PHP_INT_MAX);
    }

    public function render(EventInterface $event)
    {
        if (!$event instanceof MvcEvent) {
            return;
        }

//        // disable next propagation
//        $event->stopPropagation();

        // Check the accept headers for application/json
        $request = $event->getRequest();
        if (!$request instanceof HttpRequest) {
            return;
        }

        // if we have a JsonModel in the result, then do nothing
        $currentModel = $event->getResult();
        if ($currentModel instanceof JsonModel) {
            return;
        }

        // create a new JsonModel - use application/api-problem+json fields.
        /** @var HttpResponse $response */
        $response = $event->getResponse();
        $model = new JsonModel([
            'code' => $response->getStatusCode(),
            'message' => $response->getReasonPhrase(),
        ]);

        // Find out what the error is
        /** @var \Exception $exception */
        $exception  = $currentModel->getVariable('exception');

        if ($currentModel instanceof ModelInterface && $currentModel->reason) {
            switch ($currentModel->reason) {
                case 'error-controller-cannot-dispatch':
                    $model->message = 'The requested controller was unable to dispatch the request.';
                    break;
                case 'error-controller-not-found':
                    $model->message = 'The requested controller could not be mapped to an existing controller class.';
                    break;
                case 'error-controller-invalid':
                    $model->message = 'The requested controller was not dispatchable.';
                    break;
                case 'error-router-no-match':
                    $model->message = 'The requested URL could not be matched by routing.';
                    break;
                default:
                    $model->message = $currentModel->message;
                    break;
            }
        }

        if ($exception instanceof \Exception) {
            $model->code = $exception->getCode();
            $model->message = $exception->getMessage();
            $event->getResponse()->setStatusCode($exception->getCode());
        }
        $model->trace = $exception->getTraceAsString();

        // set our new view model
        $model->setTerminal(true);
        $event->setResult($model);
        $event->setViewModel($model);
    }
}
