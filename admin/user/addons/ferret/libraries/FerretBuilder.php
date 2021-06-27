<?php

class FerretBuilder
{
    const ENTRY_LIMIT = 200;
    const CHUNK_LIMIT = 2000;

    protected $indexes = null;
    protected $settings = null;
    protected $config = null;

    public function __construct($params)
    {
        $this->indexes = ee('Model')->get('ferret:FerretIndex')->all();
        $this->settings = $params['settings'];
        $this->config = $params['config'];
    }

    /**
     * Builds and saves FerretObjects and Records for a channel entry
     *
     * @param $entry
     * @param $indexName
     * @return bool
     */
    public function entry($entry, $indexName = null)
    {
        $success = true;

        if ($entry->status !== 'open') {
            return $success;
        }

        foreach ($this->indexes as $index) {
            if ($indexName && $index->name !== $indexName) {
                continue;
            }

            $mapping = $index->fields;

            if (!isset($mapping[$entry->channel_id])) {
                continue;
            }

            $mapping = $mapping[$entry->channel_id];
            $keys = array_keys($mapping);
            $fields = ee('Model')
                ->get('ChannelField')
                ->filter('field_name', 'IN', $keys)
                ->all();

            $object = array_merge($this->addCoreFields($entry, $index), $this->addCustomFields($entry, $fields, $mapping));

            $objects = $this->chunkObject($object);
            $objects = $this->runIntegrations($objects, $entry);

            $success = $this->saveObjects($objects, $index->name);
        }

        return $success;
    }

    /**
     * Rebuilds a single index
     *
     * @param $indexName
     * @param $offset
     * @return bool
     */
    public function index($indexName, $offset = 0)
    {
        $index = ee('Model')
            ->get('ferret:FerretIndex')
            ->filter('name', $indexName)
            ->first();

        $indexFields = $index->fields;
        $channelIds = array_keys($indexFields);

        if (!$channelIds) {
            return false;
        }

        $entries = ee('Model')
            ->get('ChannelEntry')
            ->filter('channel_id', 'IN', $channelIds)
            ->limit(self::ENTRY_LIMIT)
            ->offset($offset)
            ->all();

        while ($entries->count()) {
            foreach ($entries as $entry) {
                $this->entry($entry, $indexName);
            }

            $offset += self::ENTRY_LIMIT;
            $entries = ee('Model')
                ->get('ChannelEntry')
                ->filter('channel_id', 'IN', $channelIds)
                ->limit(self::ENTRY_LIMIT)
                ->offset($offset)
                ->all();
        }

        return true;
    }

    /**
     * Adds all required core Expression Engine fields
     *
     * @param $entry
     * @param $index
     * @return array
     */
    protected function addCoreFields($entry, $index)
    {
        $object = [
            'objectID' => (int)$entry->site_id."-".$entry->entry_id,
            'entry_id' => (int)$entry->entry_id,
            'site_id' => $entry->site_id,
            'channel' => $entry->Channel->channel_title,
            'channel_id' => $entry->channel_id,
            'author' => is_object($entry->Author) ? $entry->Author->username : null,
            'title' => $entry->title,
            'url_title' => $entry->url_title,
            'url' => $this->setUrl($entry, $index),
            'status' => $entry->status,
            'categories' => $this->getCategories($entry, $index),
            'entry_date' => $entry->entry_date,
            'objectAction' => 'addObject',
        ];

        return $object;
    }

    /**
     * Adds all custom created fields specified in the index
     *
     * @param $entry
     * @param $fields
     * @param $mapping
     * @return array|mixed
     */
    protected function addCustomFields($entry, $fields, $mapping)
    {
        $values = [];
        foreach ($fields as $field) {
            $fieldName = 'field_id_' . $field->field_id;
            $data = $entry->$fieldName;

            $values[$field->field_name] = $this->getData($entry, $field, $data);
        }

        $values = $this->mapData($mapping, $values);

        return $values;
    }

    /**
     * Runs all integrations
     *
     * @param $objects
     * @param $entry
     * @return mixed
     */
    protected function runIntegrations($objects, $entry)
    {
        foreach ($this->settings['integrations'] as $integrationModel) {
            /** @var BaseIntegration $integration */
            $integration = new $integrationModel($entry, $objects, $this->settings);
            $objects = $integration->run();
        }

        foreach ($this->config['userIntegrations'] as $integrationModel) {
            require_once __DIR__ . '/../user/integrations/' . $integrationModel . '.php';
            $integration = new $integrationModel($entry, $objects);
            $objects = $integration->run();
        }

        return $objects;
    }

