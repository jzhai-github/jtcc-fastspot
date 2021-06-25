<?php

namespace fostermade\ferret\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class FerretRecord extends Model
{
    protected static $_primary_key = 'objectID';
    protected static $_table_name = 'exp_ferret_records';

    protected $objectID;
    protected $index;
    protected $entry_id;
    protected $order;
}
