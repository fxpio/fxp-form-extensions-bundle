<?php

/*
 * This file is part of the Fxp package.
 *
 * (c) François Pluchino <francois.pluchino@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fxp\Bundle\FormExtensionsBundle\Controller;

use Fxp\Component\FormExtensions\Form\Helper\AjaxChoiceListHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author François Pluchino <francois.pluchino@gmail.com>
 */
class AjaxFormController extends Controller
{
    /**
     * Gets the ajax response of choice list.
     *
     * @param Request $request
     * @param string  $type
     *
     * @return Response
     */
    public function ajaxChoiceListAction(Request $request, $type)
    {
        return AjaxChoiceListHelper::generateResponse($request,
            $this->get('form.factory')->createBuilder($type, null,
                array('select2' => array('enabled' => true, 'ajax' => true))));
    }
}
