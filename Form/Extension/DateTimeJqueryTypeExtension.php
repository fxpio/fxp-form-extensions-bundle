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

use Sonatra\Bundle\FormExtensionsBundle\Form\Util\DateTimeUtil;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class DateTimeJqueryTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['widget'] !== 'single_text') {
            return;
        }

        $attr = $view->vars['attr'];
        $dataAttributes = array(
            'locale',
            'date_picker',
            'time_picker',
            'time_picker_first',
            'button_id',
            'open_focus',
            'format',
            'with_minutes',
            'with_seconds',
            'hour_min',
            'hour_max',
            'hour_step',
            'minute_min',
            'minute_max',
            'minute_step',
            'second_min',
            'second_max',
            'second_step',
        );

        foreach ($dataAttributes as $dataAttr) {
            $name = str_replace('_', '-', $dataAttr);
            $value = $options[$dataAttr];

            if (true === $value) {
                $value = 'true';
            } elseif (false === $value) {
                $value = 'false';
            } elseif (null === $value) {
                continue;
            }

            $attr['data-'.$name] = $value;
        }

        $view->vars['attr'] = array_merge($attr, array(
            'data-datetime-picker' => 'true',
            'data-button-id' => $view->vars['id'].'_datetime_btn',
            'data-format' => DateTimeUtil::convertToJsFormat($attr['data-format']),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $format = function (Options $options) {
            return DateTimeUtil::getPattern($options['locale'], $options['user_timezone'],
                                            $options['date_picker'], $options['time_picker'],
                                            $options['with_seconds']);
        };

        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'locale' => \Locale::getDefault(),
            'user_timezone' => null,
            'date_picker' => true,
            'time_picker' => true,
            'time_picker_first' => false,
            'button_id' => null,
            'open_focus' => true,
            'hour_min' => null,
            'hour_max' => null,
            'hour_step' => null,
            'minute_min' => null,
            'minute_max' => null,
            'minute_step' => null,
            'second_min' => null,
            'second_max' => null,
            'second_step' => null,

            // override parent type value (merge options for datetime, date, time)
            'format' => $format,
            'placeholder' => null,
            'with_minutes' => true,
            'with_seconds' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return DateTimeType::class;
    }
}