    /**
     * Saves a record and indexing object to the database
     *
     * @param $objects
     * @param $index
     * @return bool
     */
    protected function saveObjects($objects, $index)
    {
        foreach ($objects as $object) {
            $record = ee('Model')
                ->make('ferret:FerretRecord', [
                    'index' => $index,
                    'entry_id' => $object['entry_id'],
                    'order' => isset($object['order']) ? $object['order'] : null,
                ]);

            if (!$record->save()) {
                return false;
            }

            $object['objectID'] = is_null($record->order) ? $object['objectID'] : $object['objectID'] . '-' . $record->order;

            $model = ee('Model')
                ->make('ferret:FerretObject', [
                    'index' => $index,
                    'entry_id' => $object['entry_id'],
                    'mapping' => json_encode($object),
                ]);

            if (!$model->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sets the URL of an entry object
     *
     * @param $entry
     * @param $index
     * @return mixed|null
     */
    protected function setUrl($entry, $index)
    {
        $url = null;
        if (isset($index->paths[$entry->channel_id])) {
            $siteUrl = rtrim(ee()->config->item('site_url'), '/');

            $url = $index->paths[$entry->channel_id];
            $url = str_replace('{base_url}', $siteUrl, $url);
            $url = str_replace('{url_title}', $entry->url_title, $url);
        }

        return $url;
    }

    /**
     * Adds categories and custom category data
     *
     * @param $entry
     * @param $index
     * @return array
     */
    protected function getCategories($entry, $index)
    {
        $indexCategories = $index->categories;
        $categories = [];
        foreach ($entry->Categories as $category) {
            $fields = $category->getCustomFields();
            $categoryData = [
                'title' => $category->cat_name,
                'image' => $category->cat_image,
            ];
            foreach ($fields as $field) {
                if (isset($indexCategories[$entry->channel_id][$category->group_id][$field->getItem('field_name')])) {
                    $categoryData[$field->getItem('field_name')] = $field->getData();
                }
            }

            $groupName = strtolower(preg_replace('/\s+/', '_', $category->CategoryGroup->group_name));

            $categories[$groupName][] = array_filter($categoryData);
        }

        return $categories;
    }

    /**
     * Returns the properly formatted field data
     *
     * @param $entry
     * @param $field
     * @param $input
     * @return mixed
     */
    protected function getData($entry, $field, $input)
    {
        $field = $field->getField();
        $field->setContentId($entry->getId());

        ee()->load->library('FieldData', ['settings' => $this->settings, 'config' => $this->config]);

        return ee()->fielddata->getData($entry, $field, $input);
    }

    /**
     * Reassigns data to any custom defined mapping
     *
     * @param $mapping
     * @param $values
     * @return array|mixed
     */
    protected function mapData($mapping, $values)
    {
        $object = [];
        foreach ($mapping as $fieldName => $mapName) {
            if (is_array($mapName) && isset($values[$fieldName])) {
                $data = $this->mapData($mapName, $values[$fieldName]);

                foreach ($data as $key => $value) {
                    $object = $this->setData($object, $key, $value);
                }
            } elseif (isset($values[$fieldName])) {
                $object = $this->setData($object, $mapName, $values[$fieldName]);
            }
        }

        return $object;
    }

    /**
     * Adds or merges data
     *
     * @param $object
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function setData($object, $key, $value)
    {
        if (isset($object[$key])) {
            $object[$key] = is_array($value) ? array_values(array_unique(array_merge($object[$key], $value))) : $object[$key] . ' ' . $value;
        } else {
            $object[$key] = is_array($value) ? array_values(array_unique($value)) : $value;
        }

        return $object;
    }

    /**
     * Breaks up an object into chunks where no individual attribute
     * is longer than self::CHUNK_LIMIT
     *
     * @param $object
     * @return array
     */
    protected function chunkObject($object)
    {
        $objects = [];
        $order = 1;
        foreach ($object as $field => $data) {
            if (is_string($data) && strpos($data, '<p>') !== false) {
                $chunks = $this->breakChunks(explode('<p>', $data));

                $index = 0;
                while ($index < count($chunks)) {
                    $value = $chunks[$index];

                    while (isset($chunks[$index + 1]) && strlen($value . ' ' . $chunks[$index + 1]) <= self::CHUNK_LIMIT) {
                        $value = $value . ' ' . $chunks[++$index];
                    }

                    $index++;

                    if (isset($objects[$order])) {
                        $objects[$order][$field] = $value;
                    } else {
                        $objects[$order] = $object;
                        $objects[$order][$field] = $value;
                        $objects[$order]['order'] = $order;
                    }

                    $order++;
                }
            }
        }

        return $objects ?: [$object];
    }

    /**
     * Ensures no single chunk is longer than $this->chunk_limit
     *
     * @param $input
     * @return array
     */
    protected function breakChunks($input)
    {
        $return = [];
        foreach ($input as $chunk) {
            if ($chunk = trim($chunk)) {
                while (strlen($chunk) > self::CHUNK_LIMIT) {
                    $string = substr($chunk, 0, self::CHUNK_LIMIT);

                    if (($breakPoint = strrpos($string, '. ')) !== false) {
                        $return[] = trim(substr($chunk, 0, $breakPoint + 1));
                        $chunk = substr($chunk, $breakPoint + 1);
                    } elseif (($breakPoint = strrpos($string, ' ')) !== false) {
                        $return[] = trim(substr($chunk, 0, $breakPoint + 1));
                        $chunk = substr($chunk, $breakPoint + 1);
                    } else {
                        $return[] = trim(substr($chunk, 0, self::CHUNK_LIMIT));
                        $chunk = substr($chunk, self::CHUNK_LIMIT + 1);
                    }
                }

                $return[] = trim($chunk);
            }
        }

        return array_values(array_filter($return));
    }
}
