<?php

// load amazon s3 library
require_once 'libraries/Request.php';
require_once 'libraries/S3.php';

require_once 'Conduit/Backup.php';

use EEHarbor\Safeharbor\Conduit\Backup;
use EEHarbor\Safeharbor\FluxCapacitor\Base\Mcp;

class Safeharbor_mcp extends Mcp
{
    private $settings;
    private $notify_email_address;
    private $base_url;

    // data to be sent to the views
    private $data = array();
    private $servers = array('eeharbor.com/index.php?ACT=23');

    public function __construct()
    {
        parent::__construct();
        $this->backupHelper = new Backup;

        // load settings
        $this->settings = ee('Model')->get('safeharbor:Settings')->first();

        // get raw property for if statements
        if ($this->settings !== null) {
            $this->notify_email_address = $this->settings->getProperty('notify_email_address');
        }
    }


    /**
     *
     *  ROUTE FUNCTIONS:
     *
     */

    /**
     * Method to return a listing of backups for the main view in the module
     * ROUTE: /admin.php?/cp/addons/settings/safeharbor/index
     *
     * @method index
     * @return Array with a view to be rendered by EE
     */
    public function index()
    {
        // do we have setting?
        $count = ee('Model')->get('safeharbor:Settings')->count();
        if ($count == 0) {
            ee()->functions->redirect(ee('CP/URL', 'addons/settings/safeharbor/settings'));
        }

        $table = ee('CP/Table');

        // set the table columns for the main page
        $table->setColumns(
            array(
                lang('backup_date') => array('encode' => false ),
                lang('backup_status'),
                lang('backup_type'),
                lang('time_to_backup') => array('encode' => false ),
                lang('backup_size') => array('encode' => false ),
                lang('backup_note') => array('encode' => false ),
                lang('download') => array('encode' => false ),
                lang('actions') => array('encode' => false ),
            )
        );

        // get data for the table
        $table->setData($this->getBackupsColData());

        $vars['base_url'] = ee('CP/URL', 'addons/settings/safeharbor');
        $vars['table'] = $table->viewData($vars['base_url']);

        // Setup the ajax backup calls
        $vars['backup_url_full'] = $this->settings->get__cron_url('full');
        $vars['backup_url_diff'] = $this->settings->get__cron_url('diff');

        // return rendered view array
        return array(
            'heading' => lang('safeharbor_module_name'),
            'body' => ee('View')->make('safeharbor:index')->render($vars)
        );
    }

    /**
     * Get settings page
     * ROUTE: /admin.php?/cp/addons/settings/safeharbor/settings
     *
     * @method settings
     * @return Settings view to be rendered by EE
     */
    public function settings()
    {
        // if the settings aren't set it means this is the first time... we'll create a new settings model
        if ($this->settings == null) {
            $this->settings = ee('Model')->make('safeharbor:Settings');
        }

        //build out the view
        $vars['sections'] = $this->getSettingsForm();

        $vars += array(
            'base_url' => ee('CP/URL', 'addons/settings/safeharbor/postSettings'),
            'cp_page_title' => lang('safeharbor_module_name_settings'),
            'save_btn_text' => 'btn_save_settings',

            'save_btn_text_working' => 'btn_saving'
        );

        $settingsContent = ee('View')->make('safeharbor:settings')->render($vars);

        if (substr(APP_VER, 0, 1) < 4) {
            $settingsContent = '<div class="box">' . $settingsContent . '</div>';
        }

        return array(
            'heading' => $vars['cp_page_title'],
            'breadcrumb' => array(
                ee('CP/URL', 'addons/settings/safeharbor/')->compile() => lang('safeharbor_module_name')
                ),
            'body' => $settingsContent
        );
    }

