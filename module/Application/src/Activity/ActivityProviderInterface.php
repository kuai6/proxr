<?php

namespace Application\Activity;

/**
 * Interface ActivitiesProviderInterface
 * @package Application\Activity
 */
interface ActivitiesProviderInterface
{
    /**
     * @return array
     */
    public function getActivitiesConfig();
}
