<?php

namespace Application\Activity\Activity;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Doctrine\Common\Util\Debug;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;

class Log extends AbstractActivity
{
    //just for test
    protected $logFile = __DIR__ .'/../../../../../data/logs/test.log';


    /**
     * @param Context $context
     * @return mixed
     */
    public function execute(Context $context)
    {
        $logContent = Debug::dump($context, 2, true, false);
        if($this->getName() !== null){
            $logContent = Debug::dump($context->get($this->getName()), 2, true, false);
        }

        $logger = new Logger();
        $writer = new Stream($this->getLogFile());
        $logger->addWriter($writer);

        $logger->log(Logger::INFO, $logContent);
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        $this->setName((string)$attributes['name']);
        if ($attributes['logFile']) {
            $this->setLogFile((string) $attributes['logFile']);
        }
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * @param string $logFile
     * @return Log
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
        return $this;
    }
}