    /**
     * Update settings. This is a post request.
     * ROUTE: /admin.php?/cp/addons/settings/safeharbor/postSettings
     *
     * @method postSettings
     * @return redirect to settings page with error or success
     */
    public function postSettings()
    {
        // set validation rules
        $rules = array(
            'notify_email_address'  =>  'required|email',
            'backup_space'          =>  'required|numeric',
            'backup_path'           =>  'required',
            'storage_path'          =>  'required',
            'db_backup'             =>  'required|enum[command,php]',
            'amazon_s3_enabled'     =>  'required|enum[n,y]',
            'ftp_enabled'           =>  'required|enum[n,y]',
            'ftp_port'              =>  'integer',
        );

        $result = ee('Validation')->make($rules)->validate($_POST);

        // does the data pass our validation?
        if ($result->isValid()) {
            // if the settings aren't set it means this is the first time... we'll create a new settings model
            if ($this->settings == null) {
                $this->settings = $this->backupHelper->initializeSettings();
            }

            // assign posted data to our model
            $this->settings->auth_code              = trim(ee('Request')->post('auth_code'));
            $this->settings->notify_email_address   = trim(ee('Request')->post('notify_email_address'));
            $this->settings->backup_space           = trim(ee('Request')->post('backup_space'));
            $this->settings->backup_path            = trim(ee('Request')->post('backup_path'));
            $this->settings->storage_path           = trim(ee('Request')->post('storage_path'));
            $this->settings->db_backup              = trim(ee('Request')->post('db_backup'));
            $this->settings->disable_remote         = 1;
            $this->settings->amazon_s3_enabled      = trim(ee('Request')->post('amazon_s3_enabled'));
            $this->settings->amazon_s3_access_key   = trim(ee('Request')->post('amazon_s3_access_key'));
            $this->settings->amazon_s3_secret       = trim(ee('Request')->post('amazon_s3_secret'));
            $this->settings->amazon_s3_bucket       = trim(ee('Request')->post('amazon_s3_bucket'));
            $this->settings->amazon_s3_endpoint     = trim(ee('Request')->post('amazon_s3_endpoint'));
            $this->settings->ftp_enabled            = trim(ee('Request')->post('ftp_enabled'));
            $this->settings->ftp_host               = trim(ee('Request')->post('ftp_host'));
            $this->settings->ftp_username           = trim(ee('Request')->post('ftp_username'));
            $this->settings->ftp_password           = trim(ee('Request')->post('ftp_password'));
            $this->settings->ftp_port               = (int)trim(ee('Request')->post('ftp_port'));
            $this->settings->ftp_path               = trim(ee('Request')->post('ftp_path'));

            // save the settings
            if ($this->settings->save()) {
                // create dir for storage
                $dirs_exist = $this->backupHelper->create_dir_structure($this->settings->storage_path);

                if (!$dirs_exist) {
                    // set fail message for directories not being created.
                    ee('CP/Alert')->makeBanner('box')
                        ->asIssue()
                        ->withTitle(lang('backup_path_not_writable'))
                        // ->addToBody('Test data gere')
                        ->defer();
                } else {
                    // set success message
                    ee('CP/Alert')->makeBanner('box')
                        ->asSuccess()
                        ->withTitle(lang('safeharbor_config_save_success'))
                        // ->addToBody('Test data gere')
                        ->defer();
                }
            } else {
                // set fail message
                ee('CP/Alert')->makeBanner('box')
                    ->asIssue()
                    ->withTitle(lang('safeharbor_config_save_failure'))
                    // ->addToBody('Test data gere')
                    ->defer();
            }
        } else {
            $failed = $result->getFailed();
            $message = '';
            foreach ($failed as $name => $fail) {
                $message .= lang('safeharbor_settings_validation_' . $name);
                $message .= '<br>';
            }

            ee('CP/Alert')->makeBanner('box')
                ->asIssue()
                ->withTitle($message)
                ->defer();
        }

        ee()->functions->redirect(ee('CP/URL', 'addons/settings/safeharbor/settings'));
    }

