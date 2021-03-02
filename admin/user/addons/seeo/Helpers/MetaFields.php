<?php

namespace EEHarbor\Seeo\Helpers;

use EEHarbor\Seeo\FluxCapacitor\FluxCapacitor;

class MetaFields
{
    // Tag params
    public $settings;

    public $default_entry_meta = array(
        'id'                       => null,
        'entry_id'                 => '',
        'site_id'                  => '',
        'channel_id'               => '',

        // Default Meta
        'title'                    => '',
        'description'              => '',
        'keywords'                 => '',
        'author'                   => '',
        'canonical_url'            => '',
        'robots'                   => 'd',

        // Sitemap
        'sitemap_priority'         => 'd',
        'sitemap_change_frequency' => 'd',
        'sitemap_include'          => 'd',

        // Open Graph
        'og_title'                 => '',
        'og_description'           => '',
        'og_type'                  => '',
        'og_url'                   => '',
        'og_image'                 => '',

        // Twitter Cards
        'twitter_title'            => '',
        'twitter_description'      => '',
        'twitter_content_type'     => '',
        'twitter_image'            => '',
    );

    public function __construct()
    {
        $this->flux = new FluxCapacitor;

        $this->settings = ee()->seeo_settings->get();
    }

    public function getTabFields($type = 'all', $entry_meta = null)
    {
        $fields = array();

        if ($type === 'all' || $type === 'standard') {
            $fields['title'] = array(
                'field_id'             => 'title',
                'field_label'          => lang('seeo_standard_title'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["title"] ? : $this->default_entry_meta['title'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['description'] = array(
                'field_id'             => 'description',
                'field_label'          => lang('seeo_standard_description'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["description"] ? : $this->default_entry_meta['description'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['keywords'] = array(
                'field_id'             => 'keywords',
                'field_label'          => lang('seeo_standard_keywords'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["keywords"] ? : $this->default_entry_meta['keywords'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['author'] = array(
                'field_id'             => 'author',
                'field_label'          => lang('seeo_standard_author'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["author"] ? : $this->default_entry_meta['author'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['canonical_url'] = array(
                'field_id'             => 'canonical_url',
                'field_label'          => lang('seeo_standard_canonical_url'),
                'field_instructions'   => lang('seeo_standard_canonical_url_instructions'),
                'field_required'       => 'n',
                'field_data'           => $entry_meta["canonical_url"] ? : $this->default_entry_meta['canonical_url'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['robots'] = array(
                'field_id'             => 'robots',
                'field_label'          => lang('seeo_standard_robots_directive'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta['robots'] ? : $this->default_entry_meta['robots'],
                'field_list_items'     => array(
                                            'd' => 'Use Channel Default',
                                            'INDEX, FOLLOW' => 'INDEX, FOLLOW',
                                            'NOINDEX, FOLLOW' => 'NOINDEX, FOLLOW',
                                            'INDEX, NOFOLLOW' => 'INDEX, NOFOLLOW',
                                            'NOINDEX, NOFOLLOW' => 'NOINDEX, NOFOLLOW'
                                            ),
                'options'              => '',
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'select',
                'field_maxl'           => 255,
                'field_wide'           => true
            );
        }

        if ($type === 'all' || $type === 'open_graph') {
            $fields['og_title'] = array(
                'field_id'             => 'og_title',
                'field_label'          => lang('seeo_og_title'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["og_title"] ? : $this->default_entry_meta['og_title'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['og_description'] = array(
                'field_id'             => 'og_description',
                'field_label'          => lang('seeo_og_description'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["og_description"] ? : $this->default_entry_meta['og_description'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['og_type'] = array(
                'field_id'             => 'og_type',
                'field_label'          => lang('seeo_og_type'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["og_type"] ? : $this->default_entry_meta['og_type'],
                'field_list_items'     => array(
                                            '' => '- Select -',
                                            'article' => 'Article',
                                            'book' => 'Book',
                                            'music.song' => 'Music - Song',
                                            'music.album' => 'Music - Album',
                                            'music.playlist' => 'Music - Playlist',
                                            'music.radio_station' => 'Music - Radio Station',
                                            'profile' => 'Profile',
                                            'video.movie' => 'Video - Movie',
                                            'video.episode' => 'Video - Episode',
                                            'video.tv_show' => 'Video - TV Show',
                                            'video.other' => 'Video - Other',
                                            'website' => 'Website'
                                            ),
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'select',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['og_url'] = array(
                'field_id'             => 'og_url',
                'field_label'          => lang('seeo_og_url'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["og_url"] ? : $this->default_entry_meta['og_url'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['og_image'] = array(
                'field_id'             => 'og_image',
                'field_label'          => lang('seeo_og_image'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["og_image"] ? : $this->default_entry_meta['og_image'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'file',
                'field_maxl'           => 255,
                'field_wide'           => true
            );
        }

        if ($type === 'all' || $type === 'twitter') {
            $fields['twitter_title'] = array(
                'field_id'             => 'twitter_title',
                'field_label'          => lang('seeo_twitter_title'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["twitter_title"] ? : $this->default_entry_meta['twitter_title'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['twitter_description'] = array(
                'field_id'             => 'twitter_description',
                'field_label'          => lang('seeo_twitter_description'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["twitter_description"] ? : $this->default_entry_meta['twitter_description'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'text',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['twitter_content_type'] = array(
                'field_id'             => 'twitter_content_type',
                'field_label'          => lang('seeo_twitter_content_type'),
                'field_instructions'   => lang('seeo_twitter_content_type_instructions'),
                'field_required'       => 'n',
                'field_data'           => $entry_meta["twitter_content_type"] ? : $this->default_entry_meta['twitter_content_type'],
                'field_list_items'     => array(
                                            '' => '- Select -',
                                            'summary' => 'Summary',
                                            'summary_large_image' => 'Summary - Large Image',
                                            'app' => 'App',
                                            'product' => 'Product'
                                            ),
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'select',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['twitter_image'] = array(
                'field_id'             => 'twitter_image',
                'field_label'          => lang('seeo_twitter_image'),
                'field_instructions'   => '',
                'field_required'       => 'n',
                'field_data'           => $entry_meta["twitter_image"] ? : $this->default_entry_meta['twitter_image'],
                'field_list_items'     => '',
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'file',
                'field_maxl'           => 255,
                'field_wide'           => true
            );
        }

        if ($type === 'all' || $type === 'sitemap') {
            $fields['sitemap_priority'] = array(
                'field_id'             => 'sitemap_priority',
                'field_label'          => lang('seeo_sitemap_priority'),
                'field_instructions'   => lang('seeo_sitemap_priority_instructions'),
                'field_required'       => 'n',
                'field_data'           => $entry_meta['sitemap_priority'] ? : $this->default_entry_meta['sitemap_priority'],
                'field_list_items'     => array(
                                            'd' => 'Use Channel Default',
                                            '0.0' => '0.0',
                                            '0.1' => '0.1',
                                            '0.2' => '0.2',
                                            '0.3' => '0.3',
                                            '0.4' => '0.4',
                                            '0.5' => '0.5 - Default',
                                            '0.6' => '0.6',
                                            '0.7' => '0.7',
                                            '0.8' => '0.8',
                                            '0.9' => '0.9',
                                            '1.0' => '1.0',
                                        ),
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'select',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['sitemap_change_frequency'] = array(
                'field_id'             => 'sitemap_change_frequency',
                'field_label'          => lang('seeo_sitemap_change_frequency'),
                'field_instructions'   => lang('seeo_sitemap_change_frequency_instructions'),
                'field_required'       => 'n',
                'field_data'           => $entry_meta["sitemap_change_frequency"] ? : $this->default_entry_meta['sitemap_change_frequency'],
                'field_list_items'     => array(
                                            'd' => 'Use Channel Default',
                                            'always' => 'Always',
                                            'hourly' => 'Hourly',
                                            'daily' => 'Daily',
                                            'weekly' => 'Weekly',
                                            'monthly' => 'Monthly',
                                            'yearly' => 'Yearly',
                                            'never' => 'Never'
                                        ),
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'select',
                'field_maxl'           => 255,
                'field_wide'           => true
            );

            $fields['sitemap_include'] = array(
                'field_id'             => 'sitemap_include',
                'field_label'          => lang('seeo_sitemap_include'),
                'field_instructions'   => lang('seeo_sitemap_include_instructions'),
                'field_required'       => 'n',
                'field_data'           => $entry_meta["sitemap_include"] ? : $this->default_entry_meta['sitemap_include'],
                'field_list_items'     => array(
                                            'd' => 'Use Channel Default',
                                            'y' => lang('yes'),
                                            'n' => lang('no')
                                        ),
                'options'              => array(),
                'selected'             => '',
                'field_fmt'            => '',
                'field_show_fmt'       => 'n',
                'field_pre_populate'   => 'n',
                'field_text_direction' => 'ltr',
                'field_type'           => 'select',
                'field_maxl'           => 255,
                'field_wide'           => true
            );
        }

        return $fields;
    }

    public function getSharedFormFields($type = 'all', $entry_meta = null)
    {
        $fields = array();

        if ($type === 'all' || $type === 'standard') {
            $fields['seeo_standard_title'] = array(
                'title' => 'seeo_standard_title',
                'desc' => '',
                'fields' => array(
                    'title' => array(
                        'type' => 'text',
                        'value' => $entry_meta["title"] ? : $this->default_entry_meta['title'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['description'] = array(
                'title' => 'seeo_standard_description',
                'desc' => '',
                'fields' => array(
                    'description' => array(
                        'type' => 'text',
                        'value' => $entry_meta["description"] ? : $this->default_entry_meta['description'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['keywords'] = array(
                'title' => 'seeo_standard_keywords',
                'desc' => '',
                'fields' => array(
                    'keywords' => array(
                        'type' => 'text',
                        'value' => $entry_meta["keywords"] ? : $this->default_entry_meta['keywords'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['author'] = array(
                'title' => 'seeo_standard_author',
                'desc' => '',
                'fields' => array(
                    'author' => array(
                        'type' => 'text',
                        'value' => $entry_meta['author'] ? : $this->default_entry_meta['author'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['canonical_url'] = array(
                'title' => 'seeo_standard_canonical_url',
                'desc' => 'seeo_standard_canonical_url_instructions',
                'fields' => array(
                    'canonical_url' => array(
                        'type' => 'text',
                        'value' => $entry_meta['canonical_url'] ? : $this->default_entry_meta['canonical_url'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['robots'] = array(
                'title' => 'seeo_standard_robots_directive',
                'desc' => '',
                'fields' => array(
                    'robots' => array(
                        'type' => 'select',
                        'value' => $entry_meta['robots'] ? : $this->default_entry_meta['robots'],
                        'required' => false,
                        'maxlength' => 255,
                        'choices' => array(
                            'INDEX, FOLLOW' => 'INDEX, FOLLOW',
                            'NOINDEX, FOLLOW' => 'NOINDEX, FOLLOW',
                            'INDEX, NOFOLLOW' => 'INDEX, NOFOLLOW',
                            'NOINDEX, NOFOLLOW' => 'NOINDEX, NOFOLLOW'
                        ),
                    )
                )
            );
        }

        if ($type === 'all' || $type === 'open_graph') {
            $fields['og_title'] = array(
                'title' => 'seeo_og_title',
                'desc' => '',
                'fields' => array(
                    'og_title' => array(
                        'type' => 'text',
                        'value' => $entry_meta['og_title'] ? : $this->default_entry_meta['og_title'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['og_description'] = array(
                'title' => 'seeo_og_description',
                'desc' => '',
                'fields' => array(
                    'og_description' => array(
                        'type' => 'text',
                        'value' => $entry_meta['og_description'] ? : $this->default_entry_meta['og_description'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['og_type'] = array(
                'title' => 'seeo_og_type',
                'desc' => '',
                'fields' => array(
                    'og_type' => array(
                        'type' => 'select',
                        'value' => $entry_meta['og_type'] ? : $this->default_entry_meta['og_type'],
                        'required' => false,
                        'maxlength' => 255,
                        'choices'     => array(
                            '' => '- Select -',
                            'article' => 'Article',
                            'book' => 'Book',
                            'music.song' => 'Music - Song',
                            'music.album' => 'Music - Album',
                            'music.playlist' => 'Music - Playlist',
                            'music.radio_station' => 'Music - Radio Station',
                            'profile' => 'Profile',
                            'video.movie' => 'Video - Movie',
                            'video.episode' => 'Video - Episode',
                            'video.tv_show' => 'Video - TV Show',
                            'video.other' => 'Video - Other',
                            'website' => 'Website'
                        )
                    )
                )
            );

            $fields['og_url'] = array(
                'title' => 'seeo_og_url',
                'desc' => '',
                'fields' => array(
                    'og_url' => array(
                        'type' => 'text',
                        'value' => $entry_meta['og_url'] ? : $this->default_entry_meta['og_url'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $og_image_data = $entry_meta['og_image'] ? : $this->default_entry_meta['og_image'];

            if ($this->flux->ver_gte(5)) {
                $file_field_content = ee()->file_field->dragAndDropField('og_image', $og_image_data, 'all', 'image');
            } else {
                $fp = ee('CP/FilePicker')->make('all');

                $fp_link = $fp->getLink()
                    ->withValueTarget('og_image')
                    ->withNameTarget('og_image')
                    ->withImage('og_image');

                ee()->lang->loadfile('filemanager');

                $fp_upload = clone $fp_link;
                $fp_upload
                    ->setText(lang('upload_file'))
                    ->setAttribute('class', 'btn action file-field-filepicker');

                $fp_edit = clone $fp_link;
                $fp_edit
                    ->setText('')
                    ->setAttribute('title', lang('edit'))
                    ->setAttribute('class', 'file-field-filepicker');

                $file = null;

                if (preg_match('/^{filedir_(\d+)}/', $og_image_data, $matches)) {
                    // Set upload directory ID and file name
                    $dir_id = $matches[1];
                    $file_name = str_replace($matches[0], '', $og_image_data);

                    $file = ee('Model')->get('File')
                        ->filter('file_name', $file_name)
                        ->filter('upload_location_id', $dir_id)
                        ->filter('site_id', ee()->config->item('site_id'))
                        ->first();
                } elseif (! empty($og_image_data) && is_numeric($og_image_data)) {
                    // If file field is just a file ID
                    $file = ee('Model')->get('File', $og_image_data)->first();
                }

                $file_field_content = ee('View')->make('file:publish')->render(array(
                    'field_name' => 'og_image',
                    'value' => $og_image_data,
                    'file' => $file,
                    'title' => ($file) ? $file->title : '',
                    'is_image' => ($file && $file->isImage()),
                    'thumbnail' => ee('Thumbnail')->get($file)->url,
                    'fp_url' => $fp->getUrl(),
                    'fp_upload' => $fp_upload,
                    'fp_edit' => $fp_edit
                ));
            }

            $fields['og_image'] = array(
                'title' => 'seeo_og_image',
                'desc' => '',
                'fields' => array(
                    'og_image' => array(
                        'type' => 'html',
                        'value' => $og_image_data,
                        'required' => false,
                        'maxlength' => 255,
                        'content' => $file_field_content
                    )
                )
            );
        }

        if ($type === 'all' || $type === 'twitter') {
            $fields['twitter_title'] = array(
                'title' => 'seeo_twitter_title',
                'desc' => '',
                'fields' => array(
                    'twitter_title' => array(
                        'type' => 'text',
                        'value' => $entry_meta['twitter_title'] ? : $this->default_entry_meta['twitter_title'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['twitter_description'] = array(
                'title' => 'seeo_twitter_description',
                'desc' => '',
                'fields' => array(
                    'twitter_description' => array(
                        'type' => 'text',
                        'value' => $entry_meta['twitter_description'] ? : $this->default_entry_meta['twitter_description'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );

            $fields['twitter_content_type'] = array(
                'title' => 'seeo_twitter_content_type',
                'desc' => 'seeo_twitter_content_type_instructions',
                'fields' => array(
                    'twitter_content_type' => array(
                        'type' => 'select',
                        'value' => $entry_meta['twitter_content_type'] ? : $this->default_entry_meta['twitter_content_type'],
                        'required' => false,
                        'maxlength' => 255,
                        'choices'     => array(
                            '' => '- Select -',
                            'summary' => 'Summary',
                            'summary_large_image' => 'Summary - Large Image',
                            'app' => 'App',
                            'product' => 'Product'
                        )
                    )
                )
            );

            $twitter_image_data = $entry_meta['twitter_image'] ? : $this->default_entry_meta['twitter_image'];

            if ($this->flux->ver_gte(5)) {
                $file_field_content = ee()->file_field->dragAndDropField('twitter_image', $twitter_image_data, 'all', 'image');
            } else {
                $fp = ee('CP/FilePicker')->make('all');

                $fp_link = $fp->getLink()
                    ->withValueTarget('twitter_image')
                    ->withNameTarget('twitter_image')
                    ->withImage('twitter_image');

                ee()->lang->loadfile('filemanager');

                $fp_upload = clone $fp_link;
                $fp_upload
                    ->setText(lang('upload_file'))
                    ->setAttribute('class', 'btn action file-field-filepicker');

                $fp_edit = clone $fp_link;
                $fp_edit
                    ->setText('')
                    ->setAttribute('title', lang('edit'))
                    ->setAttribute('class', 'file-field-filepicker');

                $file = null;

                if (preg_match('/^{filedir_(\d+)}/', $twitter_image_data, $matches)) {
                    // Set upload directory ID and file name
                    $dir_id = $matches[1];
                    $file_name = str_replace($matches[0], '', $twitter_image_data);

                    $file = ee('Model')->get('File')
                        ->filter('file_name', $file_name)
                        ->filter('upload_location_id', $dir_id)
                        ->filter('site_id', ee()->config->item('site_id'))
                        ->first();
                } elseif (! empty($twitter_image_data) && is_numeric($twitter_image_data)) {
                    // If file field is just a file ID
                    $file = ee('Model')->get('File', $twitter_image_data)->first();
                }

                $file_field_content = ee('View')->make('file:publish')->render(array(
                    'field_name' => 'twitter_image',
                    'value' => $twitter_image_data,
                    'file' => $file,
                    'title' => ($file) ? $file->title : '',
                    'is_image' => ($file && $file->isImage()),
                    'thumbnail' => ee('Thumbnail')->get($file)->url,
                    'fp_url' => $fp->getUrl(),
                    'fp_upload' => $fp_upload,
                    'fp_edit' => $fp_edit
                ));
            }

            $fields['twitter_image'] = array(
                'title' => 'seeo_twitter_image',
                'desc' => '',
                'fields' => array(
                    'twitter_image' => array(
                        'type' => 'html',
                        'value' => $twitter_image_data,
                        'required' => false,
                        'maxlength' => 255,
                        'content' => $file_field_content
                    )
                )
            );
        }

        if ($type === 'all' || $type === 'sitemap') {
            $fields['sitemap_priority'] = array(
                'title' => 'seeo_sitemap_priority',
                'desc' => 'seeo_sitemap_priority_instructions',
                'fields' => array(
                    'sitemap_priority' => array(
                        'type' => 'select',
                        'value' => $entry_meta['sitemap_priority'] ? : $this->default_entry_meta['sitemap_priority'],
                        'required' => false,
                        'maxlength' => 255,
                        'choices'     => array(
                            '0.0' => '0.0',
                            '0.1' => '0.1',
                            '0.2' => '0.2',
                            '0.3' => '0.3',
                            '0.4' => '0.4',
                            '0.5' => '0.5 - Default',
                            '0.6' => '0.6',
                            '0.7' => '0.7',
                            '0.8' => '0.8',
                            '0.9' => '0.9',
                            '1.0' => '1.0',
                        )
                    )
                )
            );

            $fields['sitemap_change_frequency'] = array(
                'title' => 'seeo_sitemap_change_frequency',
                'desc' => 'seeo_sitemap_change_frequency_instructions',
                'fields' => array(
                    'sitemap_change_frequency' => array(
                        'type' => 'select',
                        'value' => $entry_meta['sitemap_change_frequency'] ? : $this->default_entry_meta['sitemap_change_frequency'],
                        'required' => false,
                        'maxlength' => 255,
                        'choices'     => array(
                            'always' => 'Always',
                            'hourly' => 'Hourly',
                            'daily' => 'Daily',
                            'weekly' => 'Weekly',
                            'monthly' => 'Monthly',
                            'yearly' => 'Yearly',
                            'never' => 'Never'
                        )
                    )
                )
            );

            $fields['sitemap_include'] = array(
                'title' => 'seeo_sitemap_include',
                'desc' => 'seeo_sitemap_include_instructions',
                'fields' => array(
                    'sitemap_include' => array(
                        'type' => 'yes_no',
                        'value' => $entry_meta['sitemap_include'] ? : $this->default_entry_meta['sitemap_include'],
                        'required' => false,
                        'maxlength' => 255,
                    )
                )
            );
        }

        return $fields;
    }
}
