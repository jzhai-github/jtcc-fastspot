<?php

include_once 'addon.setup.php';
use EEHarbor\Seeo\FluxCapacitor\Base\Tab;
use EEHarbor\Seeo\Helpers\MetaFields;

/**
 * class Seeo_tab
 *
 * @package         SEEO
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2018, Tom Jaeger/EEHarbor
 */

class Seeo_tab extends Tab
{

    private $meta_fields;
    private $default_entry_meta;

    //----------------------------------------------------------------------------
    // METHODS
    //----------------------------------------------------------------------------

    public function __construct()
    {
        // -------------------------------------
        //  Load helper, libraries and models
        // -------------------------------------
        parent::__construct();

        ee()->load->add_package_path(PATH_THIRD . 'seeo');
        ee()->load->library(array('seeo_settings'));
        ee()->lang->loadfile('seeo');

        $tab_title = ee()->config->item('seeo_tab_title');

        if ($tab_title) {
            ee()->lang->language['seeo'] = $tab_title;
        }

        $this->meta_fields = new MetaFields;
        $this->default_entry_meta = $this->meta_fields->default_entry_meta;
    }

    public function display($channel_id, $entry_id = '')
    {
        // --------------------------------------
        // Setting the empty array so I can return
        // it if SEEO isn't enabled on this
        // channel.
        // --------------------------------------
        $tab_settings = array();

        // --------------------------------------
        // Look up the settings for this channel
        // --------------------------------------
        $channel_settings = ee()->seeo_settings->get($channel_id);

        // --------------------------------------
        // If the index isn't set, then we are assuming
        // SEEO should be disabled.
        // --------------------------------------
        if (!isset($channel_settings[$channel_id])) {
            // $this->showErrorAlertOnTab();
            // return $tab_settings;
        } else {
            // --------------------------------------
            // The index is set, so let's bail out if
            // it's not explicitly set to 'y'
            // --------------------------------------

            if ($channel_settings[$channel_id]['settings']['enabled'] != 'y') {
                // $this->showErrorAlertOnTab();
                // return $tab_settings;
            }
        }

        // --------------------------------------
        // Look up existing data if there is any
        // otherwise, we'll load the default values.
        // --------------------------------------
        $entry_meta = $this->getEntryMeta($entry_id);

        $tab_settings = $this->meta_fields->getTabFields('all', $entry_meta);

        return $tab_settings;
    }

    //----------------------------------------------------------------------------

    /**
     * Validate data in Tab
     * @param $params
     * @return array|bool
     */
    public function validate($channelEntry, $values)
    {
		$validator = ee('Validation')->make(array(
			'title' => 'maxLength[70]',
			'description' => 'maxLength[155]',
		));

        return $validator->validate($values);
    }

    //----------------------------------------------------------------------------

    /**
     * Process the data in the Tab
     * @param $params
     */
    public function save($channelEntry, $params)
    {
        // --------------------------------------
        // Set up the content array for filling further down
        // --------------------------------------
        $content = array();

        $nonNullableFields = array('id', 'entry_id', 'site_id', 'channel_id');

        // --------------------------------------
        // Loop through the array of available fields,
        // check if they are set, and assign the value to
        // $content if they are.
        // --------------------------------------
        foreach (array_keys($this->default_entry_meta) as $field) {
            if (isset($params[$field]) && !is_array($params[$field])) {
                $content[$field] = $params[$field];
            } else if (!in_array($field, $nonNullableFields)) {
                $content[$field] = null;
            }
        }

        //----------------------------------------------------------------------------
        // Toggle the image processing based on Default File Browser vs. Assets
        //----------------------------------------------------------------------------

        if (ee()->seeo_settings->get($channelEntry->channel_id, 'file_manager') == 'assets') {
            $content['og_image']      = $params['og_image'][0];
            $content['twitter_image'] = $params['twitter_image'][0];
        } else {
            // --------------------------------------
            // Process the OG Image upload
            // --------------------------------------

            // if (!empty($params['og_image'][0])) {
                // $content['og_image'] = '{filedir_' . $params['og_image'][1] . '}' . $params['og_image'][0];

            if (!empty($params['og_image'])) {
                $content['og_image'] = $params['og_image'];
            } else {
                $content['og_image'] = null;
            }

            // --------------------------------------
            // Process the Twitter upload
            // --------------------------------------

            // if (!empty($params['twitter_image'][0])) {
                // $content['twitter_image'] = '{filedir_' . $params['twitter_image'][1] . '}' . $params['twitter_image'][0];
            if (!empty($params['twitter_image'])) {
                $content['twitter_image'] = $params['twitter_image'];
            } else {
                $content['twitter_image'] = null;
            }
        }

        $content['site_id']    = $channelEntry->site_id;
        $content['entry_id']   = $channelEntry->entry_id;
        $content['channel_id'] = $channelEntry->channel_id;

        $where = array(
            'entry_id' => $content['entry_id'],
            'site_id'  => $content['site_id'],
        );

        $query = ee()->db->get_where('seeo', $where);

        if ($query->num_rows()) {
            ee()->db->where($where);
            ee()->db->update('seeo', $content);
        } else {
            ee()->db->insert('seeo', $content);
        }
    }

    //----------------------------------------------------------------------------

    /**
     * Clean up on entry deletion
     * @param $params
     */
    public function delete($params)
    {
        if (!empty($params['entry_ids'])) {
            $entry_ids = $params['entry_ids'];
        } else {
            $entry_ids = $params;
        }

        if (!empty($entry_ids)) {
            foreach ($entry_ids as $i => $entry_id) {
                ee()->db->where('entry_id', $entry_id);
                ee()->db->delete('seeo');
            }
        }
    }

    // This would be a nice thing to implement if it were possible...
    private function showErrorAlertOnTab()
    {
        // ee('CP/Alert')->makeInline('mytab')
        //     ->asWarning()
        //     ->cannotClose()
        //     ->withTitle('Disabled for this channel.')
        //     ->addToBody('Enable it to update this.')
        //     ->now();
        return false;
    }

    private function getEntryMeta($entry_id = false)
    {
        if (!empty($entry_id)) {
            $entry_meta = ee()->db->from('seeo')->where(array('entry_id' => $entry_id))->get()->row_array();
        }

        if (empty($entry_meta) || !is_array($entry_meta)) {
            $entry_meta = $this->default_entry_meta;
        }

        return $entry_meta;
    }
}
