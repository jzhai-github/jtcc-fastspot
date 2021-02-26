<?php

namespace HopStudios\Hop404Reporter\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

/**
 * Hop Studios Config Model class
 *
 * @package   Hop Studios
 * @author    Hop Studios <tech@hopstudios.com>
 * @copyright Copyright (c) Copyright (c) 2019 Hop Studios
 */

class Config extends Model
{
    protected static $_primary_key = 'setting_id';
    protected static $_table_name = 'hop_404_reporter_settings';

    protected static $_typed_columns = array(
        'value' => 'base64Serialized',
    );

    protected $setting_id;
    protected $setting_name;
    protected $value;
}