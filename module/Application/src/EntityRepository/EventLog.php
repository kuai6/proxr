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
            'dateTime' => 'sysdate(6)'
        ];
        foreach ($bits as $binIndex => $bitValue) {
            $values['bit'.$binIndex] = sprintf("'%s'", $bitValue);
        }

        $query->insert($this->getClassMetadata()->getTableName())
            ->values($values)->execute();
        $id = $query->getConnection()->lastInsertId();
        return $this->find($id);
    }

    public function getLast($device, $dateTime)
    {
        if($dateTime instanceof \DateTime) {
            $dateTime = $dateTime->format('Y-m-d H:i:s.u');
        }

        /** @var QueryBuilder $qb */
        $qb = $this->getEntityManager()->getConnection()->createQueryBuilder();

        $query = $qb->select('*')
                    ->from($this->getClassMetadata()->getTableName(), 'l')
                    ->where('l.deviceId = :device')
                    ->andWhere('l.dateTime >= :dateTime')
                    ->orderBy('dateTime', 'DESC')
                    ->setParameters([
                        'device' => $device,
                        'dateTime' => $dateTime
                    ]);
        $result = $query->execute()->fetchAll();

        return $result;
    }

}
