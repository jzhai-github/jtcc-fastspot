<?php

namespace fostermade\ferret\engines;

interface EngineInterface
{
    /**
     * Get the settings for this particular engine
     *
     * @param $settings
     * @return array
     */
    public static function getSettingsForm($settings);

    /**
     * Retrieves all the indexes available
     *
     * @return array
     */
    public function getIndices();

    /**
     * Adds/Updates objects in the index
     *
     * @param $indexName
     * @param $objects
     * @throws \Exception
     * @return bool|string
     */
    public function index($indexName, $objects);

    /**
     * Deletes an object from the index
     *
     * @param $indexName
     * @param $objects
     * @throws \Exception
     * @return bool
     */
    public function delete($indexName, $objects);

    /**
     * Deletes all objects in the specified index
     *
     * @param $indexName
     * @return bool|string
     */
    public function clearIndex($indexName);
}
