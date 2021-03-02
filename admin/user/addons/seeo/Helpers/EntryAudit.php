<?php

namespace EEHarbor\Seeo\Helpers;

use EEHarbor\Seeo\Helpers\AuditItem;
use EllisLab\ExpressionEngine\Library\Data\Collection;

class EntryAudit
{
    public $entry;
    public $entry_id;
    public $channel_id;
    public $settings;
    public $item;
    public $items;

    public $map_counts = array(
        'standard' => 6,
        'open_graph' => 5,
        'twitter' => 4,
        'sitemap' => 3,
        'status' => 0
    );

    public $map = array(
        'standard' => array(
            'total' => 6,
            'count' => 0,
            'class' => '',
            'fields' => array(
                'meta_title',
                'meta_description',
                'meta_keywords',
                'meta_canonical_url',
                'meta_robots',
                'meta_author'
            )
        ),
        'open_graph' => array(
            'total' => 5,
            'count' => 0,
            'class' => '',
            'fields' => array(
                'og_title',
                'og_description',
                'og_image',
                'og_type',
                'og_url'
            )
        ),
        'twitter' => array(
            'total' => 4,
            'count' => 0,
            'class' => '',
            'fields' => array(
                'twitter_title',
                'twitter_description',
                'twitter_content_type',
                'twitter_image'
            )
        ),
        // 'sitemap' => array(
        //     'total' => 3,
        //     'count' => 0,
        //     'class' => '',
        //     'fields' => array(
        //         'sitemap_priority',
        //         'sitemap_change_frequency',
        //         'sitemap_include'
        //     )
        // ),
        'status' => array(
            'total' => 4,
            'count' => 0,
            'class' => '',
            'fields' => array()
        )
    );

    public $notMetaItem = array(
        'entry_id',
        'channel_id',
    );

    public function __construct($entry, $settings = null)
    {
        $this->entry = $entry;
        $this->entry_id = $entry['entry_id'];
        $this->channel_id = $entry['channel_id'];

        // Set the settings
        if ($settings) {
            $this->settings = $settings;
        } else {
            $this->settings = array();
        }

        $this->items['standard'] = new Collection;
        $this->items['open_graph'] = new Collection;
        $this->items['twitter'] = new Collection;
    }

    public function auditItems()
    {
        foreach ($this->map as $group => $items) {
            foreach ($items['fields'] as $field) {
                if (!empty($this->entry[$field])) {
                    $this->map[$group]['count']++;
                }
            }

            if ($this->map[$group]['count'] === 0) {
                $this->map[$group]['class'] = 'missing';
            } elseif ($this->map[$group]['count'] === $this->map_counts[$group]) {
                $this->map[$group]['class'] = 'full';

                $this->map['status']['count']++;
                $this->map['status']['class'] = 'partial';

                if ($this->map['status']['count'] === $this->map['status']['total']) {
                    $this->map['status']['class'] = 'full';
                }
            } else {
                $this->map[$group]['class'] = 'partial';
            }
        }
    }

    public function registerItem($item)
    {
        // If its not in the hidemap, it should always be there
        if (in_array($item['shortname'], $this->notMetaItem)) {
            return null;
        }

        // TODO Load layout and see if field is hidden for this channel.

        // Determine which "group" of items this is.
        switch (substr($item['shortname'], 0, 2)) {
            case 'og':
                $this->items['open_graph'][] = new AuditItem($item);
                break;

            case 'tw':
                $this->items['twitter'][] = new AuditItem($item);
                break;

            default:
                $this->items['standard'][] = new AuditItem($item);
        }
    }

    public function isHidden($item)
    {
        // If its not in the hidemap, it should always be there
        if (! array_key_exists($item['shortname'], $this->hideMap)) {
            return false;
        }

        $hiddenSetting = $this->hideMap[$item['shortname']];

        if (array_key_exists($hiddenSetting, $this->settings) && $this->settings[$hiddenSetting] == 'y') {
            return true;
        }

        return false;
    }
}
