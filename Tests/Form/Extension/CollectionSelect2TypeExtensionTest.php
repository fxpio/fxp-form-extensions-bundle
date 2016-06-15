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

use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\BaseChoiceSelect2TypeExtension;
use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\ChoiceSelect2TypeExtension;
use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\CollectionSelect2TypeExtension;
use Sonatra\Bundle\FormExtensionsBundle\Form\Type\CurrencyType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RouterInterface;

/**
 * Tests case for collection of select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class CollectionSelect2TypeExtensionTest extends AbstractSelect2TypeExtensionTest
{
    protected function setUp()
    {
        parent::setUp();

        /* @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->dispatcher;
        /* @var RouterInterface $router */
        $router = $this->router;

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new ChoiceSelect2TypeExtension($dispatcher, $this->requestStack, $router, ChoiceType::class, 10))
            ->addTypeExtension(new ChoiceSelect2TypeExtension($dispatcher, $this->requestStack, $router, CurrencyType::class, 10))
            ->addTypeExtension(new BaseChoiceSelect2TypeExtension(CurrencyType::class))
            ->addTypeExtension(new CollectionSelect2TypeExtension(10))
            ->getFormFactory();

        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function getExtensionTypeName()
    {
        return CollectionType::class;
    }

    protected function mergeOptions(array $options)
    {
        $options = parent::mergeOptions($options);
        $options['entry_type'] = CurrencyType::class;
        $options['select2'] = array_merge_recursive(isset($options['select2']) ? $options['select2'] : array(), array('enabled' => true));

        return $options;
    }

    protected function getSingleData()
    {
        return array('EUR');
    }

    protected function getValidSingleValue()
    {
        return 'EUR';
    }

    protected function getValidAjaxSingleValue()
    {
        return 'EUR';
    }

    protected function getMultipleData()
    {
        return array('EUR', 'USD');
    }

    protected function getValidMultipleValue()
    {
        return array('EUR', 'USD');
    }

    protected function getValidAjaxMultipleValue()
    {
        return implode(',', $this->getValidMultipleValue());
    }

    public function testDefaultOptions()
    {
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions(array()));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidSingleValue(), $selectorView->vars['value']);
    }

    public function testDefaultEnabledOptions()
    {
        // Skip test
    }

    public function testDisabled()
    {
        $options = $this->mergeOptions(array());
        $options['select2']['enabled'] = false;
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $options);

        $this->assertFalse($form->getConfig()->hasAttribute('selector'));
        $config = $form->getConfig();

        $this->assertTrue($config->getOption('compound'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertFalse($select2Opts['enabled']);

        $view = $form->createView();
        $this->assertFalse(array_key_exists('selector', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals((array) $this->getValidSingleValue(), $view->vars['value']);
    }

    public function testSingleWithTags()
    {
        $options = array('select2' => array('tags' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidSingleValue(), $selectorView->vars['value']);
        $this->assertTrue(array_key_exists('tags', $selectorView->vars['select2']));
    }

    public function testSingleAjax()
    {
        $options = array('select2' => array('ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidAjaxSingleValue(), $selectorView->vars['value']);
    }

    public function testSingleAjaxWithTags()
    {
        $options = array('select2' => array('ajax' => true, 'tags' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));

        $this->assertTrue($form->getConfig()->hasAttribute('selector'));
        /* @var FormBuilderInterface $config */
        $config = $form->getConfig()->getAttribute('selector');
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilderInterface', $config);

        $this->assertTrue($form->getConfig()->getOption('compound'));
        $this->assertTrue($form->getConfig()->getOption('allow_add'));
        $this->assertTrue($form->getConfig()->getOption('allow_delete'));

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface', $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals($this->getSingleData(), $selectorView->vars['data']);
        $this->assertEquals((array) $this->getValidAjaxSingleValue(), $selectorView->vars['value']);
    }

    public function testMultiple()
    {
        // Skip test
    }

    public function testMultipleAjax()
    {
        // Skip test
    }

    public function testRequiredAjaxEmptyChoice()
    {
        $options = array('select2' => array('ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals(array(), $selectorView->vars['choices']);
    }

    public function testSinglePlaceHolder()
    {
        // Skip test
    }

    public function testAjaxRoute()
    {
        $options = array('required' => false, 'select2' => array('ajax' => true, 'ajax_route' => 'foobar'));
        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $this->assertEquals('/foobar', $selectorView->vars['select2']['ajax']['url']);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The "Symfony\Component\Form\Extension\Core\Type\TextType" type is not an "choice" with Select2 extension, because: the options "multiple", "select2" do not exist.
     */
    public function testWithoutChoice()
    {
        $options = $this->mergeOptions(array());
        $options['entry_type'] = TextType::class;

        $this->factory->create($this->getExtensionTypeName(), null, $options);
    }

    public function testChoiceLoaderOption()
    {
        // Skip test
    }

    public function testInvalidChoiceLoaderOption()
    {
        // Skip test
    }

    public function testDefaultType()
    {
        $options = array(
            'select2' => array(
                'enabled' => true,
            ),
        );

        $form = $this->factory->create($this->getExtensionTypeName(), null, $options);

        $this->assertSame('Symfony\Component\Form\Extension\Core\Type\ChoiceType', $form->getConfig()->getOption('entry_type'));
    }

    public function testAllowAddTag()
    {
        $options = array('allow_add' => true, 'entry_options' => array('choices' => array('Bar' => 'foo')));
        $data = array('foo', 'Baz');
        $form = $this->factory->create($this->getExtensionTypeName(), $data, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $valid = array(
            new ChoiceView('foo', 'foo', 'Bar'),
            new ChoiceView('Baz', 'Baz', 'Baz'),
        );
        $this->assertEquals($valid, $selectorView->vars['choices']);
        $this->assertSame('true', $selectorView->vars['select2']['tags']);
    }

    public function testDenyAddTag()
    {
        $options = array('allow_add' => false, 'entry_options' => array('choices' => array('Bar' => 'foo')));
        $data = array('foo', 'Baz');
        $form = $this->factory->create($this->getExtensionTypeName(), $data, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertTrue(array_key_exists('selector', $view->vars));
        /* @var FormView $selectorView */
        $selectorView = $view->vars['selector'];
        $this->assertInstanceOf('Symfony\Component\Form\FormView', $selectorView);

        $valid = array(
            new ChoiceView('foo', 'foo', 'Bar'),
        );
        $this->assertEquals($valid, $selectorView->vars['choices']);
        $this->assertFalse(array_key_exists('tags', $selectorView->vars['select2']));
    }
}
