<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class ConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('sonatra_form_extensions.config.auto_configuration')) {
            $this->processPackages($container);
        }

        /* @var ParameterBag $pb */
        $pb = $container->getParameterBag();
        $pb->remove('sonatra_form_extensions.config.auto_configuration');
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function processPackages(ContainerBuilder $container)
    {
        $packageManagerDef = $container->getDefinition('fxp_require_asset.assetic.config.package_manager');
        $packages = array(
            array(
                'name' => '@bower/jquery',
                'patterns' => array(
                    'dist/jquery.js',
                ),
            ),
            array(
                'name' => '@bower/select2',
                'patterns' => array(
                    'dist/js/select2.js',
                ),
            ),
            array(
                'name' => '@bower/hammerjs',
                'patterns' => array(
                    'hammer.js',
                ),
            ),
            array(
                'name' => '@bower/moment',
                'patterns' => array(
                    'moment.js',
                ),
            ),
            array(
                'name' => '@bower/jquery-knob',
                'patterns' => array(
                    'js/jquery.knob.js',
                ),
            ),
            array(
                'name' => '@bower/sonatra-jquery-datetime-picker',
                'patterns' => array(
                    'js/*',
                    'less/datetime-picker.less',
                ),
            ),
        );

        $packageManagerDef->addMethodCall('addPackages', array($packages));
    }
}
