<?php

namespace Application\Activity;

/**
 * Class ContextTrait
 * @package Application\Activity
 */
trait ContextTrait
{
    /** @var  Context */
    private $context;

    /**
     * @param $context
     * @return $this
     */
    protected function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return Context
     */
    protected function getContext()
    {
        return $this->context;
    }
}
