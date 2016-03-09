<?php

namespace Application\Form\Device\Fieldset;

use Zend\Form\Element\Checkbox;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Text;
use Zend\Form\Fieldset;
use Zend\Form\FormInterface;
use Zend\Hydrator\ClassMethods;
use Zend\Stdlib\InitializableInterface;

/**
 * Class Bank
 * @package Application\Form\Device\Fieldset
 */
class Bank extends Fieldset implements InitializableInterface
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
        $this->setObject(new \Application\Entity\Bank());

        $this->add([
            'type' => Hidden::class,
            'name' => 'id',
        ]);

        $this->add([
            'type' => Text::class,
            'name' => 'name',
        ]);

        for ($i = 0; $i <= 7; $i++) {
            $this->add([
                'type' =>  Checkbox::class,
                'name' => sprintf('bit%d', $i)
            ]);
        }
    }

    /**
     * Ensures state is ready for use. Here, we append the name of the fieldsets to every elements in order to avoid
     * name clashes if the same fieldset is used multiple times
     *
     * @param  FormInterface $form
     * @return mixed|void
     */
    public function prepareElement(FormInterface $form)
    {
//        if ($this->getObject() !== null) {
//            switch (true) {
//                case $this->getObject() instanceof \Application\Entity\Bank\ContactClosure:
//                    $class = Checkbox::class;
//                    break;
//                case $this->getObject() instanceof \Application\Entity\Bank\Relay:
//                    $class = Checkbox::class;
//                    break;
//                default:
//                    $class = Checkbox::class;
//                    break;
//            }
//
//            for ($i = 0; $i <= 7; $i++) {
//                $this->add([
//                    'type' => $class,
//                    'name' => sprintf('bit%d', $i)
//                ]);
//            }
//        }

        parent::prepareElement($form);
    }
}