<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Rocket_ext {
    var $version = '';
    var $settings = [];
    var $name = '';
    var $description = '';
    var $settings_exist = false;
    var $docs_url = '';

    public function __construct() {
        // load settings from the addon file
        $this->loadSettings();

        // make sure we have a cache folder
        $this->cache_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'rocket_cache';
        if (!file_exists($this->cache_path)) {
            mkdir($this->cache_path, 0755, true);
        }

        // see if rocket is enabled
        $this->enabled = false;
        $this->enabled_path = $this->cache_path . DIRECTORY_SEPARATOR . 'enabled';
        if (file_exists($this->enabled_path)) {
            $this->enabled = true;
        }

        // bypass cache if logged in?
        $this->bypass = false;
        $this->bypass_path = $this->cache_path . DIRECTORY_SEPARATOR . 'bypass';
        if (file_exists($this->bypass_path)) {
            $this->bypass = true;
        }

        // settings from the database
        $this->exceptions = [];
        $this->exceptions_mode = 'exclude';
        $this->update_on_save = 'yes';
        $this->dont_minify = 'no';
        if (ee()->db->table_exists('exp_rocket_settings')) {
            $settings = ee()->db->query("SELECT `label`,`value` FROM `exp_rocket_settings`;");

            foreach($settings->result_array() as $_setting) {
                switch ($_setting['label']) {
                    case 'exceptions':
                        $this->exceptions = explode(PHP_EOL, $_setting['value']);
                        break;
                    case 'exceptions_mode':
                        $this->exceptions_mode = $_setting['value'];
                        break;
                    case 'update_on_save':
                        if ($_setting['value'] != 'yes') {
                            $this->update_on_save = 'no';
                        }
                        break;
                    case 'dont_minify':
                        $this->dont_minify = $_setting['value'];
                        break;
                }
            }
        }
    }

    private function actions_create() {
        ee()->db->insert('actions', [
            'class' => __CLASS__,
            'method' => 'action_purge_cache',
        ]);
    }

    public function action_purge_cache()
    {
        die('called');
    }

    public function activate_extension() {
        $this->actions_create();
        $this->hooks_create();

        ee()->load->dbforge();
        // paths table
        $fields = [
            'entry_id' => ['type' => 'int', 'unsigned' => true],
            'path' => ['type' => 'varchar', 'constraint' => '10000'],
        ];
        ee()->dbforge->add_field($fields);
        ee()->dbforge->create_table('rocket_paths', true);

        // settings table
        $fields = [
            'label' => ['type' => 'varchar', 'constraint' => '50'],
            'value' => ['type' => 'text'],
        ];
        ee()->dbforge->add_field($fields);
        ee()->dbforge->add_key('label', true);
        ee()->dbforge->create_table('rocket_settings', true);
        ee()->db->query("INSERT INTO exp_rocket_settings (`label`,`value`) VALUES ('exceptions','');");
        ee()->db->query("INSERT INTO exp_rocket_settings (`label`,`value`) VALUES ('exceptions_mode','exclude');");

        $this->member_login();
    }

    public function cache_url($url) {
        if ($this->exceptions_mode == 'exclude') {
            foreach ($this->exceptions as $_exception) {
                if (!empty($_exception)) {
                    if (substr($_exception, -1) == '*' && strpos($url, substr($_exception, 0, -1)) === 0) {
                        // starts with
                        return false;
                    } elseif ($url == $_exception) {
                        // exact match
                        return false;
                    }
                }
            }
        }

        if ($this->exceptions_mode == 'include') {
            if (empty($this->exceptions)) {
                return false;
            }
            $match = false;
            foreach($this->exceptions as $_exception) {
                if (substr($_exception, -1) == '*' && strpos($url, substr($_exception, 0, -1)) === 0) {
                    // starts with
                    $match = true;
                } elseif ($url == $_exception) {
                    // exact match
                    $match = true;
                }
            }
            if (!$match) {
                return false;
            }
        }

        $path = $this->cache_path;
        $filename = base64_encode($url) . '.html';
        $filepath = $path . DIRECTORY_SEPARATOR . $filename;

        $scheme = !empty($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
        $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $url;

        $html = $this->get($url);
        if (!empty($html)) {
            $html .= "<!-- Rocket Cache $url -->";

            file_put_contents($filepath, $html);
        }
    }

    public function cp_custom_menu($menu) {
        $sub = $menu->addSubmenu('Rocket');
        $sub->addItem('Settings', ee('CP/URL')->make('addons/settings/rocket'));
        $sub->addItem('Purge Cache', ee('CP/URL')->make('addons/settings/rocket/action_purge_cache'));
    }

    public function disable_extension() {
        $this->disable_rocket();
        $this->purge_cache();
        if (is_dir($this->cache_path)) {
            rmdir($this->cache_path);
        }
        $this->hooks_destroy();
        ee()->load->dbforge();
        ee()->dbforge->drop_table('rocket_settings');
        ee()->dbforge->drop_table('rocket_paths');
    }

    public function disable_rocket()
    {
        if (file_exists($this->enabled_path)) {
            unlink($this->enabled_path);
        }
        $this->enabled = false;
    }

    public function enable_rocket()
    {
        touch($this->enabled_path);
        $this->enabled = true;
    }

    public function get($url) {
        $url .= strpos($url, '?') ? '&rocket_bypass' : '?rocket_bypass';

        $out = @file_get_contents($url, false);
        if (!empty($out)) {
            $pattern = "(<input type=\"hidden\" name=\"csrf_token\" value=\"\w*\" ?\/>)";
            $out = preg_replace($pattern, '{{ROCKET_CSRF}}', $out);

            $out = $this->minify_html($out);
        }

        return $out;
    }

    private function hooks_create() {
        ee()->db->insert_batch('extensions', [
            [
                'class' => __CLASS__,'priority' => 1,'version' => $this->version,'enabled' => 'y','settings' => '',
                'hook' => 'core_boot',
                'method' => 'render_url',
            ],
            [
                'class' => __CLASS__, 'priority' => 1, 'version' => $this->version, 'enabled' => 'y', 'settings' => '',
                'hook' => 'channel_entries_query_result',
                'method' => 'log_channel_entry_ids',
            ],
            [
                'class' => __CLASS__, 'priority' => 1, 'version' => $this->version, 'enabled' => 'y', 'settings' => '',
                'hook' => 'cp_custom_menu',
                'method' => 'cp_custom_menu',
            ],
            [
                'class' => __CLASS__, 'priority' => 1, 'version' => $this->version, 'enabled' => 'y', 'settings' => '',
                'hook' => 'cp_member_login',
                'method' => 'member_login',
            ],
            [
                'class' => __CLASS__, 'priority' => 1, 'version' => $this->version, 'enabled' => 'y', 'settings' => '',
                'hook' => 'cp_member_logout',
                'method' => 'member_logout',
            ],
            [
                'class' => __CLASS__, 'priority' => 1, 'version' => $this->version, 'enabled' => 'y', 'settings' => '',
                'hook' => 'low_reorder_post_sort',
                'method' => 'log_low_reorder_entry_ids',
            ],
            [
                'class' => __CLASS__, 'priority' => 1, 'version' => $this->version, 'enabled' => 'y', 'settings' => '',
                'hook' => 'after_channel_entry_update',
                'method' => 'updated_channel_entry',
            ],
        ]);

    }

    private function hooks_destroy() {
        ee()->db->where('class', __CLASS__);
        ee()->db->delete('extensions');
    }

    private function loadSettings() {
        $settings = include PATH_THIRD . 'rocket/addon.setup.php';

        foreach ($settings as $_key => $_setting) {
            $this->{$_key} = $_setting;
        }
    }

    public function log_channel_entry_ids($channel, $query_result) {
        if (REQ == 'CP') {
            return $query_result;
        }
        if (ee()->TMPL->template_type == '404') {
            return $query_result;
        }
        if (stripos(json_encode(ee()->TMPL->tag_data), 'search_results')) {
            return $query_result;
        }
        $uri = $_SERVER['REQUEST_URI'];


        foreach ($query_result as $_result) {
            $entry_id = $_result['entry_id'];


            ee()->db->query(
                "INSERT INTO exp_rocket_paths (`entry_id`, `path`)
                SELECT * FROM (SELECT ?, ?) AS tmp
                WHERE NOT EXISTS(
                    SELECT entry_id FROM exp_rocket_paths WHERE `entry_id` = ? AND `path` = ?
                ) LIMIT 1;",
                [$entry_id, $uri, $entry_id, $uri]
            );
        }

        return $query_result;
    }

    public function member_login()
    {
        setcookie('loggedin',1,0,'/');
    }

    public function member_logout()
    {
        setcookie('loggedin',1,time()-3600, '/');
    }

    private function minify_html($html) {
        if ($this->dont_minify == 'yes') {
            return $html;
        }

        $search = array(
            '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
            '/[^\S ]+\</s',     // strip whitespaces before tags, except space
            '/(\s)+/s',         // shorten multiple whitespace sequences
            '/<!--(.|\s)*?-->/' // Remove HTML comments
        );

        $replace = array(
            '>',
            '<',
            '\\1',
            ''
        );

        $out = preg_replace($search, $replace, $html);

        return $out;
    }

    public function purge_cache() {
        $files = glob($this->cache_path . DIRECTORY_SEPARATOR . '*.html');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function render_url() {
        if (REQ == 'CP' || REQ == 'ACTION' || isset($_GET['rocket_bypass'])) {
            $_SERVER['REQUEST_URI'] = str_replace('?rocket_bypass', '', str_replace('&rocket_bypass', '', $_SERVER['REQUEST_URI']));
            return;
        }
        $uri = $_SERVER['REQUEST_URI'];

        if ($this->bypass && isset($_COOKIE['loggedin'])) {
            return;
        }

        $_SESSION['ROCKET_CSRF'] = CSRF_TOKEN;

        if ($this->enabled) {
            $filename = base64_encode($uri) . '.html';
            $filepath = $this->cache_path . DIRECTORY_SEPARATOR . $filename;
            if (file_exists($filepath)) {
                $out = file_get_contents($filepath);
                $out = str_replace(
                    '{{ROCKET_CSRF}}',
                    '<input type="hidden" name="csrf_token" value="'.CSRF_TOKEN.'" />',
                    $out
                );
                die($out);
            } else {
                $this->cache_url($uri);
            }
        }
    }


    public function save_settings() {
        if (empty($_POST)) {
            show_error(lang('unauthorized_access'));
        }

        // safe to assume someone is logged in at this point
        $this->member_login();

        if ($_POST['enabled'] == 'y') {
            $this->enable_rocket();
        } else {
            $this->disable_rocket();
        }

        if ($_POST['bypass'] == 'y') {
            touch($this->bypass_path);
        } else {
            if (file_exists($this->bypass_path)) {
                unlink($this->bypass_path);
            }
        }

        ee()->db->query(
            "REPLACE INTO exp_rocket_settings (value,label) VALUES (?,?);",
            [$_POST['exceptions'], 'exceptions']
        );

        ee()->db->query(
            "REPLACE INTO exp_rocket_settings (value,label) VALUES (?,?);",
            [$_POST['exceptions_mode'], 'exceptions_mode']
        );

        ee()->db->query(
            "REPLACE INTO exp_rocket_settings (value, label) VALUES (?,?);",
            [$_POST['dont_minify'], 'dont_minify']
        );

        ee()->db->query(
            "REPLACE INTO exp_rocket_settings (value, label) VALUES (?,?);",
            [$_POST['update_on_save'], 'update_on_save']
        );

        $this->purge_cache();

        ee()->functions->redirect(ee('CP/URL')->make('addons/settings/rocket'));
    }

    public function settings_form($current) {
        $url = explode('/', $_SERVER['QUERY_STRING']);
        if (end($url) == 'action_purge_cache') {
            $this->purge_cache();
            ee('CP/Alert')->makeBanner('Rocket')
              ->asSuccess()
              ->withTitle('Cache Purged')
              ->addToBody('Rocket cache has been purged')
              ->defer();

            ee()->functions->redirect(ee('CP/URL')->make('addons/settings/rocket'));
            die();
        }

        $exceptions = '';
        $exceptions_result = ee()->db->query("SELECT value FROM `exp_rocket_settings` WHERE `label` = 'exceptions';");
        if ($exceptions_result->num_rows() > 0) {
            $exceptions = $exceptions_result->result_array()[0]['value'];
        }

        $vars = [
            'base_url' => ee('CP/URL')->make('addons/settings/rocket/save'),
            'cp_page_title' => 'Rocket Settings',
            'action_button' => [
                'href' => ee('CP/URL')->make('addons/settings/rocket/action_purge_cache'),
                'text' => 'settings_purge_cache',
            ],
            'save_btn_text' => 'btn_save_settings',
            'save_btn_text_working' => 'btn_saving',
            'alerts_name' => 'rocket-save',
            'sections' => [[]],
        ];

        $vars['sections'] = [
            [[
                'title' => 'settings_rocket_enabled',
                'fields' => [
                    'enabled' => [
                        'type' => 'yes_no',
                        'value' => $this->enabled,
                        'required' => false,
                    ],
                ],
            ]],
            [[
                'title' => 'settings_bypass_when_logged_in',
                'fields' => [
                    'bypass' => [
                        'type' => 'yes_no',
                        'value' => $this->bypass,
                        'required' => false,
                    ],
                ],
            ]],
            [[
                'title' => 'settings_dont_minify',
                'fields' => [
                    'dont_minify' => [
                        'type' => 'inline_radio',
                        'choices' => [
                            'yes' => 'Minification disabled/off',
                            'no' => 'Minification enabled/on',
                        ],
                        'value' => $this->dont_minify,
                        'required' => true,
                    ],
                ],
            ]],
            [[
                'title' => 'settings_update_on_save',
                'fields' => [
                    'update_on_save' => [
                        'type' => 'inline_radio',
                        'choices' => [
                            'yes' => 'Yes - Create a new cached files when an entry is saved',
                            'no' => 'No - Delete cached files on save but do not create new ones',
                        ],
                        'value' => $this->update_on_save,
                        'required' => true,
                    ],
                ],
            ]],
            [[
                'title' => 'settings_exceptions_mode',
                'fields' => [
                    'exceptions_mode' => [
                        'type' => 'inline_radio',
                        'choices' => [
                            'include' => 'Include - Only cache URLs listed below',
                            'exclude' => 'Exclude - Do not cache URLs listed below',
                        ],
                        'value' => $this->exceptions_mode,
                        'required' => true,
                    ],
                ],
            ]],
            [[
                'title' => 'settings_exceptions',
                'fields' => [
                    'exceptions' => [
                        'type' => 'textarea',
                        'value' => $exceptions,
                        'required' => false,
                    ],
                ],
            ]],
        ];

        return ee('View')->make('ee:_shared/form')->render($vars);
    }

    public function updated_channel_entry($entry, $values, $modified) {
        $paths = ee()->db->query(
            "SELECT DISTINCT(`path`) AS `path`
            FROM `exp_rocket_paths`
            WHERE `entry_id` = ?",
            [$entry->entry_id]
        );


        foreach ($paths->result_array() as $_path) {
            ee()->db->query("DELETE FROM exp_rocket_paths WHERE `path` = ?", [$_path['path']]);

            if ($this->update_on_save == 'yes') {
                $this->cache_url($_path['path']);
            } else {
                $file = $this->cache_path . DIRECTORY_SEPARATOR . base64_encode($_path['path']) . '.html';

                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }

        return true;
    }

    public function update_extension($current = '')
    {
        if ($current == '' OR $current == $this->version) {
            return false;
        }

        $this->hooks_destroy();
        $this->hooks_create();

        ee()->db->where('class', __CLASS__);
        ee()->db->update(
            'extensions',
            ['version' => $this->version]
        );
    }
}
