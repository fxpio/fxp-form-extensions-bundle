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
use Sonatra\Bundle\FormExtensionsBundle\DependencyInjection\SonatraFormExtensionsExtension;
use Sonatra\Bundle\FormExtensionsBundle\SonatraFormExtensionsBundle;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Tests case for Extension.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class SonatraFormExtensionsExtensionTest extends TestCase
{
    public function testExtensionExist()
    {
        $container = $this->createContainer();

        $this->assertTrue($container->hasExtension('sonatra_form_extensions'));
    }

    public function testExtensionLoader()
    {
        $container = $this->createContainer();

        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.choice_select2'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.country_select2'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.currency_select2'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.language_select2'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.locale_select2'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.timezone_select2'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.collection_select2'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.entity_select2'));

        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.datetime_jquery'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.date_jquery'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.time_jquery'));
        $this->assertTrue($container->hasDefinition('form.type_extension.sonatra.birthday_jquery'));
    }

    public function testExtensionLoaderWithDisabledConfig()
    {
        $container = $this->createContainer(array(
            'select2' => array('enabled' => false),
            'datetime_picker' => array('enabled' => false),
        ));

        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.choice_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.country_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.currency_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.language_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.locale_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.timezone_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.collection_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.entity_select2'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.entity_collection_select2'));

        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.datetime_jquery'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.date_jquery'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.time_jquery'));
        $this->assertFalse($container->hasDefinition('form.type_extension.sonatra.birthday_jquery'));
    }

    public function testExtensionLoaderWithCustomTwigResources()
    {
        $container = $this->createContainer(array(), array(
            'form_themes' => array(
                '@Test/Form/form_test.html.twig',
            ),
        ));

        $resources = $container->getParameter('twig.form.resources');
        $this->assertEquals(array(
            'form_div_layout.html.twig',
            '@SonatraFormExtensions/Form/form_div_layout.html.twig',
            '@Test/Form/form_test.html.twig',
        ), $resources);
    }

    protected function createContainer(array $config = array(), array $twigConfig = array())
    {
        $configs = empty($config) ? array() : array($config);
        $twigConfigs = empty($twigConfig) ? array() : array($twigConfig);
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.bundles' => array(
                'FrameworkBundle' => 'Symfony\\Bundle\\FrameworkBundle\\FrameworkBundle',
                'TwigBundle' => 'Symfony\\Bundle\\TwigBundle\\TwigBundle',
                'SonatraFormExtensionsBundle' => 'Sonatra\\Bundle\\FormExtensionsBundle\\SonatraFormExtensionsBundle',
            ),
            'kernel.bundles_metadata' => array(),
            'kernel.cache_dir' => __DIR__,
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => __DIR__,
            'kernel.charset' => 'UTF-8',
            'locale' => 'en',
            'assetic.debug' => true,
        )));

        $sfExt = new FrameworkExtension();
        $twigExt = new TwigExtension();
        $extension = new SonatraFormExtensionsExtension();

        $container->registerExtension($sfExt);
        $container->registerExtension($twigExt);
        $container->registerExtension($extension);

        $sfExt->load(array(), $container);
        $twigExt->load($twigConfigs, $container);
        $extension->load($configs, $container);

        if (!empty($twigConfigs)) {
            $container->prependExtensionConfig('twig', $twigConfigs[0]);
            $container->setDefinition('twig.loader.filesystem', new Definition(TwigEngine::class));
        }

        $bundle = new SonatraFormExtensionsBundle();
        $bundle->build($container);

        $container->getCompilerPassConfig()->setOptimizationPasses(array());
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}
