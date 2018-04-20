<?php

namespace Application\Service;

use Application\Entity\Bank;
use Doctrine\ORM\EntityManager;

/**
 * Class BankService
 * @package Application\Service
 */
class BankService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * BankService constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Bank $bank
     * @return Bank
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(Bank $bank)
    {
        $this->entityManager->persist($bank);
        $this->entityManager->flush($bank);
        return $bank;
    }

}