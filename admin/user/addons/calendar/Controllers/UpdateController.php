<?php
/**
 * Calendar for ExpressionEngine
 *
 * @package       Solspace:Calendar
 * @author        Solspace, Inc.
 * @copyright     Copyright (c) 2010-2020, Solspace, Inc.
 * @link          https://docs.solspace.com/expressionengine/calendar/
 * @license       https://docs.solspace.com/license-agreement/
 */

namespace Solspace\Addons\Calendar\Controllers;

use Solspace\Addons\Calendar\Services\UpdateService;
use Solspace\Addons\Calendar\Utilities\ControlPanel\CpView;
use Solspace\Addons\Calendar\Utilities\ControlPanel\Navigation\NavigationLink;
use Solspace\Addons\Calendar\Utilities\ControlPanel\View;

class UpdateController
{
    /**
     * @return View
     */
    public function index()
    {
        $updateService = new UpdateService();

        $view = new CpView(
            'update/index',
            array(
                'updates' => $updateService->getInstallableUpdates(),
                'format'  => ee()->config->item('date_format'),
            )
        );

        $view->setHeading('Updates');

        return $view;
    }
}
