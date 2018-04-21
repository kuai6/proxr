<?php

namespace Application\EntityRepository;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;

/**
 * Class EventLog
 * @package Application\EntityRepository
 */
class EventLog extends EntityRepository
{
    /**
     * @param $deviceId
     * @param $bankId
     * @param $bits
     * @return string
     */
    public function saveLog($deviceId, $bankId, $bits)
    {
        /** @var \Application\EntityRepository\Bank $bankRepository */
        $bankRepository = $this->getEntityManager()->getRepository(\Application\Entity\Bank::class);
        /** @var array $bank */
        $bank = $bankRepository->getBankByDeviceAndNameDBAL($deviceId, $bankId);
        /** @var QueryBuilder $query */
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $values = [
            'type' =>  "'eventlog'",
            'bankId' => $bank['id'],
            'deviceId' => $deviceId,
            'dateTime' => 'current_timestamp'
        ];
        foreach ($bits as $binIndex => $bitValue) {
            $values['bit'.$binIndex] = sprintf("'%s'", $bitValue);
        }

        $query->insert($this->getClassMetadata()->getTableName())
            ->values($values)->execute();
        $id = $query->getConnection()->lastInsertId();
        return $this->find($id);
    }
}