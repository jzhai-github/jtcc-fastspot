<?php

use EllisLab\ExpressionEngine\Model\Content\FieldFacade;

class FieldData
{
    public $settings;
    public $config;

    /**
     * FieldData constructor.
     *
     * @param $params
     */
    public function __construct($params)
    {
        $this->settings = $params['settings'];
        $this->config = $params['config'];
    }

    /**
     * @param $entry
     * @param $field
     * @param $data
     * @return array|float|int|null
     */
    public function getData($entry, $field, $data)
    {
        ee()->load->library('logger');

        $output = null;
        switch ($field->getType()) {
            case 'assets':
                $output = $this->assets($field);
                break;
            case 'checkboxes':
            case 'multi_select':
            case 'radio':
            case 'select':
                $output = $this->valueLabel($field, $data);
                break;
            case 'date':
            case 'number':
                $output = $this->number($data);
                break;
            case 'fieldpack_checkboxes':
            case 'fieldpack_dropdown':
            case 'fieldpack_multiselect':
            case 'fieldpack_pill':
            case 'fieldpack_radio_buttons':
                $output = $this->valueLabel($field, $data, true);
                break;
            case 'fieldpack_list':
                $output = $this->fieldpackList($data);
                break;
            case 'email_address':
            case 'fieldpack_switch':
            case 'text':
            case 'toggle':
            case 'url':
                $output = $this->simple($data);
                break;
            case 'duration':
                $output = $this->duration($field, $data);
                break;
            case 'file':
                $output = $this->file($data);
                break;
            case 'file_grid':
                $output = $this->fileGrid($entry, $field);
                break;
            case 'grid':
                $output = $this->grid($entry, $field);
                break;
            case 'fluid_field':
                $output = $this->fluidField($entry, $field);
                break;
            case 'matrix':
                $output = $this->matrix($entry, $field);
                break;
            case 'relationship':
                $output = $this->relationship($field);
                break;
            case 'rte':
            case 'textarea':
            case 'wygwam':
                $output = $this->richText($data);
                break;
            case 'structure':
                $output = $this->structure($data);
                break;
            default:
                ee()->logger->developer('Unsupported Field Type:' . $field->getType());
        }

        return $output;
    }

    /**
     * Field Methods
     *
     * Used to extract properly formatted data for indexing
     */

    /**
     * @param $field
     * @return array|null
     */
    protected function assets($field)
    {
        $query = ee()->db
            ->select('file_id')
            ->from('assets_selections')
            ->where('entry_id', $field->getContentId());

        if ($matrixCol = $field->getItem('parent_col_id')) {
            $query->where('col_id', $matrixCol)
                ->where('row_id', $field->getItem('parent_row_id'))
                ->where('field_id', $field->getItem('field_id'));
        } elseif ($gridCol = $field->getItem('grid_col_id')) {
            $query->where('col_id', $gridCol)
                ->where('row_id', $field->getItem('grid_row_id'))
                ->where('field_id', $field->getItem('grid_field_id'));
        } else {
            $query->where('field_id', $field->getItem('field_id'));
        }

        $result = $query->get();

        if (ee()->load->is_loaded('assets_lib') == false) {
            ee()->load->add_package_path(PATH_THIRD . 'assets/');
            ee()->load->library('assets_lib');
        }

        if ($result->num_rows == 0) {
            return null;
        }

        $return = [];
        foreach ($result->result_array() as $row) {
            $return[] = ee()->assets_lib->get_file_url($row['file_id']);
        }

        return $return;
    }

    /**
     * @param $field
     * @param $data
     * @return string|null
     */
    protected function duration($field, $data)
    {
        if (!$data || !isset($field->getItem('field_settings')['units'])) {
            return null;
        }

        $units = $field->getItem('field_settings')['units'];
        $data = explode(':', $data);
        switch (count($data)) {
            case 3:
                $data = ($data[0] * 60 * 60) + ($data[1] * 60) + $data[2];
                break;
            case 2:
                $data = ($data[0] * 60) + $data[1];
                break;
            default:
                $data = $data[0];
        }

        return $data . ' ' . $units;
    }

