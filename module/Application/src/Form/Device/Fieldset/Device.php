<?php

namespace Application\Form\Device\Fieldset;

use Zend\Form\Element\Collection;
use Zend\Form\Fieldset;
use Zend\Hydrator\ClassMethods;
use Zend\Stdlib\InitializableInterface;

/**
 * Class Device
 * @package Application\Form\Device\Fieldset
 */
class Device extends Fieldset implements InitializableInterface
{
    /**
     * This function is automatically called when creating element with factory. It
     * allows to perform various operations (add elements...)
     *
     * @return void
     */
    public function init()
    {
        $this->setHydrator(new ClassMethods(false));

        $this->add([
            'name' => 'banks',
            'type' => Collection::class,
            'options' => [
                'count' => 1,
                'should_create_template' => true,
                'allow_add' => false,
                'target_element' => [
                    'type' => Bank::class,
                ],
                'label' => 'Banks'
            ],
            'attributes' => [
                'type' => 'grid',
            ]
        ]);
    }
}