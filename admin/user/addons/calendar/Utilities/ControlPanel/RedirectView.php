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

namespace Solspace\Addons\Calendar\Utilities\ControlPanel;

class RedirectView extends View implements RenderlessViewInterface
{
    /** @var string */
    private $url;

    /**
     * RedirectView constructor.
     *
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    public function compile()
    {
        header('Location: ' . $this->url);
        die();
    }

}
