<?php

class BaseUserIntegration
{
    protected $entry = null;
    protected $objects = null;

    public function __construct($entry, $objects)
    {
        $this->entry = $entry;
        $this->objects = $objects;
    }
}
