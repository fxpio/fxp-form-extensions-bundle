<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Doctrine\Form\ChoiceList;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\QueryBuilder;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxORMQueryBuilderLoader implements AjaxEntityLoaderInterface
{
    /**
     * Contains the query builder that builds the query for fetching the
     * entities.
     *
     * This property should only be accessed through queryBuilder.
     *
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var QueryBuilder
     */
    private $backupQueryBuilder;

    /**
     * @var int|null
     */
    private $size;

    /**
     * Construct an ORM Query Builder Loader.
     *
     * @param QueryBuilder $queryBuilder The query builder for creating the query builder.
     *
     * @throws UnexpectedTypeException
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->backupQueryBuilder = $queryBuilder;
        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function setSearch($identifier, $search)
    {
        $qb = $this->queryBuilder;
        $alias = current($qb->getRootAliases());
        $qb->andWhere("LOWER({$alias}.{$identifier}) LIKE LOWER(:{$identifier})");
        $qb->setParameter($identifier, "%{$search}%");
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === $this->size) {
            $paginator = new Paginator($this->queryBuilder);
            $this->size = (integer) $paginator->count();
        }

        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaginatedEntities($pageSize, $pageNumber = 1)
    {
        $pageSize = $pageSize < 1 ? 1 : $pageSize;
        $pageNumber = $pageNumber < 1 ? 1 : $pageNumber;
        $paginator = new Paginator($this->queryBuilder);
        $paginator->getQuery()->setFirstResult(($pageNumber - 1) * $pageSize)
            ->setMaxResults($pageSize);

        return $paginator->getIterator()->getArrayCopy();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntities()
    {
        $qb = clone $this->queryBuilder;

        return $qb->getQuery()->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getEntitiesByIds($identifier, array $values)
    {
        $qb = clone $this->backupQueryBuilder;
        $alias = current($qb->getRootAliases());
        $parameter = 'AjaxORMQueryBuilderLoader_getEntitiesByIds_'.$identifier;
        $where = $qb->expr()->in($alias.'.'.$identifier, ':'.$parameter);

        // Guess type
        $entity = current($qb->getRootEntities());
        $metadata = $qb->getEntityManager()->getClassMetadata($entity);

        if (in_array($metadata->getTypeOfField($identifier), array('integer', 'bigint', 'smallint'))) {
            $parameterType = Connection::PARAM_INT_ARRAY;

            // Filter out non-integer values (e.g. ""). If we don't, some
            // databases such as PostgreSQL fail.
            $values = array_values(array_filter($values, function ($v) {
                return (string) $v === (string) (int) $v;
            }));
        } elseif ('guid' === $metadata->getTypeOfField($identifier)) {
            $parameterType = Connection::PARAM_STR_ARRAY;

            // Like above, but we just filter out empty strings.
            $values = array_values(array_filter($values, function ($v) {
                return (string) $v !== '';
            }));
        } else {
            $parameterType = Connection::PARAM_STR_ARRAY;
        }

        return !$values
            ? array()
            : $qb->andWhere($where)
                ->getQuery()
                ->setParameter($parameter, $values, $parameterType)
                ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->queryBuilder = clone $this->backupQueryBuilder;
        $this->size = null;
    }
}
