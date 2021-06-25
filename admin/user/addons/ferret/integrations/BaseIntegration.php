<?php

/*
 * Base integration class
 */

abstract class BaseIntegration
{
    protected $config = null;
    protected $entry = null;
    protected $objects = null;
    protected $settings = null;

    public function __construct($entry, $objects, $settings)
    {
        $this->config = include __DIR__ . '/../config/config.php';
        $this->entry = $entry;
        $this->objects = $objects;
        $this->settings = $settings;
    }

    abstract public function run();
}