    /**
     * Download link for .tgz file
     * ROUTE: /admin.php?/cp/addons/settings/safeharbor/download/$backup_id/$type
     *
     * @method download
     * @return Download of the backup
     */
    public function download($backup_id, $type = 'full')
    {
        //
        // this function is all ugly. rewrite all the logic
        //

        $backup_path = $this->backupHelper->_get_storage_path('base');

        ee()->db->select('name, full_backup_id, status');
        $backup = ee()->db->get_where('safeharbor_backups', array('backup_id' => $backup_id), 1);
        $backup = $backup->row_array();

        list($name, $extension) = explode('.', $backup['name']);

        if ($type === 'full') {
            $ext = '.tgz';
            $backup_file = $backup_path . (($backup['status'] == 'archived') ? 'old_backups/' : 'current_backup/') . $name . $ext;
        } else {
            $ext = '.sql';
            $backup_file = $backup_path . (($backup['status'] == 'archived') ? 'old_backups/' : 'current_backup/') . $name . $ext;
        }

        if (file_exists($backup_file)) {
            $download = true;
        } else {
            if ($backup['status'] == 'archived') {
                $backup_path = $this->backupHelper->_get_storage_path('default_old');
            } else {
                $backup_path = $this->backupHelper->_get_storage_path('default_current');
            }

            if ($type === 'full') {
                $ext = '.tgz';
                $backup_file = $backup_path . $name . $ext;
            } else {
                $ext = '.sql';
                $backup_file = $backup_path . $name . $ext;
            }

            if (file_exists($backup_file)) {
                $download = true;
            } else {
                $download = false;
                ee('CP/Alert')->makeBanner('box')
                        ->asIssue()
                        ->withTitle(lang('download_failed_file_not_found'))
                        ->defer();
            }
        }

        if ($download) {
            // send file download to user
            $file_size = filesize($backup_file);
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=" . $name . $ext);
            header("Content-Type: application/octet-stream");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".$file_size);

            // Per PHP's documentation: readfile() will not present any memory issues, even when sending large files, on
            // its own. If you encounter an out of memory error ensure that output buffering is off with ob_get_level().
            readfile($backup_file);
        } else {
            // this means we don't have a backup id... set fail message
            ee('CP/Alert')->makeBanner('box')
                        ->asIssue()
                        ->withTitle("Doesn't exist")
                        // ->addToBody('Test data gere')
                        ->defer();
        }

        // redirect with flash message from above
        ee()->functions->redirect(ee('CP/URL', 'addons/settings/safeharbor'));
    }

    /**
     * Backup your DB. Clicking 'Run Full Backup' Hits this
     * ROUTE: /admin.php?/cp/addons/settings/safeharbor/backup/(full|differential)
     *
     * @method backup
     * @return Redirects with Flash data
     */
    public function backup($type = 'full')
    {
        // function used to trigger backup inside the CP.
        $request = new Request();
        $request->set_option('CURLOPT_CONNECTTIMEOUT', 2);
        $request->set_option('CURLOPT_TIMEOUT', 2);
        $request->set_option('CURLOPT_SSL_VERIFYPEER', false);
        $request->set_option('CURLOPT_SSL_VERIFYHOST', false);
        $result = $request->get($this->settings->get__cron_url($type));

        if (!empty($result['error'])) {
            // set failure flash message
            ee('CP/Alert')->makeBanner('box')
                ->asIssue()
                ->withTitle(lang('backup_start_failed'))
                ->addToBody('There was a problem starting your backup:<br /><br />'.$result['error'])
                ->defer();
        } else {
            // set success flash message
            ee('CP/Alert')->makeBanner('box')
                ->asSuccess()
                ->withTitle(lang('backup_running'))
                // ->addToBody('Test data gere')
                ->defer();
        }

        // redirect
        ee()->functions->redirect(ee('CP/URL', 'addons/settings/safeharbor'));
    }

    /**
     * Add a note to a backup
     *
     * @method note
     * @param  int $backup_id ID of backup we would like to add this note to.
     * @return Redirect             Redirects with flash data
     */
    public function note($backup_id = false)
    {
        // Find backups that match this id
        $backup = ee('Model')->get('safeharbor:Backups')->filter('backup_id', (int) $backup_id);

        // if we find some, continue
        if ($backup->count()) {
            // get first backup that matches this id and get it.
            $backup = $backup->first();
            $backup->note = ee('Request')->post('note', '');
            $backup->save();

            // set success message
            ee('CP/Alert')->makeBanner('box')
                        ->asSuccess()
                        ->withTitle(lang('backup_note_save_success'))
                        // ->addToBody('Test data gere')
                        ->defer();
        } else {
            // this means we don't have a backup id... set fail message
            ee('CP/Alert')->makeBanner('box')
                        ->asIssue()
                        ->withTitle(lang('backup_note_save_failure'))
                        // ->addToBody('Test data gere')
                        ->defer();
        }

        // redirect with flash message from above
        ee()->functions->redirect(ee('CP/URL', 'addons/settings/safeharbor'));
    }

