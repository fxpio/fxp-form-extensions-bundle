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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Tests case for datetime jquery form extension type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class DateTimeJqueryTypeExtensionTest extends TypeTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(
                new DateTimeJqueryTypeExtension()
            )
            ->getFormFactory();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    public function testDefaultOption()
    {
        $form = $this->factory->create(DateTimeType::class, null, array('locale' => 'en_EN'));
        $config = $form->getConfig();

        $this->assertEquals('single_text', $config->getOption('widget'));
        $this->assertTrue($config->getOption('date_picker'));
        $this->assertTrue($config->getOption('time_picker'));
        $this->assertEquals('en_EN', $config->getOption('locale'));
        $this->assertEquals($this->getValidDateTime(), $config->getOption('format'));
    }

    public function testFormatFr()
    {
        $form = $this->factory->create(DateTimeType::class, null, array('locale' => 'fr_FR'));

        $this->assertEquals('dd/MM/y HH:mm', $form->getConfig()->getOption('format'));
    }

    public function testDefaultAttributes()
    {
        $form = $this->factory->create(DateTimeType::class, null, array('locale' => 'en_EN'));
        $view = $form->createView();
        $validAttr = array(
            'data-locale' => 'en_EN',
            'data-date-picker' => 'true',
            'data-time-picker' => 'true',
            'data-time-picker-first' => 'false',
            'data-open-focus' => 'true',
            'data-format' => $this->getValidDateTimeAttribute(),
            'data-with-minutes' => 'true',
            'data-with-seconds' => 'false',
            'data-datetime-picker' => 'true',
            'data-button-id' => 'datetime_datetime_btn',
        );

        $this->assertEquals($validAttr, $view->vars['attr']);
    }

    /**
     * @return string
     */
    protected function getValidDateTime()
    {
        if (defined('INTL_ICU_VERSION')
                && version_compare(INTL_ICU_VERSION, '51.2', '>=')) {
            return 'M/d/yy, h:mm a';
        }

        return 'M/d/yy h:mm a';
    }

    /**
     * @return string
     */
    protected function getValidDateTimeAttribute()
    {
        if (defined('INTL_ICU_VERSION')
                && version_compare(INTL_ICU_VERSION, '51.2', '>=')) {
            return 'M/D/YYYY, h:mm A';
        }

        return 'M/D/YYYY h:mm A';
    }
}
