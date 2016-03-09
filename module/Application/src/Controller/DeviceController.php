<?php

namespace Application\Controller;


use Application\Form\Device\View;
use Doctrine\ORM\EntityManager;
use Zend\Form\FormElementManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


/**
 * Class DeviceController
 * @package Application\Controller
 */
class DeviceController extends AbstractActionController
{
    public function indexAction()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $entityManager->getRepository(\Application\Entity\Device::class);

        return [
            'devices' => $deviceRepository->findAll()
        ];
    }

    public function viewAction()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var \Application\EntityRepository\Device $deviceRepository */
        $deviceRepository = $entityManager->getRepository(\Application\Entity\Device::class);
        /** @var \Application\Entity\Device $device */
        $device = $deviceRepository->find(1);

        return [
           'device' => $device
        ];
    }
}