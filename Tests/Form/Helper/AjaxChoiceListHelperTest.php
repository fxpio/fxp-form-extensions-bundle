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

use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Sonatra\Bundle\FormExtensionsBundle\Form\Helper\AjaxChoiceListHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests case for choice list helper.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxChoiceListHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return string
     */
    protected function getHelperClass()
    {
        return 'Sonatra\Bundle\FormExtensionsBundle\Form\Helper\AjaxChoiceListHelper';
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidArgumentException
     * @expectedExceptionMessage The 'invalid' format is not allowed. Try with 'xml', 'json'
     */
    public function testInvalidFormat()
    {
        /* @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var AjaxChoiceLoaderInterface $choiceLoader */
        $choiceLoader = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();

        $helper = $this->getHelperClass();
        /* @var AjaxChoiceListHelper $helper */
        $helper::generateResponse($request, $choiceLoader, 'invalid');
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\InvalidArgumentException
     * @expectedExceptionMessage You must create a child class of AjaxChoiceLostHelper and override the "createChoiceListFormatter" method
     */
    public function testInvalidFormatter()
    {
        /* @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var AjaxChoiceLoaderInterface $choiceLoader */
        $choiceLoader = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();

        $helper = $this->getHelperClass();
        /* @var AjaxChoiceListHelper $helper */
        $helper::generateResponse($request, $choiceLoader);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface", "NULL" given
     */
    public function testInvalidFormAjaxFormatter()
    {
        /* @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilderInterface')->getMock();

        /* @var FormInterface|\PHPUnit_Framework_MockObject_MockObject $form */
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $form->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($formBuilder));

        $helper = $this->getHelperClass();
        /* @var AjaxChoiceListHelper $helper */
        $helper::generateResponse($request, $form);
    }

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     * @expectedExceptionMessage Expected argument of type "Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface", "NULL" given
     */
    public function testInvalidChoiceLoader()
    {
        /* @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $formatter = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface')->getMock();

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilderInterface')->getMock();
        $formBuilder->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnCallback(function ($value) use ($formatter) {
                return 'select2' === $value
                    ? array(
                        'ajax_formatter' => $formatter,
                    )
                    : null;
            }));

        /* @var FormInterface|\PHPUnit_Framework_MockObject_MockObject $form */
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $form->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($formBuilder));

        $helper = $this->getHelperClass();
        /* @var AjaxChoiceListHelper $helper */
        $helper::generateResponse($request, $form);
    }

    public function getAjaxIds()
    {
        return array(
            array(null),
            array(''),
            array('1'),
            array('1,2'),
            array(array(1, 2)),
        );
    }

    /**
     * @dataProvider getAjaxIds
     *
     * @param null|string|array $ajaxIds
     */
    public function testGenerateResponse($ajaxIds)
    {
        /* @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($value) use ($ajaxIds) {
                return 'prefix_ids' === $value
                    ? $ajaxIds
                    : null;
            }));

        $formatter = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface')->getMock();
        $formatter->expects($this->any())
            ->method('formatResponseData')
            ->will($this->returnValue('MOCK_FORMATTED_DATA'));

        $choiceLoader = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilderInterface')->getMock();
        $formBuilder->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnCallback(function ($value) use ($formatter, $choiceLoader) {
                if ('select2' === $value) {
                    return array(
                        'ajax_formatter' => $formatter,
                    );
                } elseif ('choice_loader' === $value) {
                    return $choiceLoader;
                }

                return $value;
            }));

        /* @var FormInterface|\PHPUnit_Framework_MockObject_MockObject $form */
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')->getMock();
        $form->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($formBuilder));

        $helper = $this->getHelperClass();
        /* @var AjaxChoiceListHelper $helper */
        $res = $helper::generateResponse($request, $form, 'json', 'prefix_');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $res);
        $this->assertSame('application/json', $res->headers->get('Content-Type'));
        $this->assertSame(json_encode('MOCK_FORMATTED_DATA'), $res->getContent());
    }

    /**
     * @param null|string|array $ajaxIds
     * @param array             $validContent
     */
    protected function executeGenerateResponseWithCreateFormatter($ajaxIds, array $validContent)
    {
        /* @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($value) use ($ajaxIds) {
                return 'prefix_ids' === $value
                    ? $ajaxIds
                    : null;
            }));

        $choiceList = $this->getMockBuilder('Symfony\Component\Form\ChoiceList\ChoiceListInterface')->getMock();
        $choiceList->expects($this->any())
            ->method('getChoices')
            ->will($this->returnValue(array()));
        $choiceList->expects($this->any())
            ->method('getOriginalKeys')
            ->will($this->returnValue(array()));
        $choiceList->expects($this->any())
            ->method('getStructuredValues')
            ->will($this->returnValue(array()));

        /* @var AjaxChoiceLoaderInterface|\PHPUnit_Framework_MockObject_MockObject $choiceLoader */
        $choiceLoader = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();
        $choiceLoader->expects($this->any())
            ->method('loadPaginatedChoiceList')
            ->will($this->returnValue($choiceList));

        $helper = $this->getHelperClass();
        /* @var AjaxChoiceListHelper $helper */
        $res = $helper::generateResponse($request, $choiceLoader, 'json', 'prefix_');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $res);
        $this->assertSame('application/json', $res->headers->get('Content-Type'));
        $this->assertEquals($validContent, json_decode($res->getContent(), true));
    }
}
