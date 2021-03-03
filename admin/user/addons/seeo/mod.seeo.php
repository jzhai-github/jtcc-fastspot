<?php

include_once 'addon.setup.php';
use EEHarbor\Seeo\Helpers\MetaTag;
use EEHarbor\Seeo\FluxCapacitor\Base\Mod;

/**
 * SEEO
 *
 * @package         SEEO
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2018, Tom Jaeger/EEHarbor
 */
class Seeo extends Mod
{
    //----------------------------------------------------------------------------
    // Parameters
    //----------------------------------------------------------------------------

    /**
     * a place to pack up all the data before sending it out
     *
     * @var
     */
    public $return_data;

    //----------------------------------------------------------------------------
    // Methods
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
    }

    public function single()
    {
        return $this->execute();
    }

    public function execute()
    {
        // --------------------------------------
        // Set up the variables
        // --------------------------------------
        $metaTag = new MetaTag;

        $metaTag->setParam('entry_id', $this->getParameter('entry_id'));
        $metaTag->setParam('url_title', $this->getParameter('url_title'));
        $metaTag->setParam('low_variable', $this->getParameter('low_variable'));
        $metaTag->setParam('fallback', ($this->getParameter('fallback') && $this->getParameter('fallback') != 'n' && $this->getParameter('fallback') != 'no'));

        // If it doesnt have any off these things, it should not be on
        if (!$metaTag->shouldParse()) {
            return false;
        }

        // --------------------------------------
        // Tag Parameters
        // --------------------------------------

        $metaTag->setParam('channel_name', ee()->TMPL->fetch_param('channel'));
        $metaTag->setParam('site_id', $this->getParameter('site_id', ee()->config->item('site_id')));
        $metaTag->setParam('hide_site_title', $this->getParameter('hide_site_title'));
        $metaTag->setParam('title_prefix', $this->getParameter('title_prefix'));
        $metaTag->setParam('title_suffix', $this->getParameter('title_suffix'));
        $metaTag->setParam('title', $this->getParameter('title'));
        $metaTag->setParam('ignore_entry_title', $this->getParameter('ignore_entry_title'));
        $metaTag->setParam('keywords', $this->getParameter('keywords'));
        $metaTag->setParam('description', $this->getParameter('description'));
        $metaTag->setParam('author', $this->getParameter('author'));
        $metaTag->setParam('canonical_url', $this->getParameter('canonical_url'));
        $metaTag->setParam('robots', $this->getParameter('robots'));
        $metaTag->setParam('og_title', $this->getParameter('og_title'));
        $metaTag->setParam('og_description', $this->getParameter('og_description'));
        $metaTag->setParam('og_type', $this->getParameter('og_type'));
        $metaTag->setParam('og_url', $this->getParameter('og_url'));
        $metaTag->setParam('og_image', $this->getParameter('og_image'));
        $metaTag->setParam('twitter_title', $this->getParameter('twitter_title'));
        $metaTag->setParam('twitter_description', $this->getParameter('twitter_description'));
        $metaTag->setParam('twitter_content_type', $this->getParameter('twitter_content_type'));
        $metaTag->setParam('twitter_image', $this->getParameter('twitter_image'));

        // --------------------------------------
        // Retrieve Meta
        // --------------------------------------

        // --------------------------------------
        // Loading from a Low Variable?
        // --------------------------------------

        // For now, this is not doing anything so we wont even run them
        // $metaTag->loadLowVar();

        // --------------------------------------
        // Support pages module?
        // --------------------------------------

        // For now, this is not doing anything so we wont even run them
        // $metaTag->loadPages();

        // --------------------------------------
        // Set up the query
        // --------------------------------------

        $metaTag->getQueriedData();

        $channel_id = 0;

        if (!empty($metaTag->seeo_query->channel_id)) {
            $channel_id = $metaTag->seeo_query->channel_id;
        }

        // Get the default settings for this channel (or the global defaults).
        $default_settings = ee()->seeo_settings->get($channel_id);
        $global_default_settings = ee()->seeo_settings->get(0);

        // This filters out the null values and empy values, and then merges on the global defaults
        $default_settings = array_filter($default_settings);
        $default_settings = array_merge($global_default_settings, $default_settings);

        // Update our MetaTag internal settings with the defaults.
        $metaTag->setSettings($default_settings);

        // Figure out what needs to be in the tagdata.
        $metaTag->setTagVars();

        // $publishTabs = new PublishTabs;
        // $visibleFields = $publishTabs->getVisibleFields(1);

        // Grab the tagdata from the template tags (if it exists).
        $tagdata = ee()->TMPL->tagdata;

        // If there is no template tagdata, use our defaults.
        if (empty($tagdata) && !empty($default_settings['template'])) {
            $tagdata = $default_settings['template'];
        }

        $this->return_data = ee()->TMPL->parse_variables_row($tagdata, $metaTag->getTagVarsArray());

        if ($this->getParameter('debug') === 'yes' || $this->getParameter('debug') === 'y') {
            $tagproper = '<div style="font-size:14px;font-weight:bold;margin-bottom:15px;">' . str_replace(array('{', '}'), array('&lbrace;', '&rbrace;'), ee()->TMPL->tagproper) . '</div>';
            $this->return_data = '<div style="margin: 10px 0;border:1px solid #ccc;background-color:#ffffe6;padding:5px 10px;color:#000;font-size:12px;font-family:sans-serif">' . $tagproper . '<pre>' . htmlentities(trim($this->return_data)) . '</pre></div>';
        }

        return $this->return_data;
    }


    // public function header()
    // {
    //     ee()->TMPL->tagparams['from'] = $this->getParameter('from', null);

    //     if (! ee()->TMPL->tagparams['from']) {
    //         return false;
    //     }

    //     return true;

    //     // // get the headers then check if a certain header is in there
    //     // $headers = getallheaders();

    //     // echo "<pre>";
    //     // var_dump($headers);
    //     // exit;

    //     // if ($headers['User-Agent'] == ee()->TMPL->tagparams['from']) {
    //     //     return true;
    //     // }
    // }

    //----------------------------------------------------------------------------

    /**
     * Generate an XML file from entries/pages
     */
    public function xml()
    {
        // Defaults, assuming user wants this
        ee()->TMPL->tagparams['disable'] = $this->getParameter('disable', 'categories|custom_fields|category_fields|member_data|pagination');
        ee()->TMPL->tagparams['dynamic'] = $this->getParameter('dynamic', 'no');
        ee()->TMPL->tagparams['limit'] = $this->getParameter('limit', 500);

        // Get the parameters
        $site_id = $this->getParameter('site_id', ee()->config->item('site_id'));
        $site_pages = ee()->config->item('site_pages');
        $site_pages = $site_pages[$site_id];

        $use_page_url = ($this->getParameter('use_page_url') == 'n' || $this->getParameter('use_page_url') == 'no') ? false : true;
        $prefer_canonical_urls = ($this->getParameter('prefer_canonical_urls') == 'yes' || $this->getParameter('prefer_canonical_urls') == 'y') ? true : false;
        $fallback_url = $this->getParameter('fallback_url') ?: false;
        $format_output = ($this->getParameter('format_output') == 'yes' || $this->getParameter('format_output') == 'y') ? true : false;

        // Get all the channel's enabled in SEEO.
        // We are including channel id 0, which means the template pages
        $channel_ids = array(0);
        $channel_settings = array();
        foreach (ee('Model')->get('Channel')->filter('site_id', $site_id)->all() as $channel) {
            $channel_ids[] = $channel->channel_id;
            $channel_settings[$channel->channel_id] = ee()->seeo_settings->get($channel->channel_id);
            $channel_settings[$channel->channel_id]['channel_url'] = $channel->channel_url;
        }

        $global_default_settings = ee()->seeo_settings->get(0);

        // Grab all our meta entries. We'll filter these below before executing this statement.

        $channels_to_fetch = array();

        // If the global default is NO, loop through each channel and get all entries for
        // enabled channels except entries that specifically opt out.

        // Check each channel to see if we are including all entries.
        foreach ($channel_settings as $channel_id => $settings) {
            // Include this channel if the global setting is to include and the channel is set to fallback OR to include.
            if (($global_default_settings['sitemap_include'] === 'y' && $settings['sitemap_include'] === 'd') || $settings['sitemap_include'] === 'y') {
                $channels_to_fetch[] = $channel_id;
            }
        }

        // Get all the entries in the channels we've specified.
        $skipMetaEntriesQuery = ee('Model')->get('seeo:Meta')->filter('sitemap_include', '==', 'n');

        if (!empty($channels_to_fetch)) {
            $skipMetaEntriesQuery->filter('channel_id', 'IN', $channels_to_fetch);
        }

        $skipMetaEntries = $skipMetaEntriesQuery->all();

        $skippedEntries = array();
        foreach ($skipMetaEntries as $skipMetaEntry) {
            $skippedEntries[] = $skipMetaEntry->entry_id;
        }

        $channelEntriesQuery = ee('Model')->get('ChannelEntry');

        if (!empty($channels_to_fetch)) {
            $channelEntriesQuery->filter('channel_id', 'IN', $channels_to_fetch);
        }

        if (!empty($skippedEntries)) {
            $channelEntriesQuery->filter('entry_id', 'NOT IN', $skippedEntries);
        }

        $channelEntries = $channelEntriesQuery->all();

        // Make sure we remove any entries that are "closed".
        $closedChannelEntriesQuery = ee('Model')->get('ChannelEntry');
        $closedChannelEntriesQuery->filter('status', 'closed');

        if (!empty($channels_to_fetch)) {
            $closedChannelEntriesQuery->filter('channel_id', 'IN', $channels_to_fetch);
        }

        if (!empty($skippedEntries)) {
            $closedChannelEntriesQuery->filter('entry_id', 'NOT IN', $skippedEntries);
        }

        $closedChannelEntries = $closedChannelEntriesQuery->all();

        $closedEntries = array();
        foreach ($closedChannelEntries as $closedChannelEntry) {
            $closedEntries[] = $closedChannelEntry->entry_id;
        }

        // Entries with sitemap_include == 'n' are excluded.

        // Get all the meta entries that:
        //  - Are NOT "closed"
        //  AND
        //      - Have sitemap include turned on
        //      - OR
        //         - Have sitemap_include set to default to the channel settings AND
        //         - Are in the specified channels

        // safety check- if there are no valid channel ids and sitemap_inculde
        // is off, then we feed it a channel id 0 and get no results
        $channel_ids_safety = (empty($channels_to_fetch) ? array(0) : $channels_to_fetch);

        $metaEntriesQuery = ee('Model')->get('seeo:Meta');

        if (!empty($closedEntries)) {
            $metaEntriesQuery->filter('entry_id', 'NOT IN', $closedEntries);
        }

        $metaEntriesQuery->filterGroup()
                            ->filter('sitemap_include', '==', 'y')
                                ->orFilterGroup()
                                    ->filter('sitemap_include', '==', 'd')
                                    ->filter('channel_id', 'IN', $channel_ids_safety)
                                ->endFilterGroup()
                            ->endFilterGroup();


        $metaEntries = $metaEntriesQuery->all();

        $metaEntriesArray = array();
        foreach ($metaEntries as $meta) {
            $metaEntriesArray[] = $meta->entry_id;
        }

        // Loop through any channel entries that do not have meta entries and create them.
        foreach ($channelEntries as $channelEntry) {
            if (!in_array($channelEntry->entry_id, $metaEntriesArray) && !in_array($channelEntry->entry_id, $closedEntries)) {
                $fakeMeta = ee('Model')->make('seeo:Meta');
                $fakeMeta->entry_id = $channelEntry->entry_id;
                $fakeMeta->site_id = ee()->config->item('site_id');
                $fakeMeta->channel_id = $channelEntry->channel_id;
                $fakeMeta->title = $channelEntry->title;
                $fakeMeta->robots = 'd';
                $fakeMeta->sitemap_priority = 'd';
                $fakeMeta->sitemap_change_frequency = 'd';
                $fakeMeta->sitemap_include = 'd';
                $fakeMeta->save();
            }
        }

        // THIS IS NOT A DUPLICATE.
        // It re-fetches the Meta entries now that we've saved the empty ones into the table.

        $metaEntriesQuery = ee('Model')->get('seeo:Meta');

        if (!empty($closedEntries)) {
            $metaEntriesQuery->filter('entry_id', 'NOT IN', $closedEntries);
        }

        $metaEntriesQuery->filterGroup()
                            ->filter('sitemap_include', '==', 'y')
                                ->orFilterGroup()
                                    ->filter('sitemap_include', '==', 'd')
                                    ->filter('channel_id', 'IN', $channel_ids_safety)
                                ->endFilterGroup()
                            ->endFilterGroup();


        $metaEntries = $metaEntriesQuery->all();

        // Return early if there are none
        if (empty($metaEntries) || count($metaEntries) === 0) {
            return '';
        }

        $xml_array = array();

        // Create an XML Array to parse and send to the front end
        foreach ($metaEntries as $meta) {
            $meta_array = array();

            // Pass the parameters to the meta object so it can know how to get the entry loc
            $meta->setParams(['prefer_canonical_urls' => $prefer_canonical_urls,
                               'use_page_url' => $use_page_url,
                               'site_pages' => $site_pages,
                               'fallback_url' => $fallback_url]);

            $meta_array['sitemap_entry_loc'] = $meta->sitemap_entry_loc;

            // If there is no location, check to see if we have a channel settings URL set.
            if ($meta_array['sitemap_entry_loc'] == false) {
                if (!empty($channel_settings[$meta->channel_id]['channel_url'])) {
                    if (substr(strtolower($channel_settings[$meta->channel_id]['channel_url']), 0, 4) === 'http') {
                        $meta_array['sitemap_entry_loc'] = $channel_settings[$meta->channel_id]['channel_url'] . '/' . $meta->ChannelEntry->url_title;
                    } else {
                        $meta_array['sitemap_entry_loc'] = ee()->functions->create_url($channel_settings[$meta->channel_id]['channel_url'] . '/' . $meta->ChannelEntry->url_title);
                    }
                } else {
                    // If we dont have an entry loc, move on. It doesnt go in the sitemap
                    continue;
                }
            }

            $meta_array['sitemap_entry_loc'] = reduce_double_slashes($meta_array['sitemap_entry_loc']);

            // Set all other variables and add it to the array
            $meta_array['sitemap_last_mod'] = $meta->sitemap_last_mod;

            // Check to see if we have entry, channel, or global data for this field.
            if (!empty($meta->sitemap_change_frequency) && $meta->sitemap_change_frequency !== 'd') {
                // We have local data that overrides the channel and global settings.
                $meta_array['sitemap_change_frequency'] = $meta->sitemap_change_frequency;
            } elseif (!empty($channel_settings[$meta->channel_id]['sitemap_change_frequency']) && $channel_settings[$meta->channel_id]['sitemap_change_frequency'] !== 'd') {
                // We didn't have any local but we have channel data that overrides the global settings.
                $meta_array['sitemap_change_frequency'] = $channel_settings[$meta->channel_id]['sitemap_change_frequency'];
            } else {
                // We didn't have any local or channel data so use the global setting.
                $meta_array['sitemap_change_frequency'] = $global_default_settings['sitemap_change_frequency'];
            }

            // Check to see if we have entry, channel, or global data for this field.
            if (!empty($meta->sitemap_priority) && $meta->sitemap_priority !== 'd') {
                // We have local data that overrides the channel and global settings.
                $meta_array['sitemap_priority'] = $meta->sitemap_priority;
            } elseif (!empty($channel_settings[$meta->channel_id]['sitemap_priority']) && $channel_settings[$meta->channel_id]['sitemap_priority'] !== 'd') {
                // We didn't have any local but we have channel data that overrides the global settings.
                $meta_array['sitemap_priority'] = $channel_settings[$meta->channel_id]['sitemap_priority'];
            } else {
                // We didn't have any local or channel data so use the global setting.
                $meta_array['sitemap_priority'] = $global_default_settings['sitemap_priority'];
            }

            $xml_array[] = $meta_array;
        }

        $tagdata = "
    <url>
     <loc>{sitemap_entry_loc}</loc>
     <lastmod>{sitemap_last_mod}</lastmod>
     <changefreq>{sitemap_change_frequency}</changefreq>
     <priority>{sitemap_priority}</priority>
    </url>";

        $return = ee()->TMPL->parse_variables($tagdata, $xml_array);

        // If there is no param to skip the XML tag and there is no XML tag in the actual template (not just tag_data), add it.
        if ($this->getParameter('xml_tag', 'y') !== 'n' && $this->getParameter('xml_tag', 'y') !== 'no' && stripos(ee()->TMPL->template, '<?xml') === false) {
            $return = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n" . $return . "\n" . '</urlset>';
        }

        if ($this->getParameter('debug') === 'yes' || $this->getParameter('debug') === 'y') {
            $tagproper = '<div style="font-size:14px;font-weight:bold;margin-bottom:15px;">' . str_replace(array('{', '}'), array('&lbrace;', '&rbrace;'), ee()->TMPL->tagproper) . '</div>';
            $return = '<div style="margin: 10px 0;border:1px solid #ccc;background-color:#ffffe6;padding:5px 10px;color:#000;font-size:12px;font-family:sans-serif">' . $tagproper . '<pre>' . htmlentities(trim($return)) . '</pre></div>';
        }

        return (! $format_output) ? str_replace(array("\n", "\t"), "", $return) : $return;
    }

    //----------------------------------------------------------------------------

    /**
     * @param $str
     * @return mixed
     */
    private function parse_image_url($str)
    {
        // If it's assets
        if (ee()->seeo_settings->get(0, 'file_manager') == 'assets') {
            ee()->load->add_package_path(PATH_THIRD . 'assets/');
            ee()->load->library('assets_lib');

            $file = ee()->assets_lib->get_file_by_id($str);

            if ($file !== false && !is_array($file)) {
                return $file->url();
            } else {
                return null;
            }
        }

        // This is attempting to determine if this is an id...
        if ((string) (int) $str === (string) $str) {
            $file = ee('Model')->get('File', $str)->first();
            return $file->getAbsoluteURL();
        }

        // If it is a file_field
        ee()->load->library('file_field');
        return ee()->file_field->parse_string($str);
    }

    //----------------------------------------------------------------------------

    /**
     * Return first available value
     *
     * @param  $primary
     * @param  $secondary
     * @param  $fallback
     * @return string
     */
    private function get_first($primary, $secondary, $fallback = '')
    {
        if (!empty($primary)) {
            return htmlspecialchars($primary, ENT_QUOTES);
        }
        if (!empty($secondary)) {
            return htmlspecialchars($secondary, ENT_QUOTES);
        }

        return htmlspecialchars($fallback, ENT_QUOTES);
    }

    //----------------------------------------------------------------------------
}
