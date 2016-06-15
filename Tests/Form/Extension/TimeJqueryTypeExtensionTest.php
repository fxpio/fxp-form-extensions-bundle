<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Form\Extension;

use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\DateTimeJqueryTypeExtension;
use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\TimeJqueryTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests case for time jquery form extension type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class TimeJqueryTypeExtensionTest extends TypeTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(
                new DateTimeJqueryTypeExtension()
            )
            ->addTypeExtension(
                new TimeJqueryTypeExtension()
            )
            ->getFormFactory();

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    public function testDefaultOption()
    {
        $form = $this->factory->create(TimeType::class, null, array('locale' => 'en'));
        $config = $form->getConfig();

        $this->assertEquals('single_text', $config->getOption('widget'));
        $this->assertFalse($config->getOption('date_picker'));
        $this->assertTrue($config->getOption('time_picker'));
        $this->assertEquals('en', $config->getOption('locale'));
        $this->assertEquals('h:mm a', $config->getOption('format'));
    }

    public function testFormatFr()
    {
        $form = $this->factory->create(TimeType::class, null, array('locale' => 'fr_FR'));

        $this->assertEquals('HH:mm', $form->getConfig()->getOption('format'));
    }

    public function testDefaultAttributes()
    {
        $form = $this->factory->create(TimeType::class, null, array('locale' => 'en'));
        $view = $form->createView();
        $validAttr = array(
            'data-locale' => 'en',
            'data-date-picker' => 'false',
            'data-time-picker' => 'true',
            'data-time-picker-first' => 'false',
            'data-open-focus' => 'true',
            'data-format' => 'h:mm A',
            'data-with-minutes' => 'true',
            'data-with-seconds' => 'false',
            'data-datetime-picker' => 'true',
            'data-button-id' => 'time_datetime_btn',
        );

        $this->assertEquals($validAttr, $view->vars['attr']);
    }
}
