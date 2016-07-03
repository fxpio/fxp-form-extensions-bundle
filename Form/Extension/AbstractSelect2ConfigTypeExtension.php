<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Form\Extension;

use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\Select2AjaxChoiceListFormatter;
use Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Loader\AjaxChoiceLoaderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ChoiceList\Factory\ChoiceListFactoryInterface;
use Symfony\Component\Form\ChoiceList\Factory\DefaultChoiceListFactory;
use Symfony\Component\Form\ChoiceList\Factory\PropertyAccessDecorator;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class AbstractSelect2ConfigTypeExtension extends AbstractTypeExtension
{
    /**
     * @var int
     */
    protected $ajaxPageSize;

    /**
     * @var ChoiceListFactoryInterface
     */
    protected $choiceListFactory;

    /**
     * Constructor.
     *
     * @param int                        $defaultPageSize
     * @param ChoiceListFactoryInterface $choiceListFactory
     */
    public function __construct($defaultPageSize = 10, ChoiceListFactoryInterface $choiceListFactory = null)
    {
        $this->ajaxPageSize = $defaultPageSize;
        $this->choiceListFactory = $choiceListFactory ?: new PropertyAccessDecorator(new DefaultChoiceListFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'select2' => array(),
        ));

        $resolver->setAllowedTypes('select2', 'array');

        $this->addSelect2Normalizer($resolver);
        $this->addChoiceLoaderNormalizer($resolver);
    }

    /**
     * @param OptionsResolver $resolver The options resolver
     */
    protected function addSelect2Normalizer(OptionsResolver $resolver)
    {
        $choiceListFactory = $this->choiceListFactory;
        $ajaxPageSize = $this->ajaxPageSize;

        $resolver->setNormalizer('select2', function (Options $options, $value) use ($choiceListFactory, $ajaxPageSize) {
            $select2Resolver = new OptionsResolver();

            $select2Resolver->setDefaults(array(
                'enabled' => false,
                'wrapper_attr' => array(),
                'width' => '100%',
                'template_result' => null,
                'template_selection' => null,
                'dropdown_parent' => null,
                'selection_adapter' => null,
                'data_adapter' => null,
                'dropdown_adapter' => null,
                'results_adapter' => null,
                'min_input_length' => null,
                'max_input_length' => null,
                'min_results_for_search' => null,
                'close_on_select' => null,
                'token_separators' => array(','),
                'create_tag' => null,
                'matcher' => null,
                'data' => null,
                'dir' => null,
                'theme' => null,
                'language' => \Locale::getDefault(),
                'tags' => false,
                'ajax' => false,
                'ajax_formatter' => new Select2AjaxChoiceListFormatter($choiceListFactory),
                'ajax_parameters' => array(),
                'ajax_reference_type' => (bool) RouterInterface::ABSOLUTE_PATH,
                'ajax_data_type' => 'json',
                'ajax_delay' => 250,
                'ajax_cache' => false,
                'ajax_route' => null,
                'ajax_page_size' => $ajaxPageSize,
            ));

            $select2Resolver->setAllowedTypes('enabled', 'bool');
            $select2Resolver->setAllowedTypes('wrapper_attr', 'array');
            $select2Resolver->setAllowedTypes('template_result', array('null', 'string'));
            $select2Resolver->setAllowedTypes('template_selection', array('null', 'string'));
            $select2Resolver->setAllowedTypes('dropdown_parent', array('null', 'string'));
            $select2Resolver->setAllowedTypes('selection_adapter', array('null', 'string'));
            $select2Resolver->setAllowedTypes('data_adapter', array('null', 'string'));
            $select2Resolver->setAllowedTypes('dropdown_adapter', array('null', 'string'));
            $select2Resolver->setAllowedTypes('results_adapter', array('null', 'string'));
            $select2Resolver->setAllowedTypes('matcher', array('null', 'string'));
            $select2Resolver->setAllowedTypes('create_tag', array('null', 'string'));
            $select2Resolver->setAllowedTypes('min_input_length', array('null', 'int'));
            $select2Resolver->setAllowedTypes('max_input_length', array('null', 'int'));
            $select2Resolver->setAllowedTypes('min_results_for_search', array('null', 'int', 'string'));
            $select2Resolver->setAllowedTypes('close_on_select', array('null', 'bool'));
            $select2Resolver->setAllowedTypes('token_separators', array('null', 'array'));
            $select2Resolver->setAllowedTypes('data', array('null', 'array'));
            $select2Resolver->setAllowedValues('dir', array(null, 'ltr', 'rtl'));
            $select2Resolver->setAllowedTypes('width', array('null', 'string'));
            $select2Resolver->setAllowedTypes('theme', array('null', 'string'));
            $select2Resolver->setAllowedTypes('language', 'string');
            $select2Resolver->setAllowedTypes('tags', 'bool');
            $select2Resolver->setAllowedTypes('ajax', 'bool');
            $select2Resolver->setAllowedTypes('ajax_formatter', 'Sonatra\Bundle\FormExtensionsBundle\Form\ChoiceList\Formatter\AjaxChoiceListFormatterInterface');
            $select2Resolver->setAllowedTypes('ajax_parameters', 'array');
            $select2Resolver->setAllowedTypes('ajax_reference_type', 'bool');
            $select2Resolver->setAllowedTypes('ajax_data_type', array('null', 'string'));
            $select2Resolver->setAllowedTypes('ajax_delay', array('null', 'int'));
            $select2Resolver->setAllowedTypes('ajax_cache', 'bool');
            $select2Resolver->setAllowedTypes('ajax_route', array('null', 'string'));
            $select2Resolver->setAllowedTypes('ajax_page_size', 'int');

            return $select2Resolver->resolve($value);
        });
    }

    /**
     * @param OptionsResolver $resolver The options resolver
     */
    protected function addChoiceLoaderNormalizer(OptionsResolver $resolver)
    {
        $choiceListFactory = $this->choiceListFactory;

        if ($resolver->isDefined('choice_loader')) {
            $resolver->setNormalizer('choice_loader', function (Options $options, $value) use ($choiceListFactory) {
                if ($options['select2']['enabled']) {
                    $value = Select2Util::convertToDynamicLoader($choiceListFactory, $options, $value);
                    $value->setAllowAdd($options['select2']['tags']);

                    if ($value instanceof AjaxChoiceLoaderInterface) {
                        $value->setPageSize($options['select2']['ajax_page_size']);
                        $value->setPageNumber(1);
                        $value->setSearch('');
                        $value->setIds(array());
                        $value->reset();
                    }
                }

                return $value;
            });
        }
    }
}
