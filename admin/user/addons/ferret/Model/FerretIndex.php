<?php

namespace fostermade\ferret\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class FerretIndex extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name = 'exp_ferret_indexes';

    protected $id;
    protected $name;
    protected $fields = '';
    protected $categories;
    protected $paths = '';

    /**
     * @return array|mixed
     */
    protected function get__categories()
    {
        return $this->categories ? unserialize($this->categories) : [];
    }

    protected function set__categories($value)
    {
        $this->setRawProperty('categories', serialize($value));
    }

    /**
     * @return mixed
     */
    protected function get__fields()
    {
        return $this->fields ? unserialize($this->fields) : [];
    }

    /**
     * @param $value
     */
    protected function set__fields($value)
    {
        $this->setRawProperty('fields', serialize($value));
    }

    /**
     * @return mixed
     */
    protected function get__paths()
    {
        return $this->paths ? unserialize($this->paths) : [];
    }

    protected function set__paths($value)
    {
        $this->setRawProperty('paths', serialize($value));
    }
}
