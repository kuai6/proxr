<?php

namespace Application\EntityRepository;

use Application\Entity\Device;
use Doctrine\ORM\EntityRepository;

/**
 * Class Bank
 * @package Application\EntityRepository
 */
class Bank extends EntityRepository
{
    /**
     * Обновляет значения битов в банке
     *
     * @param int $deviceId
     * @param int $bankName
     * @param array $bits
     * @return bool
     */
    public function saveBitsDBAL($deviceId, $bankName, $bits)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();
        $query->update($this->getClassMetadata()->getTableName(), 'b');

        foreach ($bits as $binIndex => $bitValue) {
            $query->set('bit'.$binIndex, $bitValue);
        }
        $query->where('deviceId = :deviceId')
            ->andWhere('name = :bankName')
            ->setParameter('deviceId', $deviceId)
            ->setParameter('bankName', $bankName);
        $query->execute();

        return true;
    }

    /**
     * @param Device $device
     * @param $bankName
     * @return null|\Application\Entity\Bank
     */
    public function getBankByDeviceAndName(Device $device, $bankName)
    {
        return $this->findOneBy([
            'device' => $device,
            'name' => $bankName
        ]);
    }

    /**
     * @param int $deviceId
     * @param string $bankName
     * @return array
     */
    public function getBankByDeviceAndNameDBAL($deviceId, $bankName)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();

        $query->select('*')
            ->from($this->getClassMetadata()->getTableName(), 'b')
            ->where('b.deviceId = :deviceId')
            ->andWhere('b.name = :name')
            ->setParameter('deviceId', $deviceId)
            ->setParameter('name', $bankName);
        return $query->execute()->fetch(\PDO::FETCH_ASSOC);
    }

}
