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
            $query->set('b.bit'.$binIndex, $bitValue);
        }
        $query->where('b.deviceId = :deviceId')
            ->andWhere('b.name = :bankName')
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
}
