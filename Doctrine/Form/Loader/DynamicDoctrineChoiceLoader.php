<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Doctrine\Form\Loader;

use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AbstractDynamicChoiceLoader;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class DynamicDoctrineChoiceLoader extends AbstractDynamicChoiceLoader
{
    /**
     * @var EntityLoaderInterface
     */
    protected $objectLoader;

    /**
     * @var IdReader
     */
    protected $idReader;

    /**
     * Creates a new choice loader.
     *
     * @param EntityLoaderInterface             $objectLoader The objects loader
     * @param IdReader                          $idReader     The reader for the object
     *                                                        IDs
     * @param null|callable|string|PropertyPath $label        The callable or path generating the choice labels
     * @param ChoiceListFactoryInterface|null   $factory      The factory for creating
     *                                                        the loaded choice list
     */
    public function __construct(EntityLoaderInterface $objectLoader, IdReader $idReader, $label, $factory = null)
    {
        parent::__construct($factory);

        $this->objectLoader = $objectLoader;
        $this->idReader = $idReader;
        $this->label = $label;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return count($this->objectLoader->getEntities());
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceListForView(array $values, $value = null)
    {
        $choiceList = $this->factory->createListFromChoices($this->loadEntities(), $value);

        return $choiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoiceList($value = null)
    {
        if ($this->choiceList) {
            return $this->choiceList;
        }

        $this->choiceList = $this->factory->createListFromChoices($this->loadEntities(), $value);

        return $this->choiceList;
    }

    /**
     * {@inheritdoc}
     */
    public function loadChoicesForValues(array $values, $value = null)
    {
        // Performance optimization
        if (empty($values)) {
            return array();
        }

        $unorderedObjects = $this->objectLoader->getEntitiesByIds($this->idReader->getIdField(), $values);
        $objectsById = array();
        $objects = array();

        foreach ($unorderedObjects as $object) {
            $objectsById[$this->idReader->getIdValue($object)] = $object;
        }

        foreach ($values as $i => $id) {
            if (isset($objectsById[$id])) {
                $objects[$i] = $objectsById[$id];
            } elseif ($this->isAllowAdd()) {
                $objects[$i] = $id;
            }
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function loadValuesForChoices(array $choices, $value = null)
    {
        // Performance optimization
        if (empty($choices)) {
            return array();
        }

        $values = array();

        foreach ($choices as $i => $object) {
            if (is_object($object)) {
                try {
                    $values[$i] = (string) $this->idReader->getIdValue($object);
                } catch (RuntimeException $e) {
                    if (!$this->isAllowAdd()) {
                        throw $e;
                    }
                }
            } elseif ($this->isAllowAdd()) {
                $values[$i] = $object;
            }
        }

        return $values;
    }

    /**
     * Load the entities.
     *
     * @return object[]
     */
    protected function loadEntities()
    {
        return $this->objectLoader->getEntities();
    }
}
