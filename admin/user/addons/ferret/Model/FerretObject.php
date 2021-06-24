<?php

namespace fostermade\ferret\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class FerretObject extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name = 'exp_ferret_objects';
    protected static $_events = ['beforeSave'];

    protected $id;
    protected $index;
    protected $entry_id;
    protected $mapping;

    /**
     * Register extension hook
     */
    public function onBeforeSave()
    {
        if (ee()->extensions->active_hook('ferret_before_save_object') === true) {
            $object = json_decode($this->mapping, true);

            $object = ee()->extensions->call('ferret_before_save_object', $object);

            $this->mapping = json_encode($object);
        }
    }
}
