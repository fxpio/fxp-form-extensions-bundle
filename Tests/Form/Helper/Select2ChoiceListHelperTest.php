<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\FormExtensionsBundle\Tests\Form\Extension;

/**
 * Tests case for select2 choice list helper.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class Select2ChoiceListHelperTest extends AjaxChoiceListHelperTest
{
    /**
     * {@inheritdoc}
     */
    protected function getHelperClass()
    {
        return 'Sonatra\Bundle\FormExtensionsBundle\Form\Helper\Select2ChoiceListHelper';
    }

    /**
     * @dataProvider getAjaxIds
     *
     * @param null|string|array $ajaxIds
     */
    public function testGenerateResponseWithCreateFormatter($ajaxIds)
    {
        $validContent = array(
            'size' => null,
            'pageNumber' => null,
            'pageSize' => null,
            'search' => null,
            'items' => array(),
        );

        $this->executeGenerateResponseWithCreateFormatter($ajaxIds, $validContent);
    }

    public function testInvalidFormatter()
    {
        // skip test
    }
}
