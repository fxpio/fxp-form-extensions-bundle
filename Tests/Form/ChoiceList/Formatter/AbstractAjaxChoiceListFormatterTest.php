<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Form\ChoiceList\Formatter;

use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface;
use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;

/**
 * Base of tests case for choice list formatter.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class AbstractAjaxChoiceListFormatterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyAccessDecorator
     */
    protected $choiceListFactory;

    /**
     * @var AjaxChoiceLoaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $choiceLoader;

    /**
     * @var AjaxChoiceListFormatterInterface
     */
    protected $formatter;

    protected function setUp()
    {
        parent::setUp();

        $this->choiceListFactory = new PropertyAccessDecorator(new DefaultChoiceListFactory());
        $this->choiceLoader = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();
        $this->formatter = $this->getFormatter();

        $this->choiceLoader->expects($this->any())
            ->method('getPageNumber')
            ->will($this->returnValue(1));
        $this->choiceLoader->expects($this->any())
            ->method('getPageSize')
            ->will($this->returnValue(10));
        $this->choiceLoader->expects($this->any())
            ->method('getSearch')
            ->will($this->returnValue(null));
        $this->choiceLoader->expects($this->any())
            ->method('getLabel')
            ->will($this->returnValue($this->getLabelField()));
    }

    protected function tearDown()
    {
        $this->choiceListFactory = null;
        $this->choiceLoader = null;
        $this->formatter = null;

        parent::tearDown();
    }

    protected function init(ChoiceListInterface $choiceList)
    {
        $this->choiceLoader->expects($this->any())
            ->method('getSize')
            ->will($this->returnValue(count($choiceList->getChoices())));

        $this->choiceLoader->expects($this->any())
            ->method('loadPaginatedChoiceList')
            ->will($this->returnValue($choiceList));
    }

    /**
     * @return string
     */
    protected function getLabelField()
    {
        return 'label';
    }

    /**
     * @return ChoiceListInterface
     */
    protected function getMockChoices()
    {
        $foo = new \StdClass();
        $foo->label = 'Bar';
        $bar = new \StdClass();
        $bar->label = 'Foo';
        $baz = new \StdClass();
        $baz->label = 'Baz';

        return new ArrayChoiceList(array(
            'foo' => $foo,
            'bar' => $bar,
            'baz' => $baz,
        ));
    }

    /**
     * @return ChoiceListInterface
     */
    protected function getMockChoiceGroups()
    {
        $foo = new \StdClass();
        $foo->label = 'Bar';
        $bar = new \StdClass();
        $bar->label = 'Foo';
        $baz = new \StdClass();
        $baz->label = 'Baz';

        return new ArrayChoiceList(array(
            'Group 1' => array(
                'foo' => $foo,
                'bar' => $bar,
            ),
            'Group 2' => array(
                'baz' => $baz,
            ),
        ));
    }

    /**
     * @return AjaxChoiceListFormatterInterface
     */
    abstract protected function getFormatter();

    /**
     * @return array
     */
    abstract protected function getValidResponseData();

    /**
     * @return array
     */
    abstract protected function getValidGroupResponseData();

    public function testFormatResponseData()
    {
        $this->init($this->getMockChoices());
        $res = $this->formatter->formatResponseData($this->choiceLoader);

        $this->assertEquals($this->getValidResponseData(), $res);
    }

    public function testFormatGroupResponseData()
    {
        $this->init($this->getMockChoiceGroups());
        $res = $this->formatter->formatResponseData($this->choiceLoader);

        $this->assertEquals($this->getValidGroupResponseData(), $res);
    }
}
