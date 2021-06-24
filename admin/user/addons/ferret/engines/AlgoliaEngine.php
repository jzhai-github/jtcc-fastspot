<?php

namespace fostermade\ferret\engines;

use AlgoliaSearch\AlgoliaException;
use AlgoliaSearch\Client;

class AlgoliaEngine implements EngineInterface
{
    protected $settings = null;
    protected $client = null;

    /**
     * AlgoliaEngine constructor.
     *
     * @param $settings
     * @param $searchOnly
     * @throws \Exception
     */
    public function __construct($settings, $searchOnly = false)
    {
        $this->settings = $settings;
        $credentials = $settings['credentials']['algolia'];

        $appId = $credentials['app_id'];
        $apiKey = $searchOnly ? $credentials['search_key'] : $credentials['admin_key'];

        try {
            $this->client = new Client($appId, $apiKey);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Get the settings for this particular engine
     *
     * @param $settings
     * @return array
     */
    public static function getSettingsForm($settings)
    {
        $credentials = [
            'app_id' => '',
            'search_key' => '',
            'admin_key' => '',
        ];

        if (isset($settings['credentials']['algolia'])) {
            $credentials = $settings['credentials']['algolia'];
        }

        return [
            'algolia_credentials' => [
                'group' => 'algolia_credentials',
                'settings' => [
                    [
                        'title' => 'app_id',
                        'fields' => [
                            'credentials[algolia][app_id]' => [
                                'type' => 'text',
                                'value' => $credentials['app_id'],
                                'required' => true,
                            ],
                        ],
                    ],
                    [
                        'title' => 'search_key',
                        'desc' => 'search_key_desc',
                        'fields' => [
                            'credentials[algolia][search_key]' => [
                                'type' => 'text',
                                'value' => $credentials['search_key'],
                                'required' => true,
                            ],
                        ],
                    ],
                    [
                        'title' => 'admin_key',
                        'desc' => 'admin_key_desc',
                        'fields' => [
                            'credentials[algolia][admin_key]' => [
                                'type' => 'password',
                                'value' => $credentials['admin_key'],
                                'required' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Retrieves all the indexes available
     *
     * @return array
     */
    public function getIndices()
    {
        try {
            $indexes = $this->client->listIndexes();

            $data = [];
            foreach ($indexes['items'] as $index) {
                $data[$index['name']] = $index['name'];
            }

            return $data;
        } catch (AlgoliaException $e) {
            return [];
        }
    }

    /**
     * Adds/Updates objects in the index
     *
     * @param $indexName
     * @param $objects
     * @throws \Exception
     * @return bool|string
     */
    public function index($indexName, $objects)
    {
        try {
            $index = $this->client->initIndex($this->buildEnvIndexName($indexName));
            $success = $index->batchObjects($objects);
        } catch (AlgoliaException $e) {
            return $e->getMessage();
        }

        return $success;
    }

    /**
     * Deletes an object from the index
     *
     * @param $indexName
     * @param $objects
     * @throws \Exception
     * @return bool
     */
    public function delete($indexName, $objects)
    {
        try {
            $index = $this->client->initIndex($this->buildEnvIndexName($indexName));
            $index->batchObjects($objects);
        } catch (AlgoliaException $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Deletes all objects in the specified index
     *
     * @param $indexName
     * @return bool|string
     */
    public function clearIndex($indexName)
    {
        try {
            $index = $this->client->initIndex($this->buildEnvIndexName($indexName));
            $index->clearIndex();
        } catch (AlgoliaException $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Sets the index prefix based on environment
     *
     * @param string $indexName
     * @return string
     */
    protected function buildEnvIndexName($indexName = '')
    {
        return (defined('ENV') ? ENV . '_' : '') . $indexName;
    }
}
