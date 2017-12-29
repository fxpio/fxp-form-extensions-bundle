<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\FormExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class FxpFormExtensionsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $withOrm = class_exists('Symfony\Bridge\Doctrine\Form\Type\EntityType');

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('form.xml');

        $container->setParameter('fxp_form_extensions.config.auto_configuration', $config['auto_configuration']);

        if ($withOrm) {
            $loader->load('orm_form.xml');
        }

        if (!$config['select2']['enabled']) {
            $container->removeDefinition('form.type_extension.fxp.choice_select2');
            $container->removeDefinition('form.type_extension.fxp.country_select2');
            $container->removeDefinition('form.type_extension.fxp.currency_select2');
            $container->removeDefinition('form.type_extension.fxp.language_select2');
            $container->removeDefinition('form.type_extension.fxp.locale_select2');
            $container->removeDefinition('form.type_extension.fxp.timezone_select2');
            $container->removeDefinition('form.type_extension.fxp.collection_select2');

            if ($withOrm) {
                $container->removeDefinition('form.type_extension.fxp.entity_select2');
                $container->removeDefinition('form.type_extension.fxp.fxp_entity_select2');
                $container->removeDefinition('form.type_extension.fxp.entity_collection_select2');
                $container->removeDefinition('form.type_extension.fxp.fxp_entity_collection_select2');
            }
        }

        if (!$config['datetime_picker']['enabled']) {
            $container->removeDefinition('form.type_extension.fxp.datetime_jquery');
            $container->removeDefinition('form.type_extension.fxp.date_jquery');
            $container->removeDefinition('form.type_extension.fxp.time_jquery');
            $container->removeDefinition('form.type_extension.fxp.birthday_jquery');
        }
    }
}
