<?php

namespace EEHarbor\Seeo\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class Meta extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name  = 'seeo';

    protected $id;
    protected $entry_id;
    protected $site_id;
    protected $channel_id;
    protected $title;
    protected $description;
    protected $keywords;
    protected $author;
    protected $title_prefix;
    protected $title_suffix;
    protected $canonical_url;
    protected $robots;
    protected $sitemap_priority;
    protected $sitemap_change_frequency;
    protected $sitemap_include;
    protected $og_title;
    protected $og_description;
    protected $og_type;
    protected $og_url;
    protected $og_image;
    protected $twitter_title;
    protected $twitter_description;
    protected $twitter_content_type;
    protected $twitter_image;

    // This allows us to set params to the meta object, for use sitemap xml generation
    private $params;

    protected static $_relationships = array(
        'TemplatePage' => array(
            'model'    => 'seeo:TemplatePage',
            'type'     => 'BelongsTo',
            'from_key' => 'id',
            'to_key'   => 'meta_id',
        ),
        'ChannelEntry' => array(
            'model'    => 'ee:ChannelEntry',
            'type'     => 'BelongsTo',
            'from_key' => 'entry_id',
            'to_key'   => 'entry_id',
            'weak'     => true,
            'inverse'  => array(
                'name' => 'Meta',
                'type' => 'hasOne',
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();
        $this->set_defaults();
    }

    public function save()
    {
        $this->set_defaults();
        parent::save();
    }

    public function get__id()
    {
        return (int) $this->id;
    }

    public function get__sitemap_last_mod()
    {
        if ($this->isTemplatePage()) {
            $sitemap_last_mod = $this->TemplatePage->sitemap_last_mod->format('Y-m-d\TH:i:s+00:00');
        } else {
            // Sometimes there is no edit date. We're not entirely sure why.
            // Also, ChannelEntry->edit_date is a DateTime, but ChannelEntry->entry_date is a timestamp
            $sitemap_last_mod = (is_null($this->ChannelEntry->edit_date)) ? date('Y-m-d\TH:i:s+00:00', $this->ChannelEntry->entry_date) : $this->ChannelEntry->edit_date->format('Y-m-d\TH:i:s+00:00');
        }
        return $sitemap_last_mod;
    }

    public function get__sitemap_entry_loc()
    {
        // Prefer canonical urls if we can find it
        if ($this->params['prefer_canonical_urls'] && !empty($this->canonical_url)) {
            return (substr(strtolower($this->canonical_url), 0, 4) === 'http' ? $this->canonical_url : ee()->functions->create_url($this->canonical_url));
        }

        // If this is not a template page and we have a canonical for the entry, use that.
        if (! $this->isTemplatePage() && !empty($this->canonical_url)) {
            return (substr(strtolower($this->canonical_url), 0, 4) === 'http' ? $this->canonical_url : ee()->functions->create_url($this->canonical_url));
        }

        // If it is a template page, the entry location is the path (unless it was already the canonical url)
        if ($this->isTemplatePage()) {
            return ee()->functions->create_url($this->TemplatePage->path);
        }

        // Use the Structure / Pages page url if specified in the params, and if it has a URI set
        if ($this->params['use_page_url'] && isset($this->params['site_pages']['uris'][$this->ChannelEntry->entry_id])) {
            return ee()->functions->create_url($this->params['site_pages']['uris'][$this->ChannelEntry->entry_id]);
        }

        // Parse the Channel Entry into the default for a fallback
        if (isset($this->params['fallback_url']) && $this->params['fallback_url']  != '') {
            return ee()->TMPL->parse_variables_row($this->params['fallback_url'], $this->ChannelEntry->toArray());
        }

        return $this->canonical_url ?: false;
    }

    public function get__assets_og_image()
    {
        $this->includeAssets();

        // --------------------------------------
        // Set up OG Image in Assets
        // --------------------------------------
        $assets_og_image = new \Assets_ft();
        $assets_og_image->settings = array(
            'multi' => 'n'
        );
        $assets_og_image->field_name = 'seeo__seeo[og_image]';
        $assets_og_image->var_id = 'seeo__seeo[og_image]';

        return $assets_og_image;
    }

    public function get__assets_twitter_image()
    {
        $this->includeAssets();

        // --------------------------------------
        // Set up Twitter Image in Assets
        // --------------------------------------
        $assets_twitter_image = new \Assets_ft();
        $assets_twitter_image->settings = array(
            'multi' => 'n'
        );
        $assets_twitter_image->field_name = 'seeo__seeo[twitter_image]';
        $assets_twitter_image->var_id = 'seeo__seeo[twitter_image]';

        return $assets_twitter_image;
    }

    public function set_defaults()
    {
        foreach ($this->get_defaults() as $field => $value) {
            if (!isset($this->$field)) {
                $this->__set($field, $value);
            }
        }
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function get_defaults()
    {
        return array(
            'entry_id'         => 0,
            'site_id'          => ee()->config->item('site_id'),
            'channel_id'       => 0,
            'sitemap_include'  => 'd',
            'sitemap_priority' => '0.5',
            'robots'           => 'INDEX, FOLLOW',
        );
    }

    public function isTemplatePage()
    {
        return ($this->entry_id === 0);
    }

    private function includeAssets()
    {
        require_once PATH_THIRD . 'assets/helper.php';
        require_once APPPATH . 'fieldtypes/EE_Fieldtype.php';
        require_once PATH_THIRD . 'assets/ft.assets.php';

        $assets_helper = new \Assets_helper;
        $assets_helper->include_sheet_resources();
    }
}
