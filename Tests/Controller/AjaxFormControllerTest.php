<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Controller;

use Sonatra\Bundle\FormExtensionsBundle\Controller\AjaxFormController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests case for controller.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxFormControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AjaxFormController
     */
    protected $controller;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    protected function setUp()
    {
        $this->controller = new AjaxFormController();
        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $this->helper = $this->getMockClass('Sonatra\Component\FormExtensions\Form\Helper\AjaxChoiceListHelper', array('generateResponse'));

        $ajaxFormatter = $this->getMockBuilder('Sonatra\Component\FormExtensions\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface')->getMock();
        $ajaxFormatter->expects($this->any())
            ->method('formatResponseData')
            ->will($this->returnValue('AJAX_FORMATTER_MOCK'));

        $ajaxChoiceLoader = $this->getMockBuilder('Sonatra\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilderInterface')->getMock();
        $formBuilder->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnCallback(function ($value) use ($ajaxFormatter, $ajaxChoiceLoader) {
                if ('select2' === $value) {
                    return array(
                        'ajax_formatter' => $ajaxFormatter,
                    );
                } elseif ('choice_loader') {
                    return $ajaxChoiceLoader;
                }

                return $value;
            }));

        $formFactory = $this->getMockBuilder('Symfony\Component\Form\FormFactoryInterface')->getMock();
        $formFactory->expects($this->any())
            ->method('createBuilder')
            ->will($this->returnValue($formBuilder));

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')->getMock();
        $container->expects($this->any())
            ->method('get')
            ->will($this->returnValue($formFactory));

        /* @var ContainerInterface $container */
        $this->controller->setContainer($container);
    }

    protected function tearDown()
    {
        $this->controller = null;
        $this->helper = null;
        $this->request = null;
    }

    public function testAjaxChoiceListAction()
    {
        /* @var Request $request */
        $request = $this->request;

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode('AJAX_FORMATTER_MOCK'));

        $this->assertEquals($response->getContent(), $this->controller->ajaxChoiceListAction($request, 'locale')->getContent());
    }
}
