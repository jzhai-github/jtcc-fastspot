<?php

class BreakpointsIntegration extends BaseIntegration
{
    protected $options = null;

    public function __construct($entry, $objects, $settings)
    {
        parent::__construct($entry, $objects, $settings);

        $this->options = $this->config[__CLASS__];
    }

    /**
     * Rewrites object URLs to truncate anything after the
     * last found breakpoint.
     */
    public function run()
    {
        if ($this->options['breakpoints']) {
            $this->fixBreakpoints();
        }

        return $this->objects;
    }

    /**
     * Rewrites object urls to truncate any values which appear after
     * the last valid breakpoint
     *
     * @return bool
     */
    protected function fixBreakpoints()
    {
        $base_url = ee()->config->item('base_url');

        foreach ($this->objects as &$object) {
            $url = str_replace($base_url, '', $object['url']);
            $segments = array_reverse(
                array_filter(
                    explode('/', $url)
                )
            );

            foreach ($segments as $key => $value) {
                $check = false;
                foreach ($this->options['breakpoints'] as $breakpoint) {
                    if (strpos($breakpoint, '{wildcard}') !== false) {
                        $breakpoint = str_replace('{wildcard}', '', $breakpoint);
                        if (strpos($value, $breakpoint) === 0) {
                            $check = true;
                        }
                    } elseif ($value == $breakpoint) {
                        $check = true;
                    }
                }

                if ($check) {
                    break;
                } else {
                    unset($segments[$key]);
                }
            }

            if ($segments) {
                $object['url'] = $base_url . implode('/', array_reverse($segments));
            }
        }

        return true;
    }
}
