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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\QueryBuilder;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxORMQueryBuilderLoader extends ORMQueryBuilderLoader
{
    /**
     * @var QueryBuilder|\Closure
     */
    private $backupQueryBuilder;

    /**
     * Construct an ORM Query Builder Loader
     *
     * @param QueryBuilder|\Closure $queryBuilder
     * @param ObjectManager         $manager
     * @param string                $class
     *
     * @throws UnexpectedTypeException
     */
    public function __construct($queryBuilder, $manager = null, $class = null)
    {
        $this->backupQueryBuilder = $queryBuilder;

        if ($queryBuilder instanceof QueryBuilder) {
            $queryBuilder = clone $queryBuilder;
        }

        /* @var EntityManager $manager */

        parent::__construct($queryBuilder, $manager, $class);
    }

    /**
     * Gets query builder.
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->getReflectionProperty('queryBuilder')
            ->getValue($this);
    }

    /**
     * Restores the query builder.
     */
    public function reset()
    {
        $this->getReflectionProperty('queryBuilder')
            ->setValue($this, clone $this->backupQueryBuilder);
    }

    /**
     * Gets the reflection parent property.
     *
     * @param string $property The property name
     *
     * @return \ReflectionProperty
     */
    protected function getReflectionProperty($property)
    {
        $ref = new \ReflectionClass($this);
        $parent = $ref->getParentClass();
        $prop = $parent->getProperty($property);
        $prop->setAccessible(true);

        return $prop;
    }
}
