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

namespace Solspace\Addons\Calendar\Services;

use Solspace\Addons\Calendar\Library\DataObjects\PluginUpdate;
use Solspace\Addons\Calendar\Utilities\AddonInfo;

class UpdateService
{
    /** @var PluginUpdate[] */
    private static $feed;

    /** @var string */
    private $jsonUrl;

    /** @var string */
    private $jsonPath;

    /** @var AddonInfo */
    private $addonInfo;

    /** @var bool */
    private $writeToCache;

    /**
     * UpdateService constructor.
     */
    public function __construct()
    {
        $cacheDir = PATH_CACHE . '/calendar';

        $this->jsonUrl   = 'https://accounts.solspace.com/expressionengine/updates/calendar3.json';
        $this->jsonPath  = $cacheDir . '/changelog.json';
        $this->addonInfo = AddonInfo::getInstance();

        $this->writeToCache = true;
        if (!file_exists($cacheDir) && !@mkdir($cacheDir) && !is_dir($cacheDir)) {
            $this->writeToCache = false;
        }

        $jsonPath = $this->jsonPath;
        if (!file_exists($jsonPath)) {
            $tmpFile = $cacheDir . '/tmpFile';

            if (!@touch($tmpFile)) {
                $this->writeToCache = false;
            } else {
                @unlink($tmpFile);
            }
        }
    }

    /**
     * @return bool
     */
    public function updateCount()
    {
        return count($this->getInstallableUpdates());
    }

    /**
     * @return PluginUpdate[]
     */
    public function getInstallableUpdates()
    {
        if (!$this->writeToCache) {
            return array();
        }

        if (null === self::$feed) {
            $pluginVersion = $this->addonInfo->getVersion();
            $feedJsonData  = $this->getDecodedJsonFeed();

            $feedData = array();
            if (is_array($feedJsonData)) {
                foreach ($feedJsonData as $item) {
                    if (version_compare($pluginVersion, $item->version, '>=')) {
                        continue;
                    }

                    $date       = new \DateTime($item->date);
                    $feedData[] = new PluginUpdate($item->version, $item->downloadUrl, $date, $item->notes);
                }
            }

            self::$feed = array_reverse($feedData);
        }

        return self::$feed;
    }

    /**
     * Fetches the latest JSON feed and outputs a bool of whether it's contents
     * contained anything or not
     *
     * @return bool
     */
    private function fetchLatestFeed()
    {
        $content = @file_get_contents($this->jsonUrl) ?: '';

        $handle = fopen($this->jsonPath, 'wb+');
        fwrite($handle, $content);
        fclose($handle);

        return !empty($content);
    }

    /**
     * @return array
     */
    private function getDecodedJsonFeed()
    {
        if (!file_exists($this->jsonPath) && !$this->fetchLatestFeed()) {
            return array();
        }

        $modTime = filemtime($this->jsonPath);
        if ($modTime < strtotime('-6 hours') && !$this->fetchLatestFeed()) {
            return array();
        }

        $content = file_get_contents($this->jsonPath);

        return json_decode($content, false);
    }
}
