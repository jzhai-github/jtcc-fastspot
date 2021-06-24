<?php

class FerretHelper
{
    /**
     * Retrieves settings from the database
     *
     * @return array
     */
    public static function getSettings()
    {
        $defaults = [
            'engine' => '',
            'strip_ee_tags' => 'n',
            'integrations' => [],
        ];

        $result = ee()->db
            ->select('settings')
            ->from('exp_extensions')
            ->where('class', 'Ferret_ext')
            ->limit(1)
            ->get();

        $settings = unserialize($result->row('settings'));

        $settings = array_merge($defaults, $settings);

        return $settings;
    }
}
