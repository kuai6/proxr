<?php

namespace Application\Activity;

/**
 * Interface ActivityManagerAwareInterface
 * @package Application\Activity
 */
interface ActivityManagerAwareInterface
{
    /**
     * @param ActivityManager $manager
     * @return void
     */
    public function setActivityManager(ActivityManager $manager);
}
