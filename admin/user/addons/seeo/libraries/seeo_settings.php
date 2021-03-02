<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use EEHarbor\Seeo\FluxCapacitor\Conduit\StaticCache;

/**
 * SEEO Settings Class
 *
 * @package         SEEO
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2018, Tom Jaeger/EEHarbor
 */

class Seeo_settings
{

    //----------------------------------------------------------------------------
    // PROPERTIES
    //----------------------------------------------------------------------------

    private $_settings = array();

    private $_default_settings = array(
        'file_manager'             => 'default',
        'enabled'                  => true,
        'divider'                  => '-',
        'title'                    => '',
        'description'              => '',
        'canonical_url'            => '',
        'keywords'                 => '',
        'author'                   => '',
        'title_prefix'             => '',
        'title_suffix'             => '',
        'og_title'                 => '',
        'og_description'           => '',
        'og_type'                  => '',
        'og_url'                   => '',
        'og_image'                 => '',
        'twitter_title'            => '',
        'twitter_description'      => '',
        'twitter_content_type'     => '',
        'twitter_image'            => '',
        'robots'                   => 'INDEX, FOLLOW',
        'sitemap_include'          => 'n',
        'sitemap_priority'         => '0.5',
        'sitemap_change_frequency' => 'weekly',
        'template'                 => '<title>{title}</title>
<meta name="description" content="{description}">
<meta name="keywords" content="{keywords}" />
<meta name="author" content="{author}" />
{if canonical_url}<link rel="canonical" href="{canonical_url}" />{/if}
<meta name="robots" content="{robots}" />

{if og_title}
<!-- Open Graph -->
<meta property="og:title" content="{og_title}" />
{if og_description}<meta property="og:description" content="{og_description}" />{/if}
{if og_type}<meta property="og:type" content="{og_type}" />{/if}
{if og_url}<meta property="og:url" content="{og_url}" />{/if}
{if og_image}<meta property="og:image" content="{og_image}" />{/if}
{/if}

{if twitter_title}
<!-- Twitter Card -->
<meta property="twitter:title" content="{twitter_title}" />
{if twitter_content_type}<meta property="twitter:card" content="{twitter_content_type}" />{/if}
{if twitter_description}<meta property="twitter:description" content="{twitter_description}" />{/if}
{if twitter_image}<meta property="twitter:image" content="{twitter_image}" />{/if}
{/if}
',
    );

    //----------------------------------------------------------------------------
    // METHODS
    //----------------------------------------------------------------------------

    /**
     * Set the settings
     * @param $settings
     */
    public function set($settings)
    {
        $this->_settings = array_merge($this->_default_settings, $settings);
    }

    /**
     * This method looks up any properties that are in this class
     * prefixed with an underscore. It returns them if they
     * exist and returns NULL if they don't.
     *
     * Magic getter
     */
    public function __get($key)
    {
        $key = '_' . $key;
        return isset($this->$key) ? $this->$key : null;
    }

    public function get($channel_id = 0, $key = null)
    {
        $cached = StaticCache::get($channel_id);

        if (empty($cached)) {
            // Load the default global settings.
            $default_global_settings = ee()->db->get_where('seeo_default_settings', array('site_id' => ee()->config->item('site_id'), 'channel_id' => 0))->row_array();

            // If the default settings are empty, import our static defaults into the DB.
            if (empty($default_global_settings)) {
                $settingData = $this->_default_settings;
                $settingData['site_id'] = ee()->config->item('site_id');
                $settingData['channel_id'] = 0;
                ee()->db->insert('seeo_default_settings', $settingData);

                $default_global_settings = $settingData;
            }

            $default_channel_settings = ee()->db->get_where('seeo_default_settings', array('site_id' => ee()->config->item('site_id'), 'channel_id' => $channel_id))->row_array();

            // If the settings are empty and we were looking for a specific channel,
            // load the global default settings instead.
            if (empty($default_channel_settings) && !empty($channel_id)) {
                $default_channel_settings = $this->_default_settings;

                // Reset the select values to "d" to denote they should fallback to globals.
                $default_channel_settings['robots'] = 'd';
                $default_channel_settings['sitemap_include'] = 'd';
                $default_channel_settings['sitemap_priority'] = 'd';
                $default_channel_settings['sitemap_change_frequency'] = 'd';
                $default_channel_settings['template'] = '';
            }


            StaticCache::set($channel_id, $default_channel_settings);
        }

        $cached = StaticCache::get($channel_id);

        if (empty($cached)) {
            $cached = array();
        }

        return is_null($key) ? $cached : (isset($cached[$key]) ? $cached[$key] : null);
    }

    //----------------------------------------------------------------------------
}
