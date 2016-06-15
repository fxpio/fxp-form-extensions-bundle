<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Form\DataTransformer;

use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface;
use Sonatra\Bundle\FormExtensionsBundle\Form\DataTransformer\Select2ChoiceToValueTransformer;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Tests case for Select2 choice to value data transformer.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class Select2ChoiceToValueTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DynamicChoiceLoaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $choiceLoader;

    /**
     * @var DataTransformerInterface
     */
    protected $transformer;

    protected function setUp()
    {
        $this->choiceLoader = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface')->getMock();
        $this->choiceLoader
            ->expects($this->any())
            ->method('loadValuesForChoices')
            ->will($this->returnCallback(function ($value) {
                foreach ($value as $i => $val) {
                    $value[$i] = strtoupper($val);
                }

                return $value;
            }));

        $this->transformer = new Select2ChoiceToValueTransformer($this->choiceLoader);
    }

    public function testTransformWithStringValue()
    {
        $this->assertSame('TEST', $this->transformer->transform('test'));
    }

    public function testTransformWithArrayValue()
    {
        $this->assertSame('TEST', $this->transformer->transform(array('test')));
    }

    public function testReverseTransformWithNullValue()
    {
        $this->choiceLoader
            ->expects($this->any())
            ->method('loadChoicesForValues')
            ->will($this->returnCallback(function () {
                return array();
            }));

        $this->assertNull($this->transformer->reverseTransform(null));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage Expected a scalar
     */
    public function testReverseTransformWithNoScalarValue()
    {
        $this->transformer->reverseTransform(array());
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     * @expectedExceptionMessage The choice "TEST" does not exist or is not unique
     */
    public function testReverseTransformWithNotUniqueChoice()
    {
        $this->choiceLoader
            ->expects($this->any())
            ->method('loadChoicesForValues')
            ->will($this->returnCallback(function () {
                return array(
                    'test1',
                    'test2',
                );
            }));

        $this->transformer->reverseTransform('TEST');
    }

    public function testReverseTransformWithNotUniqueChoiceButEmptyValue()
    {
        $this->choiceLoader
            ->expects($this->any())
            ->method('loadChoicesForValues')
            ->will($this->returnCallback(function () {
                return array(
                    'test1',
                    'test2',
                );
            }));

        $this->assertNull($this->transformer->reverseTransform(null));
    }

    public function testReverseTransformWithStringValue()
    {
        $this->choiceLoader
            ->expects($this->any())
            ->method('loadChoicesForValues')
            ->will($this->returnCallback(function () {
                return array(
                    'test',
                );
            }));

        $this->assertSame('test', $this->transformer->reverseTransform('TEST'));
    }
}
