<?php

namespace EEHarbor\Seeo\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class Migration extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name  = 'seeo_migration';

    protected $id;
    protected $shortname;
    protected $date;
    protected $settings;
}