    /**
     * Search for a backup
     *
     * @method search
     * @param  [type] $backup_id [description]
     * @return [type] [description]
     */
    public function search($backup_id)
    {
        // load libraries
        ee()->load->library('table');

        if (version_compare(APP_VER, '2.6.0', '<')) {
            ee()->cp->set_variable('cp_page_title', ee()->lang->line('safeharbor_module_name_search'));
        } else {
            ee()->view->cp_page_title = ee()->lang->line('safeharbor_module_name_search');
        }

        $ajax_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=safeharbor'.AMP.'method=query'.AMP.'O='.$backup_id.'';
        $ajax_url=  html_entity_decode($ajax_url);

        // setting the JS stuff needed up
        ee()->cp->add_to_head('
        <script type="text/javascript">
            var safeharbor_settings = {
                "site_id": ' . ee()->config->item('site_id') . ',
                "xid": "' . XID_SECURE_HASH . '"};
        </script>');

        ee()->cp->add_to_head('<script type="text/javascript">
        $(document).ready(function()
            {
            $(".search").keyup(function()
             // $("#live_search").submit(function()
            {
                // $(".live_search").submit(function() {
                var filesearch = $(this).val();
                var dataString = "qs"+ filesearch;
                if(filesearch!="")
                {
                    $.ajax({
                        type: "GET",
                        url: "'.$ajax_url.'",
                        // data: { dataString, "xid": "' . XID_SECURE_HASH . '" },
                        data: { qs:filesearch },
                        cache: false,
                        success: function(html)
                        {
                            $("#display").html(html).show();
                        }
                    });
                }
            // });
                return false;
            });

        });
        </script>');

        // check to see if we have the current backup indexed.  If not go ahead and index it.
        $is_indexed = ee()->db->query("SELECT id FROM ".ee()->db->dbprefix('safeharbor_indexes')." WHERE backup_id=$backup_id LIMIT 1");

        if ($is_indexed->num_rows() > 0) {
            // this means the backup has been indexed.
        } else {
            // backup hasn't been indexed, lets index it and continue
            $this->index_backup($backup_id);
        }

        $this->data['backup_id'] = ee()->input->get('O');

        return ee()->load->view('search', $this->data, true);
    }


    public function delete($backup_id)
    {

        // I really don't like how the path checks are done here... it should be moved to the _remove_backup method.
        // We will also have to refactor the manage_backup_files to remove the paths from there.
        // also check to see where else we might need to refactor as a result.
        // TODO: refactor this to remove the path checks
        // TODO: move path checks into the _remove_backup method possible
        ee()->db->select('name, backup_id, start_time, status');
        $backup = ee()->db->get_where('safeharbor_backups', array('backup_id' => $backup_id), 1);
        $backup = $backup->row_array();

        // seperate name and extension, because we need to delete the .tgz and the .sql if they exist.
        list($name, $extension) = explode('.', $backup['name']);


        if ($backup['status'] != 'current_full') {
            $path = $this->backupHelper->_get_storage_path('old');

            if (file_exists($path . $name . '.tgz')) {
                $return_data = $this->backupHelper->_remove_backup($path . $name . '.tgz', $backup_id);
            } else {
                $path = $this->backupHelper->_get_storage_path('default_old');

                if (file_exists($path.$backup['name'])) {
                    $return_data = $this->backupHelper->_remove_backup($path . $path . $name . '.tgz', $backup_id);
                }
            }

            $path = $this->backupHelper->_get_storage_path('old');

            if (file_exists($path . $name . '.sql')) {
                $return_data = $this->backupHelper->_remove_backup($path . $name . '.sql', $backup_id);
            } else {
                $path = $this->backupHelper->_get_storage_path('default_old');

                if (file_exists($path . $name . '.sql')) {
                    $return_data = $this->backupHelper->_remove_backup($path . $path . $name . '.sql', $backup_id);
                }
            }
        } else {
            $path = $this->backupHelper->_get_storage_path('current');

            if (file_exists($path . $name . '.tgz')) {
                $return_data = $this->backupHelper->_remove_backup($path . $name . '.tgz', $backup_id);
            } else {
                $path = $this->backupHelper->_get_storage_path('default_current');

                if (file_exists($path . $name . '.tgz')) {
                    $return_data = $this->backupHelper->_remove_backup($path . $name . '.tgz', $backup_id);
                }
            }

            $path = $this->backupHelper->_get_storage_path('current');

            if (file_exists($path . $name . '.sql')) {
                $return_data = $this->backupHelper->_remove_backup($path . $name . '.sql', $backup_id);
            } else {
                $path = $this->backupHelper->_get_storage_path('default_current');

                if (file_exists($path . $name . '.sql')) {
                    $return_data = $this->backupHelper->_remove_backup($path . $name . '.sql', $backup_id);
                }
            }
        }

        if ($return_data) {
            // set success message
            ee('CP/Alert')->makeBanner('box')
                        ->asSuccess()
                        ->withTitle(lang('backup_deleted'))
                        // ->addToBody('Test data gere')
                        ->defer();
        } else {
            // this means we don't have a backup id... set fail message
            ee('CP/Alert')->makeBanner('box')
                        ->asIssue()
                        ->withTitle(lang('backup_delete_failed'))
                        // ->addToBody('Test data gere')
                        ->defer();
        }

        // redirect
        ee()->functions->redirect(ee('CP/URL', 'addons/settings/safeharbor'));
    }

    /**
     * Sets up an array for the settings page
     *
     * @method getSettingsForm
     * @return array formatted for the ee shared settings view
     */
    private function getSettingsForm()
    {
        // create column for auth code
        $form[0][1]['title'] = lang('auth_code');
        $form[0][1]['desc'] = '';
        $form[0][1]['fields'] = array(
            'auth_code' => array('type' => 'text',
                                    'value' => $this->settings->get__auth_code()),
            );

        // add the email here
        $form[0][2]['title'] = lang('notify_email_address');
        $form[0][2]['desc'] = '';
        $form[0][2]['fields'] = array(
            'notify_email_address' => array('type' => 'text',
                                    'value' => $this->settings->notify_email_address,
                                    'required' => true));

        // attach backup to email?
        $form[0][3]['title'] = lang('backup_space');
        $form[0][3]['desc'] = '';
        $form[0][3]['fields'] = array(
            'backup_space' => array('type' => 'text',
                                    'value' => $this->settings->backup_space));

        // EE Path
        $form[0][4]['title'] = lang('backup_path');
        $form[0][4]['desc'] = '';
        $form[0][4]['fields'] = array(
            'backup_path' => array('type' => 'text',
                                        'value' => $this->settings->backup_path));

        // Backups path
        $form[0][5]['title'] = lang('storage_path');
        $form[0][5]['desc'] = '';
        $form[0][5]['fields'] = array(
            'storage_path' => array('type' => 'text',
                                        'value' => $this->settings->storage_path));

        // Type of backup
        $form[0][8]['title'] = lang('db_backup');
        $form[0][8]['desc'] = '';
        $form[0][8]['fields'] = array(
            'db_backup' => array('type' => 'select',
                                'choices' => array( 'command' => lang('command'), 'php' => lang('php')),
                                'value' => $this->settings->db_backup));

        // CRON url
        $form[0][10]['title'] = lang('cron_url');
        $form[0][10]['desc'] = '';
        $form[0][10]['fields'] = array(
            'trigger_url' => array('type'       => 'text',
                                    'value'     => $this->settings->get__cron_url(),
                                    'disabled'  => 'disabled'),
            );

        // Enabled?
        $form['Amazon S3'][0]['title'] = lang('amazon_s3_enabled');
        $form['Amazon S3'][0]['desc'] = '';
        $form['Amazon S3'][0]['fields'] = array(
            'amazon_s3_enabled' => array('type' => 'yes_no',
                                        'value' => (bool) $this->settings->amazon_s3_enabled));

        // Amazon S3 access key ID
        $form['Amazon S3'][1]['title'] = lang('amazon_s3_access_key');
        $form['Amazon S3'][1]['desc'] = '';
        $form['Amazon S3'][1]['fields'] = array(
            'amazon_s3_access_key' => array('type' => 'text',
                                        'value' => $this->settings->amazon_s3_access_key));

        // Amazon S3 secret
        $form['Amazon S3'][2]['title'] = lang('amazon_s3_secret');
        $form['Amazon S3'][2]['desc'] = '';
        $form['Amazon S3'][2]['fields'] = array(
            'amazon_s3_secret' => array('type' => 'text',
                                        'value' => $this->settings->amazon_s3_secret));

        // Amazon S3 bucket
        $form['Amazon S3'][3]['title'] = lang('amazon_s3_bucket');
        $form['Amazon S3'][3]['desc'] = '';
        $form['Amazon S3'][3]['fields'] = array(
            'amazon_s3_bucket' => array('type' => 'text',
                                        'value' => $this->settings->amazon_s3_bucket));

        // Amazon S3 endpoint
        $form['Amazon S3'][4]['title'] = lang('amazon_s3_endpoint');
        $form['Amazon S3'][4]['desc'] = '';
        $form['Amazon S3'][4]['fields'] = array(
            'amazon_s3_endpoint' => array('type' => 'text',
                                        'value' => $this->settings->amazon_s3_endpoint));

        // Check if FTP is even enabled on this server.
        // If not, force FTP enabled to No and hijack the rest of the settings to display a warning.
        if (!function_exists('ftp_connect')) {
            $this->settings->ftp_enabled = 'n';
        }

        // Enabled?
        $form['FTP'][0]['title'] = lang('ftp_enabled');
        $form['FTP'][0]['desc'] = '';
        $form['FTP'][0]['fields'] = array(
            'ftp_enabled' => array('type' => 'yes_no',
                                        'value' => (bool) $this->settings->ftp_enabled));

        $ftp_field_index = 1;

        // Check if FTP is even enabled on this server.
        // If not, force FTP enabled to No and hijack the rest of the settings to display a warning.
        if (!function_exists('ftp_connect')) {
            $php_info_url = ee('CP/URL', 'utilities/php');

            $form['FTP'][$ftp_field_index]['title'] = '';
            $form['FTP'][$ftp_field_index]['desc'] = '';
            $form['FTP'][$ftp_field_index]['fields'] = array(
                'ftp_test' => array(
                    'type' => 'html',
                    'content' => '<strong>FTP functionality is not enabled on your server.</strong><br />Please ask your hosting provider to enable FTP for PHP.<br /><a target="_blank" href="'.$php_info_url.'">PHP Info Page</a>'
                )
            );

            $ftp_field_index++;
        }

        // FTP Host
        $form['FTP'][$ftp_field_index]['title'] = lang('ftp_host');
        $form['FTP'][$ftp_field_index]['desc'] = '';
        $form['FTP'][$ftp_field_index]['fields'] = array(
            'ftp_host' => array('type' => 'text',
                                        'value' => $this->settings->ftp_host));
        $ftp_field_index++;

        // FTP Username
        $form['FTP'][$ftp_field_index]['title'] = lang('ftp_username');
        $form['FTP'][$ftp_field_index]['desc'] = '';
        $form['FTP'][$ftp_field_index]['fields'] = array(
            'ftp_username' => array('type' => 'text',
                                        'value' => $this->settings->ftp_username));

        $ftp_field_index++;

        // FTP Password
        $form['FTP'][$ftp_field_index]['title'] = lang('ftp_password');
        $form['FTP'][$ftp_field_index]['desc'] = '';
        $form['FTP'][$ftp_field_index]['fields'] = array(
            'ftp_password' => array('type' => 'text',
                                        'value' => $this->settings->ftp_password));

        $ftp_field_index++;

        // FTP port
        $form['FTP'][$ftp_field_index]['title'] = lang('ftp_port');
        $form['FTP'][$ftp_field_index]['desc'] = '';
        $form['FTP'][$ftp_field_index]['fields'] = array(
            'ftp_port' => array('type' => 'text',
                                        'value' => $this->settings->ftp_port));

        $ftp_field_index++;

        // Path on remote server to store backups
        $form['FTP'][$ftp_field_index]['title'] = lang('ftp_path');
        $form['FTP'][$ftp_field_index]['desc'] = '';
        $form['FTP'][$ftp_field_index]['fields'] = array(
            'ftp_path' => array('type' => 'text',
                                        'value' => $this->settings->ftp_path));

        $ftp_field_index++;

        // Option to test FTP settings
        $form['FTP'][$ftp_field_index]['title'] = lang('ftp_test');
        $form['FTP'][$ftp_field_index]['desc'] = '';
        $form['FTP'][$ftp_field_index]['fields'] = array(
            'ftp_test' => array(
                'type' => 'html',
                'content' => (!function_exists('ftp_connect') ? lang('ftp_no_php_ftp') : '<button type="button" id="ftp_test" class="btn action">'.lang('ftp_test_button').'</button><div id="ftp_test_response" style="display:none;margin-top:20px;"></div>')
            )
        );

        // Javascript for the FTP test connection.
        ee()->javascript->output("$(function() {
            $('#ftp_test').on('click', function() {
                $('#ftp_test_response').hide();
                $(this).html('".lang('ftp_test_button_waiting')."').addClass('disable').attr('disabled', true);

                // Retrieve and check all the FTP settings.
                var ftpHost = $('input[name=ftp_host]').val();
                var ftpUser = $('input[name=ftp_username]').val();
                var ftpPass = $('input[name=ftp_password]').val();
                var ftpPort = $('input[name=ftp_port]').val();
                var ftpPath = $('input[name=ftp_path]').val();

                var testRequest = $.ajax({
                    url: '".ee('CP/URL', 'addons/settings/safeharbor/test_ftp')."',
                    type: 'POST',
                    data: {
                        ftpHost:ftpHost,
                        ftpUser:ftpUser,
                        ftpPass:ftpPass,
                        ftpPort:ftpPort,
                        ftpPath:ftpPath
                    }
                });

                testRequest.done(function(r) {
                    if(r == 'success') r = '<span style=\"color:green;font-weight:bold;\">".lang('ftp_test_remote_success')."</span><br />".lang('ftp_test_remote_path_success')."';

                    $('#ftp_test_response').html(r).show();

                    $('#ftp_test').html('".lang('ftp_test_button')."').removeClass('disable').attr('disabled', false);
                });

                testRequest.fail(function( jqXHR, textStatus, errorThrown) {
                    var errorMessage = '';
                    var errorResponse = JSON.parse(jqXHR.responseText);

                    if(errorResponse.error) errorMessage = errorResponse.error;

                    $('#ftp_test_response').html('<div style=\"background-color:#ff8082;padding:10px;border:1px solid #ff0000;\"><strong>'+textStatus+'</strong><br />'+errorThrown+'<br /><br />'+errorMessage+'</div>').show();

                    $('#ftp_test').html('".lang('ftp_test_button')."').removeClass('disable').attr('disabled', false);
                });
            });
        });");

        return $form;
    }

    public function test_ftp()
    {
        ee()->load->library('ftp');

        ee()->ftp->debug = true;

        ee()->ftp->connect(array(
            'hostname' => ee()->input->post('ftpHost'),
            'username' => ee()->input->post('ftpUser'),
            'password' => ee()->input->post('ftpPass'),
            'port' => ee()->input->post('ftpPort')
        ));

        if (!empty(ee()->input->post('ftpPath'))) {
            ee()->ftp->changedir(ee()->input->post('ftpPath'));
        }

        $testFileName = 'safeharbor_test_file_'.uniqid().'.txt';

        // Create a file to test if it's writable.
        if (!ee()->ftp->upload(PATH_THIRD.'safeharbor/safeharbor_test_file.txt', $testFileName)) {
            die('{"error":"'.lang('ftp_error_remote_path').'"}');
        }

        // Delete our test file.
        if (!ee()->ftp->delete_file($testFileName)) {
            die('{"error":"'.lang('ftp_error_remote_delete_test').'"}');
        }

        ee()->ftp->close();

        die('success');
    }

    /**
     * sets up an array for the main landing page in module
     *
     * @method getBackupsColData
     * @param  int $limit Sets the number of backups we want to display data for
     * @return array formatted for the core table helper in EE
     */
    private function getBackupsColData($limit = false)
    {

        // grab the db rows
        $backups = ee('Model')->get('safeharbor:Backups')->order('start_time', 'desc')->all();

        ee()->load->helper(array('number','date'));

        $col_data = array();

        // loop over the backups and populate the display rows
        foreach ($backups as $key => $backup) {
            // Start Date
            $col_data[$key][] = '<nobr>'.date('Y-m-d g:ia', $backup->start_time).'</nobr>';
            // Status
            $col_data[$key][] = ($backup->end_time ? lang('backup_status_complete') : lang('backup_status_inprogress'));
            // Type
            $col_data[$key][] = ucwords($backup->backup_type);
            // Duration
            $col_data[$key][] = '<nobr>'.str_replace(array('seconds', 'minutes'), array('sec', 'min'), timespan($backup->start_time, $backup->end_time)).'</nobr>';

            // Filesize
            if (empty($backup->backup_size)) {
                $backup->backup_size = 0;
            }
            if (empty($backup->backup_dbsize)) {
                $backup->backup_dbsize = 0;
            }
            $col_data[$key][] = '<nobr>'.byte_format(round($backup->backup_size * pow(2, 30))).' / '.byte_format(round($backup->backup_dbsize * pow(2, 30))).'</nobr>'; // 2^30 == 1024 * 1024 * 1024 == 2^10 * 2^10 * 2^10

            // Backup note
            $bn = $backup->getProperty('note');
            $note_html  = '';
            $note = $backup->note;

            if (empty($note)) {
                $note_html .= '<span class="toggle-note"><a class="add-note btn action" href="#">'.lang('backup_add_note').'</a></span>';
            } else {
                $note_html .= '<span class="toggle-note"><span>'.$backup->note.'</span><a class="edit btn action" href="#">'.lang('backup_edit').'</a></span>';
            }
            $note_html .= '<div class="form-note" style="display:none;">';
            $note_html .= form_open(ee('CP/URL', 'addons/settings/safeharbor/note/'.$backup->backup_id));
            $note_html .= '<div class="textfield">'.form_input('note', $backup->note).'</div>';
            $note_html .= '<div class="buttons"><span class="button">'.form_submit(array('name'=>'submit', 'value'=>lang('backup_save_note'), 'class'=>'submit btn')).'</span> <span class="button"><a class="submit" href="#">'.lang('backup_cancel').'</a></span></div>';
            $note_html .= form_close();
            $note_html .= '</div>';

            // add note to this backup
            $col_data[$key][] = $note_html;

            // Download

            // build the url for the download
            $download_url = ee('CP/URL', 'addons/settings/safeharbor/download/' . $backup->backup_id . '/full');
            $download_SQL_url = ee('CP/URL', 'addons/settings/safeharbor/download/' . $backup->backup_id . '/sql');

            // If the backup isn't finished yet, don't show download buttons.
            if (!$backup->end_time) {
                $downloads = lang('backup_running');
            } else {
                $downloads = '<a href="' . $download_url . '" class="btn">' . lang('download_files') . '</a> ';
                $downloads .= '<a href="' . $download_SQL_url . '" class="btn">' . lang('download_sql') . '</a>';
            }

            $col_data[$key][] = '<nobr>'.$downloads.'</nobr>';

            //
            // Actions
            //

            $actions = '';
            // build the url for the search
            // $search_url = ee('CP/URL', 'addons/settings/safeharbor/search/' . $backup->backup_id);
            // build the url for the delete
            $delete_url = ee('CP/URL', 'addons/settings/safeharbor/delete/' . $backup->backup_id);

            // $actions .= '<a href="' . $search_url . '">' . lang('search') . '</a>';
            // $actions .= ' | ';
            $actions .= '<a href="' . $delete_url . '" onclick="return confirm(\'Are you sure you want to delete this backup?\');" class="btn action btn-warning">' . lang('delete') . '</a>';

            $col_data[$key][] = $actions;
        }

        return $col_data;
    }

    private function index_backup($backup_id)
    {
        $base_path = $this->backupHelper->_get_storage_path('base');
        $backup = $this->backupHelper->get_backups('1', array('backup_id' => $backup_id));
        $backup = $backup[0];

        if ($backup['status'] != 'current_full') {
            $backup_path = $this->backupHelper->_get_storage_path('old');
        } else {
            $backup_path = $this->backupHelper->_get_storage_path('current');
        }

        $full_file_path = $backup_path . $backup['name'];

        if (file_exists($full_file_path)) {
            if (chdir($backup_path)) {
                $command = 'tar -ztf '.$backup['name'].'';
            }
        } else {
            if ($backup['status'] != 'current_full') {
                $backup_path = $this->backupHelper->_get_storage_path('default_old');
            } else {
                $backup_path = $this->backupHelper->_get_storage_path('default_current');
            }

            if (chdir($backup_path)) {
                $command = 'tar -ztf '.$backup['name'].'';
            } else {
                ee()->session->set_flashdata('message_failure', ee()->lang->line('index_failed'));
                $error = true;
            }
        }

        if (empty($error)) {
            $files = shell_exec($command);

            // switching to an array
            $files = explode("\n", $files);
            $i=0;
            foreach ($files as $key => $file) {
                ee()->db->set(array('backup_id' => $backup_id, 'file' => $file));
                ee()->db->insert('safeharbor_indexes');
            }
        }
    }

    /**
     * Adds a modal for the view... with out content in it
     *
     * @method addModal
     * @param  strin $name Name we want the modal to have
     */
    public function addModal($name)
    {
        // EEHarbor leaves all contents blank so we can load it form aJax
        $modal_vars = array('name' => 'modal-'.$name, 'contents' => '');
        $modal = ee('View')->make('ee:_shared/modal')->render($modal_vars);
        ee('CP/Modal')->addModal('modal-'.$name, $modal);
    }

    /**
     * Sends data to the modal request and dies so we don't get the whole CP in our modal
     *
     * @method sendModalContent
     * @param  strig $content any content we want... txt or HTML etc
     * @return echo's content and exites
     */
    public function sendModalContent($content)
    {
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        echo $content;
        exit;
    }
}

/* End of File: mcp.safeharbor.php */
