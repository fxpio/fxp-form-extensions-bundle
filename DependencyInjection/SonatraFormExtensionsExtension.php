<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SonatraFormExtensionsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('form.xml');

        $container->setParameter('sonatra_form_extensions.config.auto_configuration', $config['auto_configuration']);

        if (!$config['select2']['enabled']) {
            $container->removeDefinition('form.type_extension.sonatra.choice_select2');
            $container->removeDefinition('form.type_extension.sonatra.country_select2');
            $container->removeDefinition('form.type_extension.sonatra.currency_select2');
            $container->removeDefinition('form.type_extension.sonatra.language_select2');
            $container->removeDefinition('form.type_extension.sonatra.locale_select2');
            $container->removeDefinition('form.type_extension.sonatra.timezone_select2');
            $container->removeDefinition('form.type_extension.sonatra.collection_select2');
            $container->removeDefinition('form.type_extension.sonatra.entity_select2');
            $container->removeDefinition('form.type_extension.sonatra.entity_collection_select2');
        }

        if (!$config['datetime_picker']['enabled']) {
            $container->removeDefinition('form.type_extension.sonatra.datetime_jquery');
            $container->removeDefinition('form.type_extension.sonatra.date_jquery');
            $container->removeDefinition('form.type_extension.sonatra.time_jquery');
            $container->removeDefinition('form.type_extension.sonatra.birthday_jquery');
        }

        if (class_exists('Symfony\Bridge\Doctrine\Form\Type\EntityType')) {
            $loader->load('orm_form.xml');
        }
    }
}
