<?php

namespace Shopsys\FrameworkBundle\Component\EntityExtension;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\Decorator\EntityManagerDecorator as BaseEntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;

class EntityManagerDecorator extends BaseEntityManagerDecorator
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @var \Doctrine\ORM\Repository\RepositoryFactory
     */
    protected $repositoryFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Doctrine\ORM\Configuration $config
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(
        EntityManagerInterface $em,
        Configuration $config,
        EntityNameResolver $entityNameResolver
    ) {
        parent::__construct($em);

        $this->entityNameResolver = $entityNameResolver;
        $this->repositoryFactory = $config->getRepositoryFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder()
    {
        return new QueryBuilder($this, $this->entityNameResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function createQuery($dql = '')
    {
        $resolvedDql = $this->entityNameResolver->resolveIn($dql);

        return parent::createQuery($resolvedDql);
    }

    /**
     * {@inheritdoc}
     */
    public function getReference($entityName, $id)
    {
        $resolvedEntityName = $this->entityNameResolver->resolve($entityName);

        return parent::getReference($resolvedEntityName, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getPartialReference($entityName, $identifier)
    {
        $resolvedEntityName = $this->entityNameResolver->resolve($entityName);

        return parent::getPartialReference($resolvedEntityName, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function find($entityName, $id, $lockMode = null, $lockVersion = null)
    {
        $resolvedEntityName = $this->entityNameResolver->resolve($entityName);

        return parent::find($resolvedEntityName, $id, $lockMode, $lockVersion);
    }

    /**
     * {@inheritdoc}
     */
    public function clear($objectName = null)
    {
        if ($objectName !== null) {
            $objectName = $this->entityNameResolver->resolve($objectName);
        }

        parent::clear($objectName);
    }

    /**
     * @param string $className
     */
    public function refreshLoadedEntitiesByClassName(string $className): void
    {
        $className = $this->entityNameResolver->resolve($className);

        $identityMap = $this->getUnitOfWork()->getIdentityMap();

        if (!array_key_exists($className, $identityMap)) {
            return;
        }

        foreach ($identityMap[$className] as $entity) {
            $this->refresh($entity);
        }
    }

    /**
     * @param string $className
     * @return \Doctrine\Persistence\ObjectRepository
     */
    public function getRepository($className)
    {
        $resolvedClassName = $this->entityNameResolver->resolve($className);

        return $this->repositoryFactory->getRepository($this, $resolvedClassName);
    }
}
