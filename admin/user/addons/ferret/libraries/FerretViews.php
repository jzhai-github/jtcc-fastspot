<?php

class FerretViews
{
    protected $indexes;
    protected $settings;
    protected $config;
    protected $indexName;

    /**
     * FerretViews constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->indexes = ee('Model')->get('ferret:FerretIndex')->all();
        $this->settings = $params['settings'];
        $this->config = $params['config'];
        $this->indexName = ee()->uri->segment(6);
    }

    /* --- View Methods --- */

    /**
     * @return array
     */
    public function addRemove()
    {
        $indexes = [];
        foreach ($this->indexes as $index) {
            $indexes[$index->name] = $index->name;
        }

        $settings['indexes'] = [
            [
                'title' => 'create_index',
                'desc' => 'create_index_desc',
                'fields' => [
                    'name' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ];

        if ($indexes) {
            $settings['indexes'][] = [
                'title' => 'delete_indexes',
                'desc' => 'delete_indexes_desc',
                'fields' => [
                    'delete' => [
                        'type' => 'checkbox',
                        'choices' => $indexes,
                    ],
                ],
            ];
        }

        return $settings;
    }

    /**
     * @return array
     */
    public function build()
    {
        if (!ee('CP/Alert')->getAllInlines()) {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->addToBody(lang('build_index'))
                ->now();
        }

        $settings = [[['fields' => ['index_name' => ['type' => 'hidden', 'value' => ee()->uri->segment(6)]]]]];

        return $settings;
    }

    /**
     * @return array
     */
    public function categories()
    {
        $settings = [];
        $index = ee('Model')
->get('ferret:FerretIndex')
            ->filter('name', $this->indexName)
            ->first();

        $channelIds = [];
        foreach ($index->fields as $channel_id => $fields) {
            $channelIds[] = $channel_id;
        }

        $channels = ee('Model')
            ->get('Channel')
            ->filter('channel_id', 'IN', $channelIds)
            ->all();

        foreach ($channels as $channel) {
            foreach ($channel->CategoryGroups as $categoryGroup) {
                $choices = [];
                foreach ($categoryGroup->CategoryFields as $field) {
                    $choices[$field->field_name] = $field->field_label;
                }

                if (isset($index->categories[$channel->channel_id][$categoryGroup->group_id])) {
                    $value = $index->categories[$channel->channel_id][$categoryGroup->group_id];
                } else {
                    $value = [];
                }

                $array = [
                    'title' => $categoryGroup->group_name,
                    'fields' => [
                        $channel->channel_id . '[' . $categoryGroup->group_id . ']' => [
                            'type' => 'checkbox',
                            'choices' => $choices,
                            'value' => $value,
                        ],
                    ],
                ];

                if ($choices) {
                    $settings[$channel->channel_title][] = $array;
                }
            }
        }

        $settings[] = [[
            'fields' => [
                'save' => [
                    'value' => true,
                    'type' => 'hidden',
                ],
            ],
        ]];

        return $settings;
    }

    /**
     * @return array
     */
    public function clear()
    {
        if (!ee('CP/Alert')->getAllInlines()) {
            ee('CP/Alert')
                ->makeInline('shared-form')
                ->asIssue()
                ->addToBody(lang('clear_index'))
                ->now();
        }

        $settings = [[['fields' => ['index_name' => ['type' => 'hidden', 'value' => ee()->uri->segment(6)]]]]];

        return $settings;
    }

    /**
     * @return array
     */
    public function fields()
    {
        $channels = ee('Model')->get('Channel')->order('channel_name')->all();
        $channelFields = [];
        foreach ($channels as $channel) {
            $fields = [];
            foreach ($channel->FieldGroups as $fieldGroup) {
                foreach ($fieldGroup->ChannelFields as $field) {
                    $fields[] = $field;
                }
            }

            foreach ($channel->CustomFields as $field) {
                $fields[] = $field;
            }

            $channelFields[$channel->getId()] = ['title' => $channel->channel_title];
            $fieldLabels = [];
            foreach ($fields as $field) {
                switch ($field->field_type) {
                    case 'fluid_field':
                        $data = $this->addFluidField($field);
                        break;
                    case 'grid':
                        $data = $this->addGridField($field);
                        break;
                    default:
                        $data = [$field->field_name => $field->field_label];
                }

                $fieldLabels = array_merge($fieldLabels, $data);
            }
            $channelFields[$channel->getId()]['fields'] = $fieldLabels;
        }

        $ferretIndex = ee('Model')->get('ferret:FerretIndex')->filter('name', '==', $this->indexName)->first();
        $indexFields = $ferretIndex->fields;

        $value = [];
        if ($indexFields) {
            foreach ($indexFields as $channelId => $fields) {
                foreach ($fields as $field => $mapping) {
                    if (is_array($mapping)) {
                        foreach ($mapping as $childField => $childMapping) {
                            if (is_array($childMapping)) {
                                foreach ($childMapping as $grandchildField => $grandchildMapping) {
                                    $value[$channelId][] = $field . '|' . $childField . '|' . $grandchildField;
                                }
                            } else {
                                $value[$channelId][] = $field . '|' . $childField;
                            }
                        }
                    } else {
                        $value[$channelId][] = $field;
                    }
                }
            }
        }

        foreach ($value as $channelId => &$fields) {
            if (is_int($channelId)) {
                foreach ($fields as $key => $field) {
                    if (!array_key_exists($field, $channelFields[$channelId]['fields'])) {
                        unset($fields[$key]);
                    }
                }
            }
        }

        $array = [];
        foreach ($channelFields as $channelId => $channel) {
            $array['fields_for_indexing'][] = [
                'title' => $channel['title'],
                'desc' => '',
                'fields' => [
                    'fields[' . $channelId . ']' => [
                        'type' => 'checkbox',
                        'choices' => $channel['fields'],
                        'value' => isset($value[$channelId]) ? $value[$channelId] : null,
                    ],
                ],
            ];
        }

        $array[] = [[
            'fields' => [
                'save' => [
                    'value' => true,
                    'type' => 'hidden',
                ],
            ],
        ]];

        return $array;
    }

    /**
     * @return array
     */
    public function index()
    {
        $engines = array_merge(['' => 'Select an engine'], $this->config['engines']);
        $groupToggles = ['' => ''];

        foreach ($engines as $key => $engine) {
            $groupToggles[$key] = strtolower($engine) . '_credentials';
        }

        $settings = [
            'engine' => [
                'settings' => [
                    'title' => 'engine',
                    'desc' => 'engine_desc',
                    'fields' => [
                        'engine' => [
                            'type' => 'select',
                            'choices' => $engines,
                            'group_toggle' => $groupToggles,
                            'value' => $this->settings['engine'],
                            'required' => true,
                        ],
                    ],
                ],
            ],
        ];

        foreach ($engines as $key => $engine) {
            if ($key) {
                $key = '\fostermade\ferret\engines\\' . $key;
                $settings += $key::getSettingsForm($this->settings);
            }
        }

        $settings['options'] = [
            [
                'title' => 'strip_ee_tags',
                'desc' => 'strip_ee_tags_desc',
                'fields' => [
                    'strip_ee_tags' => [
                        'type' => 'yes_no',
                        'value' => $this->settings['strip_ee_tags'],
                    ],
                ],
            ],
        ];

        $settings['integrations'] = [
            [
                'title' => 'integrations',
                'desc' => 'integrations_desc',
                'fields' => [
                    'integrations' => [
                        'type' => 'checkbox',
                        'choices' => $this->config['integrations'],
                        'value' => $this->settings['integrations'],
                    ],
                ],
            ],
        ];

        return $settings;
    }

    /**
     * @return array
     */
    public function mapping()
    {
        $index = ee('Model')->get('ferret:FerretIndex')->filter('name', '==', $this->indexName)->first();
        $indexFields = $index->fields;

        $data = [];
        foreach ($indexFields as $channelId => $fields) {
            $mapping = isset($indexFields[$channelId]) ? $indexFields[$channelId] : [];
            $channel = ee('Model')
                ->get('Channel')
                ->fields('channel_title')
                ->filter('channel_id', $channelId)
                ->first();

            $data[$channel->channel_title] = [];

            foreach ($fields as $key => $field) {
                if (is_array($field)) {
                    foreach ($field as $childKey => $childField) {
                        if (is_array($childField)) {
                            foreach ($childField as $granchildKey => $grandchildField) {
                                if (isset($mapping[$key][$childKey][$granchildKey])) {
                                    $value = $mapping[$key][$childKey][$granchildKey];
                                } else {
                                    $value = $grandchildField;
                                }

                                $data[$channel->channel_title][] = [
                                    'title' => $key . ': ' . $childKey . ': ' . $granchildKey,
                                    'desc' => '',
                                    'fields' => [
                                        $channelId . '[' . $key . '][' . $childKey . '][' . $granchildKey . ']' => [
                                            'type' => 'text',
                                            'value' => $value,
                                        ],
                                    ],
                                ];
                            }
                        } else {
                            if (isset($mapping[$key][$childKey])) {
                                $value = $mapping[$key][$childKey];
                            } else {
                                $value = $childField;
                            }
                            $data[$channel->channel_title][] = [
                                'title' => $key . ': ' . $childKey,
                                'desc' => '',
                                'fields' => [
                                    $channelId . '[' . $key . '][' . $childKey . ']' => [
                                        'type' => 'text',
                                        'value' => $value,
                                    ],
                                ],
                            ];
                        }
                    }
                } else {
                    if (isset($mapping[$key])) {
                        $value = $mapping[$key];
                    } else {
                        $value = $field;
                    }

                    $data[$channel->channel_title][] = [
                        'title' => $key,
                        'desc' => '',
                        'fields' => [
                            $channelId . '[' . $key . ']' => [
                                'type' => 'text',
                                'value' => $value,
                            ],
                        ],
                    ];
                }
            }
        }

        $data[] = [[
            'fields' => [
                'save' => [
                    'value' => true,
                    'type' => 'hidden',
                ],
            ],
        ]];

        return $data;
    }

    /**
     * @return array
     */
    public function paths()
    {
        $index = ee('Model')
            ->get('ferret:FerretIndex')
            ->filter('name', $this->indexName)
            ->first();

        $indexFields = $index->fields;
        $indexPaths = $index->paths;

        if (!$keys = array_keys($indexFields)) {
            return [];
        }

        $channels = ee('Model')
            ->get('Channel')
            ->filter('channel_id', 'IN', array_keys($indexFields))
            ->order('channel_name')
            ->all();

        $array = [];
        foreach ($channels as $channel) {
            $array['Paths'][] = [
                'title' => $channel->channel_title,
                'desc' => 'paths_desc',
                'fields' => [
                    $channel->getId() => [
                        'type' => 'text',
                        'value' => isset($indexPaths[$channel->getId()]) ? $indexPaths[$channel->getId()] : '',
                    ],
                ],
            ];
        }

        $array[] = [[
            'fields' => [
                'save' => [
                    'value' => true,
                    'type' => 'hidden',
                ],
            ],
        ]];

        return $array;
    }

    /* --- Helper methods --- */

    /**
     * @param $field
     * @return array
     */
    private function addFluidField($field)
    {
        $fluidFields = array_filter($field->getProperty('field_settings')['field_channel_fields']);

        $returnData = [];
        foreach ($fluidFields as $fluidField) {
            $childField = ee('Model')
                ->get('ChannelField')
                ->filter('field_id', $fluidField)
                ->fields('field_label', 'field_name', 'field_type')
                ->first();

            if (!is_object($childField)) {
                continue;
            }

            if ($childField->field_type === 'grid') {
                $data = $this->addGridField($childField);
                foreach ($data as $key => $value) {
                    $name = $field->field_name . '|' . $key;
                    $label = $field->field_label . ': ' . $value;
                    $returnData[$name] = $label;
                }
            } else {
                $name = $field->field_name . '|' . $childField->field_name;
                $label = $field->field_label . ': ' . $childField->field_label;
                $returnData[$name] = $label;
            }
        }

        return $returnData;
    }

    /**
     * @param $field
     * @return array
     */
    private function addGridField($field)
    {
        ee()->load->model('grid_model');
        $gridField = new grid_model();
        $gridFields = $gridField->get_columns_for_field($field->getId(), 'channel');

        $returnData = [];
        foreach ($gridFields as $gridField) {
            $name = $field->field_name . '|' . $gridField['col_name'];
            $label = $field->field_label . ': ' . $gridField['col_label'];

            $returnData[$name] = $label;
        }

        return $returnData;
    }
}
