<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Form\Util;

use Sonatra\Bundle\FormExtensionsBundle\Form\Util\FormUtil;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;

/**
 * Tests case for form util.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class FormUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testIsFormType()
    {
        $parentType = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeInterface')->getMock();
        $parentType->expects($this->any())
            ->method('getInnerType')
            ->will($this->returnValue(new TextType()));

        $formInnerType = $this->getMockBuilder('Symfony\Component\Form\FormTypeInterface')->getMock();

        $formType = $this->getMockBuilder('Symfony\Component\Form\ResolvedFormTypeInterface')->getMock();
        $formType->expects($this->any())
            ->method('getInnerType')
            ->will($this->returnValue($formInnerType));
        $formType->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parentType));

        $formConfig = $this->getMockBuilder('Symfony\Component\Form\FormBuilderInterface')->getMock();
        $formConfig->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($formType));

        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $form->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($formConfig));

        /* @var FormInterface $form */
        $this->assertTrue(FormUtil::isFormType($form, TextType::class));
        $this->assertTrue(FormUtil::isFormType($form, get_class($formInnerType)));
        $this->assertTrue(FormUtil::isFormType($form, array(TextType::class, get_class($formInnerType))));
        $this->assertTrue(FormUtil::isFormType($form, array(TextType::class, 'Baz')));
        $this->assertFalse(FormUtil::isFormType($form, 'Baz'));
        $this->assertFalse(FormUtil::isFormType($form, array('Baz', 'Boo!')));
    }
}
