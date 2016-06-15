<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Doctrine\Form\Converter;

use Sonatra\Bundle\FormExtensionsBundle\Doctrine\Form\Converter\NewTagConverterInterface;
use Sonatra\Bundle\FormExtensionsBundle\Doctrine\Form\EventListener\NewTagConverterListener;
use Symfony\Component\Form\FormEvent;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class NewTagConverterListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnSubmit()
    {
        /* @var NewTagConverterInterface|\PHPUnit_Framework_MockObject_MockObject $converter */
        $converter = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Doctrine\Form\Converter\NewTagConverterInterface')->getMock();
        $converter->expects($this->any())
            ->method('convert')
            ->will($this->returnCallback(function ($value) {
                return 'CONVERTED_'.$value;
            }));

        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $event = new FormEvent($form, 'FOO_BAR');
        $listener = new NewTagConverterListener($converter);

        $listener->onSubmit($event);
        $this->assertSame('CONVERTED_FOO_BAR', $event->getData());
    }
}
