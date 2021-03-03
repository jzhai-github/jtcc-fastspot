<?php

namespace EEHarbor\Seeo\Model;

use Datetime;
use EllisLab\ExpressionEngine\Service\Model\Model;

class TemplatePage extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name  = 'seeo_template_page';

    protected $id;
    protected $meta_id;
    protected $path;
    protected $channel_id;

    protected static $_relationships = array(
        'Meta' => array(
            'model'    => 'seeo:Meta',
            'type'     => 'HasOne',
            'from_key' => 'meta_id',
            'to_key'   => 'id',
        ),
        'Channel' => array(
            'model'    => 'ee:Channel',
            'type'     => 'BelongsTo',
            'from_key' => 'channel_id',
            'to_key'   => 'channel_id',
            'weak'     => true,
            'inverse'  => array(
                'name' => 'TemplatePage',
                'type' => 'hasOne',
            ),
        ),
    );

    public function delete()
    {
        $this->Meta->delete();
        parent::delete();
    }

    public function set__path($value)
    {
        // Set the path correctly
        $path = "/" . trim($value, " \t\n\r\0\x0B/");

        $this->setRawProperty('path', $path);
    }

    public function get__sitemap_last_mod()
    {
        // If there is no channel, we default to the first of the month.
        if (!$this->Channel) {
            return new DateTime('first day of this month');
        }

        $lastEntry = $this->Channel->Entries->sortBy('edit_date')->last();

        if (empty($lastEntry)) {
            return new DateTime('first day of this month');
        }

        return $lastEntry->edit_date;
    }
}
