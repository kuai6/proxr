<?php

namespace Application\EntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Activity
 * @package Application\EntityRepository
 */
class Activity extends EntityRepository
{

    public function getActivitiesDBAL($eventName, $deviceId, $bankId)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $query->select('a.*')
            ->from($this->getClassMetadata()->getTableName(), 'a')
            ->where('a.event = :eventName')
            ->andWhere('a.deviceId = :deviceId')
            ->andWhere('a.bankId = :bankId')
            ->setParameter('eventName', $eventName)
            ->setParameter('deviceId', $deviceId)
            ->setParameter('bankId', $bankId);
        return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
}
