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

use Sonatra\Bundle\FormExtensionsBundle\Form\Extension\ChoiceSelect2TypeExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Tests case for abstract select2 form extension type.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class AbstractSelect2TypeExtensionTest extends TypeTestCase
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var RouterInterface||\PHPUnit_Framework_MockObject_MockObject
     */
    protected $router;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dispatcher;

    protected function setUp()
    {
        parent::setUp();

        \Locale::setDefault('en');

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->router = $this->getMockBuilder('Symfony\Component\Routing\RouterInterface')->getMock();
        $this->requestStack = new RequestStack();
        /* @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->requestStack->push($request);

        $this->router->expects($this->any())
            ->method('generate')
            ->will($this->returnCallback(function ($param) {
                return '/'.$param;
            }))
        ;

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(new ChoiceSelect2TypeExtension($this->dispatcher, $this->requestStack, $this->router, $this->getExtensionTypeName(), 10))
            ->getFormFactory();

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->requestStack = null;
        $this->router = null;
    }

    /**
     * @return array|null
     */
    protected function getChoices()
    {
        return;
    }

    protected function mergeOptions(array $options)
    {
        if (is_array($this->getChoices())) {
            $options['choices'] = $this->getChoices();
        }

        return $options;
    }

    protected function getDynamicLoaderInterface()
    {
        return 'Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\DynamicChoiceLoaderInterface';
    }

    protected function getAjaxLoaderInterface()
    {
        return 'Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface';
    }

    /**
     * @return string
     */
    abstract protected function getExtensionTypeName();

    /**
     * @return string
     */
    abstract protected function getSingleData();

    /**
     * @return string
     */
    abstract protected function getValidSingleValue();

    /**
     * @return string
     */
    abstract protected function getValidAjaxSingleValue();

    /**
     * @return array
     */
    abstract protected function getMultipleData();

    /**
     * @return array
     */
    abstract protected function getValidMultipleValue();

    /**
     * @return string
     */
    abstract protected function getValidAjaxMultipleValue();

    public function testDefaultOptions()
    {
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions(array()));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertFalse($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->validateChoiceLoaderForDefaultOptions($config);

        $view = $form->createView();

        $this->assertFalse(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
    }

    protected function validateChoiceLoaderForDefaultOptions(FormConfigInterface $config)
    {
        $this->assertNull($config->getOption('choice_loader'));
    }

    public function testDefaultEnabledOptions()
    {
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions(array(
            'select2' => array(
                'enabled' => true,
            ),
        )));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getDynamicLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
    }

    public function testDisabled()
    {
        $options = array('select2' => array('enabled' => false));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertFalse($select2Opts['enabled']);
        $this->assertFalse($select2Opts['tags']);

        $view = $form->createView();
        $this->assertFalse(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
    }

    public function testSingleWithTags()
    {
        $options = array('select2' => array('enabled' => true, 'tags' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf($this->getDynamicLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidSingleValue(), $view->vars['value']);
        $this->assertTrue(array_key_exists('tags', $view->vars['select2']));
    }

    public function testSingleAjax()
    {
        $options = array('select2' => array('enabled' => true, 'ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getAjaxLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxSingleValue(), $view->vars['value']);
    }

    public function testSingleAjaxWithTags()
    {
        $options = array('select2' => array('enabled' => true, 'ajax' => true, 'tags' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getSingleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertFalse($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertTrue($select2Opts['tags']);
        $this->assertInstanceOf($this->getAjaxLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getSingleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxSingleValue(), $view->vars['value']);
    }

    public function testMultiple()
    {
        $options = array('multiple' => true, 'select2' => array('enabled' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getMultipleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertFalse($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getDynamicLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getMultipleData(), $view->vars['data']);
        $this->assertEquals($this->getValidMultipleValue(), $view->vars['value']);
    }

    public function testMultipleAjax()
    {
        $options = array('multiple' => true, 'select2' => array('enabled' => true, 'ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), $this->getMultipleData(), $this->mergeOptions($options));
        $config = $form->getConfig();

        $this->assertFalse($config->getOption('compound'));
        $this->assertTrue($config->getOption('multiple'));
        $this->assertTrue($config->hasOption('select2'));
        $select2Opts = $config->getOption('select2');
        $this->assertTrue($select2Opts['enabled']);
        $this->assertTrue($select2Opts['ajax']);
        $this->assertFalse($select2Opts['tags']);
        $this->assertInstanceOf($this->getAjaxLoaderInterface(), $config->getOption('choice_loader'));

        $view = $form->createView();

        $this->assertTrue(array_key_exists('select2', $view->vars));
        $this->assertEquals($this->getMultipleData(), $view->vars['data']);
        $this->assertEquals($this->getValidAjaxMultipleValue(), $view->vars['value']);
    }

    public function testRequiredAjaxEmptyChoice()
    {
        $options = array('select2' => array('enabled' => true, 'ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertEquals(array(), $view->vars['choices']);
    }

    public function testSinglePlaceHolder()
    {
        $options = array('required' => false, 'select2' => array('enabled' => true, 'ajax' => true));
        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertTrue(isset($view->vars['placeholder']));
        $this->assertEquals('', $view->vars['placeholder']);
    }

    public function testAjaxRoute()
    {
        $options = array('required' => false, 'select2' => array('enabled' => true, 'ajax' => true, 'ajax_route' => 'foobar'));
        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));
        $view = $form->createView();

        $this->assertEquals('/foobar', $view->vars['select2']['ajax']['url']);
    }

    public function testChoiceLoaderOption()
    {
        $choiceLoader = $this->getMockBuilder($this->getDynamicLoaderInterface())->getMock();
        $choiceLoader->expects($this->any())
            ->method('loadValuesForChoices')
            ->will($this->returnValue(array()));
        $choiceLoader->expects($this->any())
            ->method('loadChoicesForValues')
            ->will($this->returnValue(array()));

        $options = array('select2' => array('enabled' => true), 'choice_loader' => $choiceLoader);

        $form = $this->factory->create($this->getExtensionTypeName(), null, $this->mergeOptions($options));

        $this->assertSame($choiceLoader, $form->getConfig()->getOption('choice_loader'));
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The "choice_loader" option must be an instance of DynamicChoiceLoaderInterface or the "choices" option must be an array
     */
    public function testInvalidChoiceLoaderOption()
    {
        $options = array('select2' => array('enabled' => true), 'choices' => null);

        $this->factory->create($this->getExtensionTypeName(), null, $options);
    }
}
