<?php

namespace EEHarbor\Seeo\Helpers;

class MetaTag
{

    // Tag params
    public $params;
    public $template_page;
    public $low_variable;
    public $pages;
    public $seeo_query;
    public $settings;
    public $tagvars;

    public function __construct()
    {
        $this->params   = new \stdClass();
        $this->tagvars  = new \stdClass();
        $this->settings = array();
        $this->loadTemplatePage();
    }

    public function hasParam($tag_param)
    {
        return (bool) @$this->params->$tag_param;
    }

    public function setParam($name, $value = null)
    {
        if (!(bool) $name) {
            return false;
        }

        if ($name === 'fallback' && $value === 'no') {
            return false;
        }

        $this->params->$name = trim($value);
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    public function shouldParse()
    {
        return ($this->hasParam('entry_id') || $this->hasParam('url_title') || $this->hasParam('low_variable') || $this->hasParam('fallback') || (bool) $this->template_page);
    }

    public function getQueriedData()
    {
        $table_name = 'seeo';

        $select =
            'keywords,
            description,
            author,
            title_prefix,
            title_suffix,
            canonical_url,
            robots,
            sitemap_priority,
            sitemap_change_frequency,
            sitemap_include,
            og_title,
            og_description,
            og_type,
            og_url,
            og_image,
            twitter_title,
            twitter_description,
            twitter_content_type,
            twitter_image';

        // If low, set up that query
        if ($this->low_variable) {
            $where['var_id'] = $this->low_variable->variable_id;

            $select = 'title, ' . $select . ', var_id';

            ee()->db->select($select);
            ee()->db->from('seeo');
            ee()->db->where($where);
        } elseif ($this->template_page) {
            $where['id'] = $this->template_page->meta_id;

            $select = 'title, ' . $select;

            ee()->db->select($select);
            ee()->db->from('seeo');
            ee()->db->where($where);
        } elseif ($this->hasParam('entry_id') || $this->hasParam('url_title')) {
            $where = array('t.site_id' => $this->params->site_id);

            if ($this->hasParam('entry_id')) {
                $where['t.entry_id'] = $this->params->entry_id;
            } elseif ($this->hasParam('url_title')) {
                $where['url_title'] = $this->params->url_title;
            }

            $select =
                't.entry_id,
                t.channel_id,
                t.title as original_title,
                url_title, '
                . $table_name .
                '.title as title, '
                . $select;

            ee()->db->select($select);
            ee()->db->from('channel_titles t');

            ee()->db->where($where);
            ee()->db->join($table_name, $table_name . '.entry_id = t.entry_id', 'left');

            if ($this->hasParam('channel_name')) {
                ee()->db->join('channels', 't.channel_id = channels.channel_id')
                    ->where('channels.channel_name', $this->params->channel_name);
            }
        } else {
            $where['entry_id'] = -1;

            $select = 'title, ' . $select;

            ee()->db->select($select);
            ee()->db->from('seeo');
            ee()->db->where($where);
        }

        // --------------------------------------
        // Run it
        // --------------------------------------

        $query = ee()->db->get();

        if ($query->num_rows >= 1) {
            $this->seeo_query = $query->row();

            // This uses the entry title as the meta title, if there is no meta title set
            // This behavior is what people expect, and is now the default
            if ($this->hasParam('ignore_entry_title') === false || $this->params->ignore_entry_title  !== 'yes') {
                $this->seeo_query->title = $this->seeo_query->title ?: $this->seeo_query->original_title;
            }

            return $this->seeo_query;
        } else {
            return null;
        }
    }

    // checks template_page from URI and assigns it
    public function loadTemplatePage()
    {
        $uri = "/" . trim(ee()->uri->uri_string(), " \t\n\r\0\x0B/");

        $this->template_page = ee('Model')->get('seeo:TemplatePage')->filter('path', '==', $uri)->first();

        return $this->template_page;
    }

    // attempts to get the low var and assigns it internally
    public function loadLowVar()
    {
        if ($this->hasParam('low_variable')) {
            $variable = ee()->db->from('global_variables')
                ->where('variable_name', $this->params->low_variable)
                ->get();

            if ($variable->num_rows() == 1) {
                $this->low_variable = $variable->row();
            } else {
                ee()->load->library('logger');
                ee()->logger->log_action('No variable with name "' . $this->params->low_variable . '" can be located!');
            }
        }
    }

    public function loadPages()
    {
        // has the url_title, but not the entry_id
        if ($this->hasParam('url_title') && !$this->hasParam('entry_id')) {
            $pages = ee()->config->item('site_pages');

            if (isset($pages[$this->params->site_id])) {
                $current_uri_string = ee()->uri->uri_string();

                if ($current_uri_string != '') {
                    foreach ($pages[$this->params->site_id]['uris'] as $page_entry_id => $page_uri) {
                        if (trim($page_uri, '/') == $current_uri_string) {
                            $this->pages = new \stdClass();

                            $this->pages->entry_id  = $page_entry_id;
                            $this->pages->url_title = false;
                        }
                    }
                }
            }
        }
    }

    public function getHighestPriorityItem($tagname)
    {
        $found_value = null;

        // This is the highest priority thing to return
        // It comes from setting an entry id or a URL title or similar
        if ($this->seeo_query && (bool) $this->seeo_query->$tagname && $this->seeo_query->$tagname !== 'd') {
            $found_value = $this->seeo_query->$tagname;
        } elseif ($this->hasParam($tagname)) {
            // Still nothing, try the fallback value (like title='My Title') in the tag
            $found_value = $this->params->$tagname;
        } elseif (isset($this->settings[$tagname]) && (bool) $this->settings[$tagname] && $this->settings[$tagname] !== 'd') {
            // If there is no meta data found, the next step would be to find is in the channel settings
            $found_value = $this->settings[$tagname];
        } else {
            // We found nothing. The last thing to check now is the global site settings.
            $globalSettings = ee()->seeo_settings->get();
            if (isset($globalSettings[$tagname]) && (bool) $globalSettings[$tagname]) {
                $found_value = $globalSettings[$tagname];
            }
        }

        // if it's an image, parse the url
        if ($tagname === 'twitter_image' || $tagname === 'og_image') {
            $found_value = $this->parseImageUrl($found_value);
        }

        // set the tagvars parameter
        $this->tagvars->$tagname = htmlspecialchars($found_value, ENT_QUOTES);

        return $this->tagvars->$tagname;
    }

    public function setTagVars()
    {
        $tag_params = array(
            'title',
            'keywords',
            'description',
            'author',
            'title_prefix',
            'title_suffix',
            'robots',
            'canonical_url',
            'og_description',
            'og_title',
            'og_type',
            'og_url',
            'twitter_content_type',
            'twitter_title',
            'twitter_description',
            'twitter_image',
            'og_image',
        );

        foreach ($tag_params as $param) {
            $this->getHighestPriorityItem($param);
        }

        $this->makeTitleChanges();
    }

    public function makeTitleChanges()
    {
        if ($this->tagvars->title == '') {
            $this->tagvars->title = $this->settings['title'];
        }

        $original_title = $this->tagvars->title;

        // -----------------------------------------
        // Add the Default site title and dividers
        // -----------------------------------------
        if ($this->params->title_prefix) {
            $this->tagvars->title = $this->params->title_prefix . ' ' . $this->settings['divider'] . ' ' . $this->tagvars->title;
        }

        if ($this->params->title_suffix) {
            $this->tagvars->title = $this->tagvars->title . ' ' . $this->settings['divider'] . ' ' . $this->params->title_suffix;
        }

        // Make sure the default site title is set
        if ((empty($this->params->hide_site_title) || ($this->params->hide_site_title !== 'yes' && $this->params->hide_site_title !== 'y')) && $this->settings['title'] && $original_title !== $this->settings['title']) {
            $this->tagvars->title = $this->tagvars->title . ' ' . $this->settings['divider'] . ' ' . $this->settings['title'];
        }
    }

    public function getTagVarsArray()
    {
        return json_decode(json_encode($this->tagvars), true);
    }

    private function parseImageUrl($str)
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

        // In case of array
        if (is_array($str)) {
            $str = @reset($str);
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
}
