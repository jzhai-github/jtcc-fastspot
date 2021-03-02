<?php

namespace EEHarbor\Seeo\Helpers;

class AuditItem
{
    public $shortname;
    // public $title;
    public $valid;
    public $badgeClass;

    public function __construct($item)
    {
        $this->shortname = $item['shortname'];
        $this->value = $item['value'];
        $this->valid = ! ($item['value'] == '');
        $this->badgeClass = ($this->valid) ? 'st-enable' : 'st-disable';
    }
}
