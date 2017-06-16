<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Sonatra\Bundle\FormExtensionsBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Tests case for Configuration.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), array(array()));

        $this->assertEquals(
                array_merge(array(), self::getBundleDefaultConfig()),
                $config
        );
    }

    protected static function getBundleDefaultConfig()
    {
        return array(
            'select2' => array(
                'enabled' => true,
            ),
            'datetime_picker' => array(
                'enabled' => true,
            ),
            'auto_configuration' => true,
        );
    }
}