    /**
     * @param $data
     * @return array|null
     */
    protected function file($data)
    {
        $filename = substr($data, strpos($data, '}') + 1);
        $start = strpos($data, '_') + 1;
        $end = strpos($data, '}') - $start;
        $directoryId = substr($data, $start, $end);

        $directory = ee('Model')
            ->get('UploadDestination')
            ->filter('id', $directoryId)
            ->first();

        if ($directory) {
            $directory = $directory->url;
        }

        $return = [$directory . $filename];

        return $return ?: null;
    }

    /**
     * @param $data
     * @return array|null
     */
    protected function fieldpackList($data)
    {
        $data = array_filter(explode(PHP_EOL, $data));

        return $data ?: null;
    }

    /**
     * @param $entry
     * @param $field
     * @return array|null
     */
    protected function fluidField($entry, $field)
    {
        $fluidFields = ee('Model')
            ->get('fluid_field:FluidField')
            ->filter('entry_id', $field->getContentId())
            ->filter('fluid_field_id', $field->getId())
            ->all();

        $object = [];
        foreach ($fluidFields as $fluidField) {
            $fluidField = $fluidField->getField();
            $label = $fluidField->getItem('field_name');

            $data = $this->getData($entry, $fluidField, $fluidField->getData());

            if (isset($object[$label])) {
                if ($fluidField->getType() === 'grid') {
                    $object[$label] = $this->mergeGrids($object[$label], $data);
                } else {
                    $object[$label] = is_array($data) ? array_merge($object[$label], $data) : $object[$label] . ' ' . $data;
                }
            } else {
                $object[$label] = $data;
            }
        }

        return $object ?: null;
    }

    /**
     * @param $entry
     * @param $field
     * @return array|null
     */
    protected function fileGrid($entry, $field)
    {
        $gridData = $this->getGridData($entry->entry_id, $field);
        $gridFields = $this->createGridFieldFacades($entry->entry_id, $field, $gridData);

        $object = [];
        foreach ($gridFields as $gridField) {
            $object[] = $this->getData($entry, $gridField, $gridField->getData())[0];
        }

        return $object ?: null;
    }

    /**
     * @param $entry
     * @param $field
     * @return array|null
     */
    protected function grid($entry, $field)
    {
        $gridData = $this->getGridData($entry->entry_id, $field);
        $gridFields = $this->createGridFieldFacades($entry->entry_id, $field, $gridData);

        $object = [];
        foreach ($gridFields as $gridField) {
            $label = $gridField->getItem('field_name');
            $data = $this->getData($entry, $gridField, $gridField->getData());

            if (!$data) {
                continue;
            }

            if (isset($object[$label])) {
                $object[$label] = is_array($data) ? array_merge($object[$label], $data) : $object[$label] . ' ' . $data;
            } else {
                $object[$label] = $data;
            }
        }

        return $object ?: null;
    }

    /**
     * @param $entry
     * @param $field
     * @return array|null
     */
    protected function matrix($entry, $field)
    {
        $matrixLabel = $field->getItem('field_name');
        $matrixData = $this->getMatrixData($field);
        $matrixFields = $this->createMatrixFieldFacades($matrixData, $field);

        $object = [];
        foreach ($matrixFields as $matrixField) {
            if (!$label = $matrixField->getItem('field_name')) {
                continue;
            }

            $data = $this->getData($entry, $matrixField, $matrixField->getData());

            if (in_array($matrixField->getType(), ['rte', 'wygwam'])) {
                $data = strip_tags($data);
            }

            if (!$data) {
                continue;
            }

            if (isset($object[$label])) {
                $object[$matrixLabel][$label] = is_array($data) ? array_merge($object[$matrixLabel][$label], $data) : $object[$matrixLabel][$label] . ' ' . $data;
            } else {
                $object[$matrixLabel][$label] = $data;
            }
        }

        return $object ?: null;
    }

    /**
     * @param $data
     * @return float|int
     */
    protected function number($data)
    {
        if (is_float($data)) {
            $data = (float)$data;
        } elseif (is_numeric($data)) {
            $data = (int)$data;
        }

        return $data;
    }

