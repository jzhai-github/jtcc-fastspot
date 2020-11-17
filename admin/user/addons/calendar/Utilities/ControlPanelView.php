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

namespace Solspace\Addons\Calendar\Utilities;

use Solspace\Addons\Calendar\Library\AddonBuilder;
use Solspace\Addons\Calendar\Utilities\ControlPanel\AjaxView;
use Solspace\Addons\Calendar\Utilities\ControlPanel\CpView;
use Solspace\Addons\Calendar\Utilities\ControlPanel\Navigation\Navigation;
use Solspace\Addons\Calendar\Utilities\ControlPanel\RedirectView;
use Solspace\Addons\Calendar\Utilities\ControlPanel\RenderlessViewInterface;
use Solspace\Addons\Calendar\Utilities\ControlPanel\View;

class ControlPanelView extends AddonBuilder
{
    /**
     * Returns a navigation view.
     * Override this method to customize the navigation view
     *
     * @return Navigation
     */
    protected function buildNavigation()
    {
        return new Navigation();
    }

    /**
     * @param View $view
     *
     * @return array
     */
    protected final function renderView(View $view)
    {
        if ($view instanceof AjaxView) {
            header('Content-Type: application/json');
            echo json_encode($view->compile());
            die();
        }

        if ($view instanceof RenderlessViewInterface) {
            $view->compile();
            die();
        }

        $viewData = array();
        if ($view instanceof CpView) {
            $addonInfo = AddonInfo::getInstance();

            $breadcrumbs = array(
                ee('CP/URL')->make('addons/settings/freeform_next')->compile() => $addonInfo->getName(),
            );

            foreach ($view->getBreadcrumbs() as $breadcrumb) {
                $breadcrumbs[$breadcrumb->getLink()->compile()] = $breadcrumb->getTitle();
            }


            $viewData = array(
                'sidebar'    => $view->isSidebarDisabled() ? null : $this->buildNavigation()->buildNavigationView(),
                'body'       => $view->compile(),
                'breadcrumb' => $breadcrumbs,
            );
        }

        if ($view->getHeading()) {
            $viewData['heading'] = $view->getHeading();
        }

        return $viewData;
    }

    /**
     * @param string $target
     *
     * @return mixed
     */
    protected function getLink($target)
    {
        return ee('CP/URL', 'addons/settings/freeform_next/' . $target);
    }
}
