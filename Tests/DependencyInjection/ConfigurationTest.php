<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\FormExtensionsBundle\Tests\DependencyInjection;

use Fxp\Bundle\FormExtensionsBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * Tests case for Configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class ConfigurationTest extends TestCase
{
    public function testDefaultConfig()
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(new Configuration(), [[]]);

        $this->assertEquals(
                array_merge([], self::getBundleDefaultConfig()),
                $config
        );
    }

    protected static function getBundleDefaultConfig()
    {
        return [
            'select2' => [
                'enabled' => true,
            ],
            'datetime_picker' => [
                'enabled' => true,
            ],
            'auto_configuration' => true,
        ];
    }
}