    /**
     * @param $field
     * @return array|null
     */
    protected function playa($field)
    {
        $wheres = [
            'parent_entry_id' => $field->getContentId(),
            'parent_field_id' => $field->getId(),
            'parent_col_id' => $field->getItem('parent_col_id'),
            'parent_row_id' => $field->getItem('parent_row_id'),
        ];

        $results = ee()->db->select('child_entry_id')
            ->from('playa_relationships')
            ->where($wheres)
            ->get();

        $entry_ids = [0];
        foreach ($results->result_array() as $row) {
            $entry_ids[] = $row['child_entry_id'];
        }

        $entries = ee('Model')
            ->get('ChannelEntry')
            ->fields('title')
            ->filter('entry_id', 'IN', $entry_ids)
            ->order('title')
            ->all();

        $data = [];
        foreach ($entries as $entry) {
            $data[] = $entry->title;
        }

        return $data ?: null;
    }

    /**
     * @param $field
     * @return array|null
     */
    protected function relationship($field)
    {
        $wheres = [
            'parent_id' => $field->getContentId(),
            'field_id' => $field->getId(),
            'fluid_field_data_id' => $field->getItem('fluid_field_data_id') ?: 0,
            'grid_field_id' => $field->getItem('grid_field_id') ?: 0,
            'grid_col_id' => $field->getItem('grid_col_id') ?: 0,
            'grid_row_id' => $field->getItem('grid_row_id') ?: 0,
        ];

        $results = ee()->db->select('child_id')
            ->from('exp_relationships')
            ->where($wheres)
            ->get();

        if (!$results->num_rows) {
            return null;
        }

        $entryIds = [];
        foreach ($results->result_array() as $row) {
            $entryIds[] = $row['child_id'];
        }

        $entries = ee('Model')
            ->get('ChannelEntry')
            ->filter('entry_id', 'IN', $entryIds)
            ->order('title')
            ->all();

        if (!$entries->count()) {
            return null;
        }

        $data = [];
        foreach ($entries as $entry) {
            $data[] = [
                'entry_id' => $entry->entry_id,
                'title' => $entry->title,
            ];
        }

        return $data;
    }

    /**
     * @param $data
     * @return string|null
     */
    protected function richText($data)
    {
        $tags = isset($this->config['chunkTags']) ? implode('', $this->config['chunkTags']) : '';
        $data = strip_tags($data, $tags);
        $data = preg_replace("/[\r\n]+/", '', $data);
        $data = trim($data);
        $data = html_entity_decode($data);
        $data = str_replace('</p>', '', $data);
        $data = preg_replace("/[\r\n]+/", "\n", $data);
        $data = preg_replace("!\s+!", ' ', $data);

        $data = $this->cleanTags($data);

        if ($this->settings['strip_ee_tags']) {
            $data = preg_replace("/\{\S+\}/", '', $data);
        }

        $data = str_replace('&nbsp;', ' ', htmlentities($data));
        $data = html_entity_decode($data);

        return $data ?: null;
    }

