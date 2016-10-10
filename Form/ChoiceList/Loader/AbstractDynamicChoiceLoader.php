<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader;

use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\PropertyAccess\PropertyPath;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class AbstractDynamicChoiceLoader implements DynamicChoiceLoaderInterface
{
    /**
     * @var ChoiceListFactoryInterface
     */
    protected $factory;

    /**
     * @var ChoiceListInterface
     */
    protected $choiceList;

    /**
     * @var bool
     */
    protected $allowAdd;

    /**
     * @var null|callable|string|PropertyPath
     */
    protected $label;

    /**
     * Creates a new choice loader.
     *
     * @param ChoiceListFactoryInterface|null $factory The factory for creating
     *                                                 the loaded choice list
     */
    public function __construct($factory = null)
    {
        $this->factory = $factory ?: new PropertyAccessDecorator(new DefaultChoiceListFactory());
        $this->allowAdd = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowAdd($allowAdd)
    {
        $this->allowAdd = (bool) $allowAdd;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowAdd()
    {
        return $this->allowAdd;
    }
}
