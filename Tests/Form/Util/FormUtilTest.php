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
        $parentType = $this->getMock('Symfony\Component\Form\ResolvedFormTypeInterface');
        $parentType->expects($this->any())
            ->method('getInnerType')
            ->will($this->returnValue(new TextType()));

        $formInnerType = $this->getMock('Symfony\Component\Form\FormTypeInterface');

        $formType = $this->getMock('Symfony\Component\Form\ResolvedFormTypeInterface');
        $formType->expects($this->any())
            ->method('getInnerType')
            ->will($this->returnValue($formInnerType));
        $formType->expects($this->any())
            ->method('getParent')
            ->will($this->returnValue($parentType));

        $formConfig = $this->getMock('Symfony\Component\Form\FormBuilderInterface');
        $formConfig->expects($this->any())
            ->method('getType')
            ->will($this->returnValue($formType));

        $form = $this->getMock('Symfony\Component\Form\FormInterface');
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
