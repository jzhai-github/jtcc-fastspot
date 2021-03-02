<?php

namespace EEHarbor\Seeo\Helpers;

class PublishTabs
{
    public $tabs;

    public function __construct()
    {
    }

    /**
     * Get all the add-on's visible fields from a channel's publish layout.
     * @param  int $channel_id The channel ID to load the publish layout for
     * @return array           Array of visible fields
     */
    public function getVisibleFields($channel_id)
    {
        $visibleFields = array();

        $this->tabs = ee()->db->get_where('layout_publish', array('channel_id' => $channel_id))->row();

        if (!empty($this->tabs)) {
            $field_layout = unserialize($this->tabs->field_layout);

            // Determine which fields are visible.
            foreach ($field_layout as $tab) {
                $hiddenFields = false;

                // If the whole tab is hidden, every field in it will be too.
                if ($tab['visible'] === false) {
                    $hiddenFields = true;
                    continue;
                }

                foreach ($tab['fields'] as $field) {
                    if (substr($field['field'], 0, 6) === 'seeo__') {
                        if ($field['visible'] === true) {
                            $visibleFields[] = $field['field'];
                        }
                    }
                }
            }
        }

        return $visibleFields;
    }
}