    /**
     * @param $data
     * @return string|null
     */
    protected function simple($data)
    {
        return $data ?: null;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function structure($data)
    {
        $entry = ee('Model')
            ->get('ChannelEntry')
            ->filter('entry_id', $data)
            ->first();

        return $entry->title;
    }

    /**
     * @param $field
     * @param $data
     * @param $fieldpack
     * @return array|null
     */
    protected function valueLabel($field, $data, $fieldpack = false)
    {
        if (!$data) {
            return null;
        }

        if ($fieldpack) {
            $fieldSettings['value_label_pairs'] = $field->getItem('field_settings')['options'];
            $data = array_filter(explode(PHP_EOL, $data));
        } else {
            $fieldSettings = $field->getItem('field_settings');
            $data = is_array($data) ? $data : explode('|', $data);
        }

        if (isset($fieldSettings['value_label_pairs'])) {
            foreach ($data as &$datum) {
                $datum = $fieldSettings['value_label_pairs'][$datum];
            }
        }

        return $data;
    }

    /**
     * Helper Methods
     *
     * Used to help with ExpressionEngine wizardry
     */

    /**
     * @param $data
     * @return mixed
     */
    public static function cleanTags($data)
    {
        while (($start = strpos($data, '<p ')) !== false) {
            $end = strpos($data, '>', $start) + 1;
            $pTag = substr($data, $start, $end - $start);

            $data = str_replace($pTag, '<p>', $data);
        }

        return $data;
    }

    /**
     * @param $entryId
     * @param $field
     * @param $gridData
     * @return array
     */
    public static function createGridFieldFacades($entryId, $field, $gridData)
    {
        $fields = [];
        foreach ($gridData['grid_rows'] as $row) {
            $rowId = $row['row_id'];
            foreach ($row as $key => $data) {
                $colId = strpos($key, 'col_id_') !== false ? substr($key, 7) : false;

                if ($colId) {
                    $gridField = $gridData['grid_fields'][$colId];
                    $metadata = [
                        'field_id' => $gridField['col_id'],
                        'field_name' => $gridField['col_name'],
                        'field_label' => $gridField['col_label'],
                        'field_type' => $gridField['col_type'],
                        'grid_col_id' => $gridField['col_id'],
                        'grid_field_id' => $gridField['field_id'],
                        'grid_row_id' => $rowId,
                        'field_settings' => $gridField['col_settings'],
                    ];

                    $facade = new FieldFacade($gridField['col_id'], $metadata);
                    $facade->setData($data);
                    $facade->setContentId($entryId);
                    $facade->setContentType($field->getContentType());

                    $fields[] = $facade;
                }
            }
        }

        return $fields;
    }

    /**
     * @param $matrix
     * @param $field
     * @return array
     */
    public static function createMatrixFieldFacades($matrix, $field)
    {
        $fields = [];
        foreach ($matrix['data'] as $rowData) {
            foreach ($matrix['columns'] as $matrixColumn) {
                $fieldSettings = unserialize(base64_decode($matrixColumn['col_settings']));
                $metadata = [
                    'field_id' => $matrixColumn['field_id'],
                    'field_name' => $matrixColumn['col_name'],
                    'field_label' => $matrixColumn['col_label'],
                    'field_type' => $matrixColumn['col_type'],
                    'parent_col_id' => $matrixColumn['col_id'],
                    'parent_row_id' => $rowData['row_id'],
                    'field_settings' => $fieldSettings,
                ];
                $data = $rowData['col_id_' . $matrixColumn['col_id']];

                $facade = new FieldFacade($matrixColumn['field_id'], $metadata);
                $facade->setData($data);
                $facade->setContentId($field->getContentId());
                $facade->setContentType($field->getContentType());

                $fields[] = $facade;
            }
        }

        return $fields;
    }

    /**
     * @param $entryId
     * @param $field
     * @return array
     */
    public static function getGridData($entryId, $field)
    {
        $entryId = $field->getContentId();
        $fieldId = $field->getId();
        $contentType = $field->getContentType();
        $fieldFieldDataId = $field->getItem('fluid_field_data_id') ?: 0;

        ee()->load->model('grid_model');
        $grid = new Grid_model();
        $gridData = [
            'grid_rows' => $grid->get_entry($entryId, $fieldId, $contentType, $fieldFieldDataId),
            'grid_fields' => $grid->get_columns_for_field($fieldId, $contentType),
        ];

        return $gridData;
    }

    /**
     * @param $field
     * @return mixed
     */
    public static function getMatrixData($field)
    {
        $matrix['data'] = ee()->db->select('*')
            ->from('matrix_data')
            ->where('entry_id', $field->getContentId())
            ->where('field_id', $field->getId())
            ->get()
            ->result_array();

        $matrix['columns'] = ee()->db->select('*')
            ->from('matrix_cols')
            ->where('field_id', $field->getId())
            ->get()
            ->result_array();

        return $matrix;
    }

    /**
     * @param $grid1
     * @param $grid2
     * @return mixed
     */
    public static function mergeGrids($grid1, $grid2)
    {
        foreach ($grid2 as $key => $value) {
            if (isset($grid1[$key])) {
                $grid1[$key] = is_array($value) ? array_merge($grid1[$key], $value) : $grid1[$key] . ' ' . $value;
            } else {
                $grid1[$key] = $value;
            }
        }

        return $grid1;
    }
}
