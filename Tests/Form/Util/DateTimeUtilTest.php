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

use Sonatra\Bundle\FormExtensionsBundle\Form\Util\DateTimeUtil;

/**
 * Tests case for datetime util.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class DateTimeUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testGetJsFormat()
    {
        $this->assertTrue(in_array(DateTimeUtil::getJsFormat('en_US'), array('M/D/YYYY h:mm A', 'M/D/YYYY, h:mm A')));
    }

    public function testGetJsFormatFr()
    {
        $this->assertSame('DD/MM/YYYY HH:mm', DateTimeUtil::getJsFormat('fr_FR'));
    }
}
