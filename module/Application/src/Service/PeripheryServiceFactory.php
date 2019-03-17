<?php

namespace Application\Service;

use Application\Entity\Periphery\PeripheryType;
use Application\EntityRepository\Periphery;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\AbstractPluginManager;

class PeripheryServiceFactory implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var DeviceService $deviceService */
        $deviceService = $serviceLocator->get(DeviceService::class);
        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->get('ApplicationEntityManager');
        /** @var \Application\EntityRepository\PeripheryType $typesRepository */
        $typesRepository = $entityManager->getRepository(PeripheryType::class);
        /** @var \Application\EntityRepository\Periphery $unitsRepository */
        $unitsRepository = $entityManager->getRepository(Periphery::class);

        return new PeripheryService($deviceService, $entityManager, $typesRepository, $unitsRepository);
    }
}
