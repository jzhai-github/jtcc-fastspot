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

class FileDownloadView extends View implements RenderlessViewInterface
{
    /** @var string */
    private $fileName;

    /** @var string */
    private $content;

    /**
     * AjaxView constructor.
     *
     * @param string $fileName
     * @param string $content
     */
    public function __construct($fileName, $content)
    {
        $this->fileName = $fileName;
        $this->content  = $content;
    }

    /**
     * @return array
     */
    public function compile()
    {
        $fileName = sprintf('"%s"', addcslashes(($this->fileName), '"\\'));
        $size     = strlen($this->content);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $size);

        echo $this->content;

        die();
    }
}
