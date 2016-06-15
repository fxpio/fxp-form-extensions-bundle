<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Event;

use Sonatra\Bundle\FormExtensionsBundle\Event\GetAjaxChoiceListEvent;
use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface;
use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Tests case for choice list event.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class GetAjaxChoiceListEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GetAjaxChoiceListEvent
     */
    protected $event;

    protected function setUp()
    {
        /* @var Request $request */
        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')->getMock();
        $requestStack = new RequestStack();
        $requestStack->push($request);

        /* @var AjaxChoiceLoaderInterface $choiceLoader */
        $choiceLoader = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface')->getMock();

        /* @var AjaxChoiceListFormatterInterface|\PHPUnit_Framework_MockObject_MockObject $formatter */
        $formatter = $this->getMockBuilder('Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface')->getMock();
        $formatter->expects($this->any())
            ->method('formatResponseData')
            ->will($this->returnValue('AJAX_FORMATTER_MOCK'));

        $this->event = new GetAjaxChoiceListEvent('foo', $requestStack, $choiceLoader, $formatter, 'json');
    }

    protected function tearDown()
    {
        $this->event = null;
    }

    public function testAjaxChoiceListAction()
    {
        $validData = 'AJAX_FORMATTER_MOCK';

        $this->assertSame($validData, $this->event->getData());
    }
}
