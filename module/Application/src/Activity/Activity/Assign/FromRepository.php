<?php

namespace Application\Activity\Activity\Assign;

use Application\Activity\AbstractActivity;
use Application\Activity\Context;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * Assign context variable from repository
 *
 * <assignFromRepository name="outVariable" [inObjectId="idVariable"] [mapper="Full_Name_Mapper_Alias"]/>
 *
 * Class FromRepository
 * @package Application\Activity\Activity
 */
class FromRepository extends AbstractActivity
{
    /** @var  string */
    protected $repositoryName;

    /** @var  string */
    protected $objectIdVariable;

    /** @var  string */
    protected $action;

    /**
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $objectIdVariable
     * @return $this
     */
    public function setObjectIdVariable($objectIdVariable)
    {
        $this->objectIdVariable = $objectIdVariable;
        return $this;
    }

    /**
     * @return string
     */
    public function getObjectIdVariable()
    {
        return $this->objectIdVariable;
    }

    /**
     * @param string $repositoryName
     * @return $this
     */
    public function setRepositoryName($repositoryName)
    {
        $this->repositoryName = $repositoryName;
        return $this;
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    /**
     * @param Context $context
     * @return mixed
     */
    public function execute(Context $context)
    {
        $repositoryName = $this->getRepositoryName();
        /** @var EntityManager $entityManager */
        $entityManager = $context->getServiceLocator()->get('ApplicationEntityManager');

        /** @var EntityRepository $repository */
        $repository = $entityManager->getRepository($repositoryName);

        $action = $this->getAction();
        switch ($action) {
            case 'create':
                $entityClassName = $repository->getClassName();
                $entity = new $entityClassName(); ;
                break;

            default:
                $objectId = $context->get($this->getObjectIdVariable());
                $entity = $repository->findOneBy(['id' => $objectId]);
        }
        $context->set($this->getName(), $entity);
    }

    /**
     * @param \SimpleXMLElement $metadata
     * @return mixed
     */
    public function fromMetadata($metadata)
    {
        $attributes = $metadata->attributes();
        $this->setName((string)$attributes['name']);
        if (isset($attributes['repository'])) {
            $this->setRepositoryName((string)$attributes['repository']);
        }

        if (isset($attributes['id'])) {
            $this->setObjectIdVariable((string)$attributes['id']);
        }

        if (isset($attributes['action'])) {
            $this->setAction((string)$attributes['action']);
        }
    }
} 