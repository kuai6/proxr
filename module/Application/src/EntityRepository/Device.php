<?php

namespace Application\EntityRepository;

use Application\Entity\Status;
use Doctrine\ORM\EntityRepository;

/**
 * Class Device
 * @package Application\EntityRepository
 */
class Device extends EntityRepository
{
    public function getDevicesDBAL()
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $query->select('d.*')
            ->from($this->getClassMetadata()->getTableName(), 'd')
            ->innerJoin('d', $this->getEntityManager()->getClassMetadata(Status\Device::class)->getTableName(), 'ds', 'd.statusId = ds.id')
            ->where('ds.code = :code')
            ->setParameter('code', Status\Device::STATUS_ACTIVE);
        return $query->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }
}
