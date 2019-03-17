<?php

namespace Application\Activity\Activity;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Application\Activity\ContextTrait;
use Application\Activity\Exception\RuntimeException;
use Application\Command\Adapter\Socket;
use Application\Command\Adc;
use Application\Command\ContactClosure;
use Application\Command\Dac;
use Application\Command\Relay;
use Application\Entity\Bank;
use Application\EntityRepository\Bank as BankEntityRepository;
use Doctrine\ORM\EntityManager;
use Server\Event\OutcomeEvent;
use Server\Service\ServerService;
use Zend\EventManager\EventManager;

/**
 * Device activity
 *
 * Set device bit
 * <device device="DeviceVariableFromContest" action="set" bank="0" bit="1" value="0" />
 *
 * Get device bit
 * <device device="DeviceVariableFromContest" action="get" bank="0" bit="1" out="DeviceBitValue" />
 *
 *
 * Class Device
 * @package Application\Activity\Activity
 */
class Device extends AbstractActivity
{
    use ContextTrait;

    const ACTION_GET = 'get';
    const ACTION_SET = 'set';

    /** @var  string */
    protected $deviceVariable;

    /** @var  int */
    protected $bank;

    /** @var  int */
    protected $bit;

    /** @var  int */
    protected $value;

    /** @var string */
    protected $rawValue;

    /** @var  string */
    protected $action;

    /** @var  string */
    protected $out;


    public function execute(Context $context)
    {
        $this->setContext($context);
        /** @var \Application\Entity\Device $device */
        $device = $context->get($this->getDeviceVariable());
        if ($device === null || !$device instanceof \Application\Entity\Device) {
            throw new RuntimeException(sprintf('Entity of class %s not found in context by name %s',
                \Application\Entity\Device::class, $this->getDeviceVariable()));
        }

        /** @var EventManager $eventManager */
        $eventManager = $context->getServiceLocator()->get('Application')->getEventManager();

        /** @var EntityManager $entityManager */
        $entityManager = $context->getServiceLocator()->get('ApplicationEntityManager');
        /** @var BankEntityRepository $bankRepository */
        $bankRepository = $entityManager->getRepository(Bank::class);

        /** @var Bank $bank */
        $bank = $bankRepository->getBankByDeviceAndName($device, $this->getBank());
        if (!$bank) {
            throw new RuntimeException('Bank entity not found');
        }
        $entityManager->refresh($bank);
        $command = $result = false;
        switch (true) {
            case $bank instanceof Bank\Relay:
                $command = new Relay();
                if ($this->getAction() === self::ACTION_SET) {
                    $method = $this->getValue() > 0 ? 'on' : 'off';
                    $setter = 'setBit' . $this->getBit();

                    $val = $method === 'on' ? 1 : 0;
                    $bank->{$setter}($val);

                    $entityManager->persist($bank);
                    $entityManager->flush($bank);

                    $result = $command->$method($bank, $this->getBit());
                    $v = [];
                    for ($i =0; $i < $bank->getAvailableBitsCount(); $i++) {
                        $getter = 'getBit'.$i;
                        $v[] = $bank->$getter();
                    }
                    $outcome = new OutcomeEvent();
                    $outcome->setName('outcome.event.'. ServerService::COMMAND_DATA);
                    $outcome->setParams([
                        'command' => ServerService::COMMAND_DATA,
                        'ip'    => $device->getIp(),
                        'port'  => $device->getPort(),
                        'data' => pack('ccc', 1, $bank->getName(), bindec(implode($v, ''))),
                    ]);
                    $eventManager->trigger($outcome);
                }
                if ($this->getAction() === self::ACTION_GET) {
                    $getter = 'getBit' . $this->getBit();
                    $result = $bank->{$getter}();
                }

                break;
            case $bank instanceof Bank\ContactClosure:
                $command = new ContactClosure();
                if ($this->getAction() === self::ACTION_GET) {
                    $getter = 'getBit' . $this->getBit();
                    $result = $bank->{$getter}();
                }

                break;
            case $bank instanceof Bank\Dac:
                $command = new Dac();
                $value = $this->getValue();
                if($context->has($this->getValue())) {
                    $value = $context->get($this->getValue());
                }

                //$result = $command->set($bank, $this->getBit(), $value);

                $outcome = new OutcomeEvent();
                $outcome->setName('outcome.event.'. ServerService::COMMAND_DATA);
                $outcome->setParams([
                    'command' => ServerService::COMMAND_DATA,
                    'ip'    => $device->getIp(),
                    'port'  => $device->getPort(),
                    'data'  => pack('c*', 1, 0, $value)
                ]);

                $eventManager->trigger($outcome);
                break;
            case $bank instanceof Bank\Adc:
                $command = new Adc();
                if ($this->getAction() === self::ACTION_GET) {
                    $getter = 'getBit' . $this->getBit();
                    $result = $bank->{$getter}();
                }
                break;

        }

        if (!$command) {
            throw new RuntimeException(sprintf('Command could be resolved by %s',
                get_class($bank)));
        }

        if ($this->getOut()) {
            $this->getContext()->set($this->getOut(), $result);
        }
        return $result;
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed|void
     * @throws RuntimeException
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        $device = (string) $attributes['device'];
        if (!$device) {
            throw new RuntimeException('Attribute device must be specified');
        }
        $this->setDeviceVariable($device);

        $action = (string) $attributes['action'];
        if (!$action) {
            throw new RuntimeException('Attribute action must be specified');
        }
        if (!in_array($action, [self::ACTION_GET, self::ACTION_SET])) {
            throw new RuntimeException(sprintf('Attribute action may be %s or %s',
                self::ACTION_GET, self::ACTION_SET));
        }
        $this->setAction($action);

        $bank = (string) $attributes['bank'];
        if ($bank === '') {
            throw new RuntimeException('Attribute bank must be specified');
        }
        $this->setBank((int) $bank);

        $bit = (string) $attributes['bit'];
        if ($bit === '') {
            throw new RuntimeException('Attribute action must be specified');
        }
        $this->setBit((int) $bit);

        $value = (string)$attributes['value'];
        if ($value === '' && $this->getAction() === self::ACTION_SET) {
            throw new RuntimeException('Attribute value must be specified if you use a set action');
        }
        $this->setValue($value);
        $this->setRawValue($attributes['rawValue']);

        if ($attributes['out']) {
            $this->setOut((string) $attributes['out']);
        }
    }

    /**
     * @return mixed
     */
    public function getDeviceVariable()
    {
        return $this->deviceVariable;
    }

    /**
     * @param mixed $deviceVariable
     * @return Device
     */
    public function setDeviceVariable($deviceVariable)
    {
        $this->deviceVariable = $deviceVariable;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBit()
    {
        return $this->bit;
    }

    /**
     * @param mixed $bit
     * @return Device
     */
    public function setBit($bit)
    {
        $this->bit = $bit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Device
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getRawValue()
    {
        return $this->rawValue;
    }

    /**
     * @param string $rawValue
     * @return $this
     */
    public function setRawValue($rawValue)
    {
        $this->rawValue = $rawValue;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     * @return Device
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getOut()
    {
        return $this->out;
    }

    /**
     * @param string $out
     * @return Device
     */
    public function setOut($out)
    {
        $this->out = $out;
        return $this;
    }

    /**
     * @return int
     */
    public function getBank()
    {
        return $this->bank;
    }

    /**
     * @param int $bank
     * @return Device
     */
    public function setBank($bank)
    {
        $this->bank = $bank;
        return $this;
    }
}
