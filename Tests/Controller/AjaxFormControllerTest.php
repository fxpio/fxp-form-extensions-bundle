<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\FormExtensionsBundle\Tests\Controller;

use Fxp\Bundle\FormExtensionsBundle\Controller\AjaxFormController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests case for controller.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AjaxFormControllerTest extends TestCase
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
        $this->helper = $this->getMockClass('Fxp\Component\FormExtensions\Form\Helper\AjaxChoiceListHelper', ['generateResponse']);

        $ajaxFormatter = $this->getMockBuilder('Fxp\Component\FormExtensions\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface')->getMock();
        $ajaxFormatter->expects($this->any())
            ->method('formatResponseData')
            ->will($this->returnValue('AJAX_FORMATTER_MOCK'));

        $ajaxChoiceLoader = $this->getMockBuilder('Fxp\Component\FormExtensions\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilderInterface')->getMock();
        $formBuilder->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnCallback(function ($value) use ($ajaxFormatter, $ajaxChoiceLoader) {
                if ('select2' === $value) {
                    return [
                        'ajax_formatter' => $ajaxFormatter,
                    ];
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
