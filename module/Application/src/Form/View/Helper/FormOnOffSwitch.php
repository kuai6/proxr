<?php

namespace Application\Form\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormInput;

/**
 * Class FormOnOffSwitch
 * @package Application\Form\View\Helper
 */
class FormOnOffSwitch extends FormInput
{
    public function render(ElementInterface $element)
    {

        /*
         * <div class="switch">
                                <div class="onoffswitch">
                                    <input type="checkbox" checked="" class="onoffswitch-checkbox" id="example1">
                                    <label class="onoffswitch-label" for="example1">
                                        <span class="onoffswitch-inner"></span>
                                        <span class="onoffswitch-switch"></span>
                                    </label>
                                </div>
                            </div>
         */

        return parent::render($element);
    }
}