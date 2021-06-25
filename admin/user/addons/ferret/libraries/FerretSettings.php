<?php

class FerretSettings
{
    protected $settings;
    protected $config;
    protected $indexName;

    /**
     * FerretSettings constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->settings = $params['settings'];
        $this->config = $params['config'];
        $this->indexName = ee()->uri->segment(6);
    }

    /**
     * @return bool
     */
    public function addRemove()
    {
        if (!$_POST) {
            return false;
        }

        if ($name = strtolower(ee()->input->post('name'))) {
            $index = ee('Model')->make('ferret:FerretIndex', compact('name'));

            $index->save();
        }

        if ($deletions = ee()->input->post('delete')) {
            $indexes = ee('Model')
                ->get('ferret:FerretIndex')
                ->filter('name', 'IN', $deletions)
                ->all();

            foreach ($indexes as $index) {
                $index->delete();
            }
        }

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->addToBody(lang('settings_saved'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret/add_remove'));

        return true;
    }

    /**
     * @return bool
     */
    public function build()
    {
        if (!$indexName = ee()->input->post('index_name')) {
            return false;
        }

        ee()->load->library('ferretIndexer', ['settings' => $this->settings]);
        ee()->ferretindexer->clear($indexName);

        ee()->load->library('FerretBuilder', [
            'settings' => $this->settings,
            'config' => $this->config,
        ]);

        ee()->ferretbuilder->index($indexName);
        ee()->ferretindexer->index();

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->addToBody(lang('build_working'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret/indexes/' . $this->indexName . '/build'));

        return true;
    }

    /**
     * @return bool
     */
    public function categories()
    {
        if (!ee()->input->post('save')) {
            return false;
        }

        $index = ee('Model')
            ->get('ferret:FerretIndex')
            ->filter('name', $this->indexName)
            ->first();

        $categoryFields = [];
        foreach ($index->fields as $channelId => $fields) {
            foreach (ee()->input->post($channelId) as $groupId => $categories) {
                foreach ($categories as $category) {
                    $categoryFields[$channelId][$groupId][$category] = $category;
                }
            }
        }

        $index->categories = array_filter($categoryFields);

        $index->save();

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->addToBody(lang('settings_saved'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret/indexes/' . $this->indexName . '/categories'));

        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        if (!ee()->input->post('index_name')) {
            return false;
        }

        if ($indexName = ee()->input->post('index_name')) {
            ee()->load->library('FerretIndexer', ['settings' => $this->settings]);
            ee()->ferretindexer->clear($indexName);
        }

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->addToBody(lang('clear_working'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret/indexes/' . $this->indexName . '/clear'));

        return true;
    }

    /**
     * @return bool
     */
    public function fields()
    {
        if (!ee()->input->post('save')) {
            return false;
        }

        $fieldSettings = [];
        foreach (ee()->input->post('fields') as $channelId => $fields) {
            $channelFields = [];
            $fields = array_filter($fields);
            foreach ($fields as $field) {
                $fieldArray = explode('|', $field);
                switch (count($fieldArray)) {
                    case 3:
                        $grandparent = $fieldArray[0];
                        $parent = $fieldArray[1];
                        $value = $fieldArray[2];
                        $channelFields[$grandparent][$parent][$value] = $value;
                        break;
                    case 2:
                        $parent = $fieldArray[0];
                        $value = $fieldArray[1];
                        $channelFields[$parent][$value] = $value;
                        break;
                    case 1:
                        $value = $fieldArray[0];
                        $channelFields[$value] = $value;
                }
            }

            $fieldSettings[$channelId] = $channelFields;
        }

        $index = ee('Model')
            ->get('ferret:FerretIndex')
            ->filter('name', $this->indexName)
            ->limit(1)
            ->first();

        $index->fields = array_filter($fieldSettings);

        $index->save();

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->addToBody(lang('settings_saved'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret/indexes/' . $this->indexName . '/fields'));

        return true;
    }

    /**
     * @return bool
     */
    public function index()
    {
        if (!ee()->input->post('engine')) {
            return false;
        }

        $credentials = ee()->input->post('credentials');
        foreach ($credentials as $key => $credential) {
            if (array_filter($credential) == null) {
                unset($credentials[$key]);
            }
        }

        $params = [
            'engine' => ee()->input->post('engine'),
            'credentials' => $credentials,
            'strip_ee_tags' => ee()->input->post('strip_ee_tags'),
            'integrations' => ee()->input->post('integrations'),
        ];

        $this->save($params);

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret'));

        return true;
    }

    /**
     * @return bool
     */
    public function mapping()
    {
        if (!ee()->input->post('save')) {
            return false;
        }

        $index = ee('Model')
            ->get('ferret:FerretIndex')
            ->filter('name', $this->indexName)
            ->first();

        $indexFields = $index->fields;

        $params = [];
        foreach ($indexFields as $channelId => $fields) {
            $mapping = ee()->input->post($channelId);

            foreach ($fields as $key => $value) {
                $params[$channelId][$key] = $mapping[$key] ?: $value;
            }
        }

        $index->fields = $params;

        $index->save();

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->addToBody(lang('settings_saved'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret/indexes/' . $this->indexName . '/mapping'));

        return true;
    }

    /**
     * @return bool
     */
    public function paths()
    {
        if (!ee()->input->post('save')) {
            return false;
        }

        $index = ee('Model')
            ->get('ferret:FerretIndex')
            ->filter('name', $this->indexName)
            ->first();

        $channels = ee('Model')->get('Channel')->all();

        $paths = [];
        foreach ($channels as $channel) {
            $paths[$channel->getId()] = ee()->input->post($channel->getId());
        }

        $index->paths = array_filter($paths);

        $index->save();

        ee('CP/Alert')
            ->makeInline('shared-form')
            ->asSuccess()
            ->addToBody(lang('settings_saved'))
            ->defer();

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/ferret/indexes/' . $this->indexName . '/paths'));

        return true;
    }

    /**
     * Persists the new settings to the database
     *
     * @param $params
     * @return mixed
     */
    public function save($params = [])
    {
        $this->settings = array_merge($this->settings, $params);

        foreach ($this->settings as $key => &$setting) {
            if (is_array($setting) && $key !== 'indexes') {
                $setting = array_filter($setting);
            }
        }

        if ($this->settings['integrations'] == false) {
            $this->settings['integrations'] = [];
        }

        $success = ee()->db->update('exp_extensions', ['settings' => serialize($this->settings)], ['class' => 'Ferret_ext']);

        if ($success) {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asSuccess()
                ->addToBody(lang('settings_saved'))
                ->defer();
        } else {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->addToBody(lang('settings_error'))
                ->defer();
        }

        return $success;
    }
}
