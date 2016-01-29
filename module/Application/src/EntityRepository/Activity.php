<?php

namespace Application\EntityRepository;

use Doctrine\ORM\EntityRepository;

/**
 * Class Activity
 * @package Application\EntityRepository
 */
class Activity extends EntityRepository
{

    public function getActivitiesDBAL($eventName, $deviceId, $bankName)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $query->select('a.*')
            ->from($this->getClassMetadata()->getTableName(), 'a')
            ->innerJoin('a', $this->getEntityManager()->getClassMetadata(\Application\Entity\Bank::class)->getTableName(), 'b', 'b.id = a.bankId')
            ->where('a.event = :eventName')
            ->andWhere('a.deviceId = :deviceId')
            ->andWhere('b.name = :bankName')
            ->setParameter('eventName', $eventName)
            ->setParameter('deviceId', $deviceId)
            ->setParameter('bankName', $bankName);
        return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
}
