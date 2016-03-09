<?php

namespace Application\Form\Device;

use Application\Form\Device\Fieldset\Device;
use Zend\Form\Form;
use Zend\Stdlib\InitializableInterface;

/**
 * Class View
 * @package Application\Form\Device
 */
class View extends Form implements InitializableInterface
{
    /**
     * This function is automatically called when creating element with factory. It
     * allows to perform various operations (add elements...)
     *
     * @return void
     */
    public function init()
    {
        $this->add([
            'name' => 'device',
            'type' => Device::class,
            'options' => ['use_as_base_fieldset' => true]
        ]);
        //$this->setBaseFieldset($this->get('device'));
    }
}