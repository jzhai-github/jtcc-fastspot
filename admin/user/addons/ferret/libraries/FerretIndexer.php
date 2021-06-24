<?php

use fostermade\ferret\engines\EngineInterface;

class FerretIndexer
{
    /** @var EngineInterface */
    protected $indexer;
    protected $settings = [];
    protected $limit = 500;

    protected $add = [];
    protected $delete = [];

    /**
     * FerretIndexer constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->settings = $params['settings'];

        if (isset($this->settings['engine']) && !empty($this->settings['engine'])) {
            $engine = 'fostermade\ferret\engines\\' . $this->settings['engine'];
            $this->indexer = new $engine($this->settings);
        }
    }

    /**
     * Adds all FerretObjects to index
     */
    public function index()
    {
        $ferretObjects = ee('Model')
            ->get('ferret:FerretObject')
            ->fields('index', 'entry_id', 'mapping')
            ->limit($this->limit)
            ->all();

        while ($ferretObjects->count()) {
            $add = [];
            $delete = [];
            foreach ($ferretObjects as $object) {
                $add[$object->index][] = json_decode($object->mapping, true);
                $delete[$object->index][] = $object->entry_id;
            }

            foreach ($delete as $index => $entryIds) {
                $this->delete($index, $entryIds);
            }

            foreach ($add as $index => $objects) {
                $objects = $this->sitePagesUrls($objects);

                $this->indexer->index($index, $objects);

                unset($add[$index]);
            }

            $ferretObjects->delete();

            $ferretObjects = ee('Model')
                ->get('ferret:FerretObject')
                ->fields('index', 'entry_id', 'mapping')
                ->limit($this->limit)
                ->all();
        }
    }

    /**
     * Deletes an entry from the index
     *
     * @param $entryId
     * @throws Exception
     * @return bool
     */
    public function delete($entryId)
    {
        $records = ee('Model')
            ->get('ferret:FerretRecord')
            ->filter('entry_id', $entryId)
            ->all();

        $delete = [];
        foreach ($records as $record) {
            $delete[$record->index][] = [
                'objectID' => $record->objectID,
                'objectAction' => 'deleteObject',
            ];
        }

        $records->delete();

        foreach ($delete as $index => $objects) {
            $this->indexer->delete($index, $objects);
        }

        return true;
    }

    /**
     * Clears the index
     *
     * @param $indexName
     */
    public function clear($indexName)
    {
        $this->indexer->clearIndex($indexName);

        ee()->db->where('index', $indexName);
        ee()->db->delete('exp_ferret_objects');

        ee()->db->where('index', $indexName);
        ee()->db->delete('exp_ferret_records');
    }

    /**
     * @param $objects
     * @return mixed
     */
    protected function sitePagesUrls($objects)
    {
        $siteUrl = rtrim(ee()->config->item('site_url'), '/');

        $sitePages = ee()->db
            ->select('site_pages')
            ->from('sites')
            ->where('site_id', ee()->config->item('site_id'))
            ->limit(1)
            ->get();

        $sitePages = unserialize(base64_decode($sitePages->row('site_pages')));
        $uris = $sitePages[ee()->config->item('site_id')]['uris'];

        foreach ($objects as &$object) {
            if (!$object['url']) {
                $object['url'] = isset($uris[$object['entry_id']]) ? $siteUrl . $uris[$object['entry_id']] : null;
            }
        }

        return $objects;
    }
}
