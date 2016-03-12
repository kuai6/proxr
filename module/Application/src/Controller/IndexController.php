<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Application\EntityRepository\Device;
use Application\Form\Device\View;
use Doctrine\ORM\EntityManager;
use Zend\Form\FormElementManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     * @return array
     */
    public function indexAction()
    {
        /** @var EntityManager $entityManager */
        $entityManager = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        /** @var Device $deviceRepository */
        $deviceRepository = $entityManager->getRepository(\Application\Entity\Device::class);
        $devices = $deviceRepository->findAll();

        return [
            'devices' => $devices
        ];
    }
}
