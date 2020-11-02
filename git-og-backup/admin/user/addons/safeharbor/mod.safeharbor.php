<?php

require_once 'libraries/Request.php';
require_once 'libraries/S3.php';
require_once 'Conduit/Backup.php';

use EEHarbor\Safeharbor\Conduit\Backup;
use EEHarbor\Safeharbor\FluxCapacitor\Base\Mod;

class Safeharbor extends Mod
{
    public $return_data = null;
    public $settings = array();
    public $dump_file = '';

    protected $db_host;
    protected $db_user;
    protected $db_pass;
    protected $db_name;

    protected $backup_path;
    protected $log = array();
    protected $error = '';

    public function __construct()
    {
        parent::__construct();

        $this->backupHelper = new Backup;
        $this->settings = $this->backupHelper->initializeSettings();
        $this->settingsArray = $this->settings->toArray();

        $this->storage_path = PATH_THIRD.'safeharbor/backups/';

        $this->db_name = ee()->db->database;
        $this->db_host = ee()->db->hostname;
        $this->db_user = ee()->db->username;
        $this->db_pass = ee()->db->password;
    }

    public function getBackupPath()
    {
        return $this->backup_path;
    }

    public function tripwire()
    {
        ee()->db->select_max('start_time');
        $query = ee()->db->get('safeharbor_backups');

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if ($row->start_time == null) {
                    $this->trip();
                } else {
                    $curday = date('z,Y');
                    $lastday = date('z,Y', $row->start_time);
                    if ($curday != $lastday && ((int) $this->settingsArray['seconds_to_run']) <= (time() - strtotime("today"))) {
                        $this->trip();
                    }
                }
            }
        } else {
            $this->trip();
        }
        return "";
    }

    private function trip()
    {
        $failed = ee()->db->where(
            array(
                'message'=>'FAILED',
                'date'=>date('d/m/Y')
            )
        )->or_where(
            array(
                'message'=>'RUNNING',
                'date'=>date('d/m/Y')
            )
        )->get('safeharbor_messages', 1)->num_rows();

        if ($failed) {
            return;
        }

        ee()->db->insert('safeharbor_messages', array(
            'site_id' => ee()->config->config['site_id'],
            'message' => 'RUNNING',
            'date' => date('d/m/Y')
        ));

        $cron = $this->settings->get__cron_url();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $cron);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        curl_exec($ch);
        curl_close($ch);
    }

    public function _backup_cron()
    {
        $type = ee()->input->get('type');

        if ($type === 'full') {
            $is_full = true;
        } else {
            $is_full = false;
        }

        $this->backup_db($is_full);

        $backup_data = $this->backup_files($is_full);

        if (empty($backup_data)) {
            die(json_encode(array('success' => false, 'message' => 'Failed creating backup data')));
        }

        $backup_file = $backup_data['file_name'];
        $backup_path = $this->backupHelper->_get_storage_path('current') . $backup_file;

        $this->_send_backup_to_s3($backup_file, $backup_path);
        $this->_send_backup_to_ftp($backup_file, $backup_path);

        // send email
        $this->send_backup_email();

        //update backup local status
        $data = array('local' => '1');
        ee()->db->where('name', $backup_file);
        ee()->db->update('safeharbor_backups', $data);

        die(json_encode(array('success' => true)));
    }

    private function send_backup_email()
    {
        // load helpers
        ee()->load->helper('number');

        $latest_backup = $this->backupHelper->get_backups(1);

        if (!empty($latest_backup)) {
            $latest_backup = $latest_backup[0];
            $backup = $latest_backup;

            $email_data['site'] = str_replace('index.php', '', ee()->functions->fetch_site_index());
            $email_data['date'] = date('F j, Y');
            $email_data['backup_size'] = byte_format(round($latest_backup['backup_size']*(1024*1024*1024)));
            $email_data['backup_filename'] = $latest_backup['name'];
            $email_data['backup_md5'] = $latest_backup['md5_hash'];
            $email_data['backup_type'] = ucfirst($latest_backup['backup_type']);

            $email_data['backup_time_start'] = date('F j, Y g:ia', (($this->settingsArray['time_diff']*3600) + $latest_backup['start_time']));

            // format start time
            if (ee()->config->item('app_version') < '260') {
                $email_data['backup_time_start'] = date('F j, Y g:ia', (($this->settingsArray['time_diff']*3600) + $latest_backup['start_time']));
            } else {
                $email_data['backup_time_start'] = ee()->localize->human_time($backup['start_time']);
            }

            if (ee()->config->item('app_version') < '260') {
                $email_data['backup_time_end'] = date('F j, Y g:ia', (($this->settingsArray['time_diff']*3600) + $latest_backup['end_time']));
            } else {
                $email_data['backup_time_end'] = ee()->localize->human_time($backup['end_time']);
            }



            $email_data['backup_database_mode'] = $latest_backup['db_backup_mode'];

            // format backup duration
            ee()->load->helper(array('number','date'));
            $email_data['backup_time_total'] = timespan($latest_backup['start_time'], $latest_backup['end_time']);

            // $hrs = (int)date('G', $latest_backup['backup_time']);
            // $min = (int)date('i', $latest_backup['backup_time']);
            // $sec = (int)date('s', $latest_backup['backup_time']);

            // $email_data['backup_time_total'] = (empty($sec) ? '' : $sec.' second'.($sec > 1 ? 's ' : ' '));

            // if( !empty($hrs) OR !empty($min) )
            // {
            //  $email_data['backup_time_total']  = (empty($hrs) ? '' : $hrs.' hour'.($hrs > 1 ? 's ' : ' '));
            //  $email_data['backup_time_total'] .= (empty($min) ? '' : $min.' minute'.($min > 1 ? 's ' : ' '));
            // }

            // status of local backup
            if (!empty($latest_backup['local'])) {
                $email_data['backup_status_local'] = 'Completed Successfully.';
            } else {
                $email_data['backup_status_local'] = 'Failed.';
            }

            // status of remote backup (eeharbor service)
            // if( !empty($latest_backup['backup_transfer_failed']) )
            // {
            //  $email_data['backup_status_remote'] = "Not Configured.\n\nUpgrade at http://eeharbor.com/safeharbor/ to receive off-site backups.";
            // }
            // else
            // {
            //  $email_data['backup_status_remote'] = 'Completed Successfully.';
            // }

            // status of amazon s3 backup
            if (!empty($latest_backup['transferred_amazon_s3'])) {
                $email_data['backup_status_amazon_s3'] = 'Completed Successfully.';
            } elseif (empty($this->settingsArray['amazon_s3_enabled'])) {
                $email_data['backup_status_amazon_s3'] = 'Not Configured';
            } else {
                $email_data['backup_status_amazon_s3'] = 'Failed.';
            }

            // status of ftp backup
            if (!empty($latest_backup['transferred_ftp'])) {
                $email_data['backup_status_ftp'] = 'Completed Successfully.';
            } elseif (empty($this->settingsArray['ftp_enabled'])) {
                $email_data['backup_status_ftp'] = 'Not Configured';
            } else {
                $email_data['backup_status_ftp'] = 'Failed.';
            }

            // build the plain text email
            $plain_message  = '';
            $plain_message .= "Safe Harbor Backup Report\n\n";
            $plain_message .= "Date: ".$email_data['date']."\n";
            $plain_message .= "Site: ".$email_data['site']."\n\n";
            $plain_message .= "======================================================================\n\n";
            $plain_message .= "Local Backup Status:  ".$email_data['backup_status_local']."\n";
            // $plain_message .= "Remote Backup Status: ".$email_data['backup_status_remote']."\n";
            $plain_message .= "Amazon S3 Backup Status: ".$email_data['backup_status_amazon_s3']."\n";
            $plain_message .= "FTP Backup Status: ".$email_data['backup_status_ftp']."\n\n";
            $plain_message .= "======================================================================\n\n";
            $plain_message .= "Backup File: ".$email_data['backup_filename']."\n";
            $plain_message .= "Backup Size: ".$email_data['backup_size']."\n\n";
            $plain_message .= "Backup Type: ".$email_data['backup_type']."\n";
            $plain_message .= "Backup Started:   ".$email_data['backup_time_start']."\n";
            $plain_message .= "Backup Completed: ".$email_data['backup_time_end']."\n\n";
            $plain_message .= "Backup completed in ".$email_data['backup_time_total']."\n\n";
            $plain_message .= "Database Backup Method: ".$email_data['backup_database_mode']."\n\n";
            $plain_message .= "======================================================================\n\n";
            $plain_message .= "Thank you for using Safe Harbor!\n\n";
            $plain_message .= "If you have any questions or comments, feel free to send us an email at help@eeharbor.com\n\n";
            $plain_message .= "http://eeharbor.com\n";
            $plain_message .= "http://twitter.com/eeharbor\n";

            $html_message = ee()->load->view('email', $email_data, true);

            ee()->load->library('email');
            ee()->load->helper('text');

            ee()->email->initialize(array('mailtype' => 'html'));

            // Get the FROM address from EE itself.
            $emailFrom = ee()->config->item('webmaster_email');

            // If the from address isn't set, default to our help email.
            if (empty($emailFrom)) {
                $emailFrom = 'help@eeharbor.com';
            }

            ee()->email->from($emailFrom);
            ee()->email->to($this->settingsArray['notify_email_address']);
            ee()->email->subject('Safe Harbor Backup Report: '.$email_data['site']);
            ee()->email->message($html_message);
            ee()->email->set_alt_message(word_wrap(entities_to_ascii($plain_message), 70));
            ee()->email->send();
        }
    }

    private function set_backup_failed()
    {
        ee()->db->where('status', 'current_full');
        return ee()->db->update('safeharbor_backups', array('backup_transfer_failed' => 1));
    }

    public function _check_config_paths()
    {
        // do we have path to backup set in a config file?
        $config_path_to_backup = ee()->config->item('safeharbor_path_to_backup');

        if (!empty($config_path_to_backup) && !empty($this->settingsArray['backup_path'])) {
            // are they the same?
            if ($config_path_to_backup !== $this->settingsArray['backup_path']) {
                // config file always wins....
                $this->settingsArray['backup_path'] = $config_path_to_backup;

                // update the db here
                ee()->db->where('site_id', ee()->config->config['site_id']);
                ee()->db->update('safeharbor_settings', array('backup_path' => $config_path_to_backup));
            }
        }

        // do we have path to store backups in the config file
        $config_file_storage_path = ee()->config->item('safeharbor_storage_path');

        if (!empty($config_file_storage_path) && !empty($this->settingsArray['storage_path'])) {
            // are they the same?
            if ($config_file_storage_path !== $this->settingsArray['storage_path']) {
                // config file always wins....
                $this->settingsArray['storage_path'] = $config_file_storage_path;

                // update the db here
                ee()->db->where('site_id', ee()->config->config['site_id']);
                ee()->db->update('safeharbor_settings', array('storage_path' => $config_file_storage_path));
            }
        }
    }

    private function send_data($data, $exit = true)
    {
        $data['version'] = $this->version;
        $data = $this->data_encode($data);

        echo $data;
        flush();
        ob_flush();

        if ($exit) {
            exit();
        }
    }

    private function data_encode($data)
    {
        return base64_encode(serialize($data));
    }

    private function backup_db($is_full = 'full')
    {
        $return = null;
        $backup_start = time();

        // need to create a new entry in the DB here... this is to stop other backups from running with the tripwire

        $db_name = ee()->db->database;
        $db_host = ee()->db->hostname;
        $db_user = ee()->db->username;
        $db_pass = ee()->db->password;

        //check to confirm directory exists.
        $this->backupHelper->create_dir_structure($this->settingsArray['storage_path']);

        // create cache folder directories for the DB
        $this->backupHelper->create_dir_structure();

        $database_dump = $this->backupHelper->_get_storage_path('current');
        $archive_status = $this->archive_current_backup();

        if ($archive_status) {
            $database_dump_file = $database_dump.'safeharbor_'.time().'.sql';

            if ($this->settingsArray['db_backup'] == 'command') {
                $command = "mysqldump --add-drop-table --host=$db_host --user='$db_user' --password='$db_pass' $db_name > $database_dump_file";
                $output = shell_exec($command);

                // Hi there, whats your name?  This is Tom from Packet Tide / EEHarbor, if you
                // see this you should shoot me a tweet @TomJaeger :)

                // test to make sure we have a database dump
                $database_error = false;
                if (file_exists($database_dump_file)) {
                    // this should be 10k. A file that might be accidentally
                    // written with an error won't have a file size greater
                    // than 10k, but a database will

                    clearstatcache();

                    if (filesize($database_dump_file) < 10240) {
                        $database_error = true;
                    }
                } else {
                    $database_error = false;
                }

                // if command based database dump fails, fall back to php dump
                if ($database_error) {
                    if ($this->php_backup_db($database_dump_file)) {
                        $return = true;
                    } else {
                        $return = false;
                    }
                } else {
                    $return = true;
                }
            } elseif ($this->settingsArray['db_backup'] == 'php') {
                if ($this->php_backup_db($database_dump_file)) {
                    $return = true;
                } else {
                    $return = false;
                }
            }
        }

        if ($return === true) {
            // update the database
            ee()->db->insert('safeharbor_backups', array(
                'site_id' => ee()->config->config['site_id'],
                'name' => pathinfo($database_dump_file, PATHINFO_BASENAME),
                'start_time' => $backup_start,
                'status' => 'current_db_only',
                'db_backup_mode' => $this->settingsArray['db_backup'],
                'backup_type' => ($is_full ? 'full' : 'differential'),
                'note' => ''
            ));
            ee()->db->delete('safeharbor_messages', array('message' => 'RUNNING'));
        } else {
            ee()->db->insert('safeharbor_messages', array(
                'site_id' => ee()->config->config['site_id'],
                'message' => 'FAILED',
                'date' => date('d/m/Y', $backup_start)
            ));
        }

        return $return;
    }

    private function php_backup_db($temp_db_dump_file)
    {
        $return_data = null;

        $db_name = ee()->db->database;
        $db_host = ee()->db->hostname;
        $db_user = ee()->db->username;
        $db_pass = ee()->db->password;

        $results = ee()->db->query('SHOW TABLES');
        $results = $results->result();

        $tables = array();
        if (!empty($results)) {
            foreach ($results as $row) {
                foreach ($row as $table) {
                    $tables[] = $table;
                }
            }
        }

        if (@touch($temp_db_dump_file)) {
            $fp = fopen($temp_db_dump_file, 'w');

            if (!empty($tables)) {
                foreach ($tables as $table) {
                    // build create table syntax
                    $create_table_results = ee()->db->query('SHOW CREATE TABLE '.$table);

                    $create_table_string  = "\n\n";
                    $create_table_string .= $create_table_results->row('Create Table');

                    $create_table_string  = str_replace('CREATE TABLE `'.$table.'`', 'CREATE TABLE IF NOT EXISTS `'.$table.'`', $create_table_string);
                    $create_table_string .= ";\n";

                    fwrite($fp, $create_table_string);

                    // build insert rows syntax
                    $num_rows = ee()->db->query('SELECT COUNT(*) AS rows FROM '.$table);
                    $num_rows = $num_rows->row('rows');

                    $loop = ceil($num_rows/50);

                    for ($x=0; $x<=$loop; $x++) {
                        $results = ee()->db->query('SELECT * FROM '.$table.' LIMIT '.($x*50).', 50');
                        $results = $results->result();

                        foreach ($results as $row) {
                            fwrite($fp, "\n".ee()->db->insert_string($table, $row).';');
                        }

                        // clean up
                        $results = null;
                        unset($results);
                    }
                }
            }

            fclose($fp);
        }

        clearstatcache();

        if (file_exists($temp_db_dump_file) && filesize($temp_db_dump_file) > 10000) {
            $this->settingsArray['db_backup'] = 'php';
            $return_data = true;
        } else {
            $return_data = false;
        }

        return $return_data;
    }

    private function backup_files($full_backup = false)
    {
        $return_data = null;

        $exclude_files = '*/old_backups/*';

        // set the exclude paths here to nothing
        $exclude_paths = '';

        if (!empty($this->settingsArray['exclude_paths'])) {
            // explode them on the comma seperation
            $exclusions = explode(',', $this->settingsArray['exclude_paths']);

            // loop over the exclusions and trim up whitespace since it's not part of the file name
            foreach ($exclusions as $key => $dir_or_file) {
                $exclusions[$key] = trim($dir_or_file);
            }

            // lets put them in the format needed for tar gz
            $exclude_paths = "--exclude '".implode("' --exclude '", $exclusions)."'";
        }

        $database_file = $this->backupHelper->_get_storage_path('current');

        $database_file .= $this->backupHelper->get_current_backup_file();

        $backup_file = str_replace('sql', 'tgz', $database_file);

        $path_to_backup = $this->get_path_to_backup();

        if ($full_backup) {
            $backup_type = 'FULL';
        } else {
            $backup_type = $this->backup_full_or_differential();
        }

        $last_full_backup = null;
        if ($backup_type != 'FULL') {
            $last_full_backup = $this->get_last_full_backup();
            if (!empty($last_full_backup)) {
                $last_timestamp = date(DATE_RFC2822, $last_full_backup['file_ctime']);
            }
        }

        if (!empty($path_to_backup['last'])) {
            if ($backup_type == 'DIFFERENTIAL') {
                // TODO: change multiple excludes to read a file for excludes, or config param
                // this was the old command tweaked didn't work on all linux boxes
                $command = "tar -pzcf $backup_file --newer-mtime '$last_timestamp' --exclude '$exclude_files' --exclude '*.tgz' $exclude_paths ./";
            } elseif ($backup_type == 'FULL') {
                // TODO: change multiple excludes to read a file for excludes, or config param
                $command = "tar -pzcf $backup_file --exclude '$exclude_files' --exclude '*.tgz' $exclude_paths ./";
            }
        } else {
            $error = "We don't have a backup path set. Please set it in your settings";
        }

        if (chdir($path_to_backup['full_path'])) {
            $output = exec($command);
        } else {
            $error = "We couldn't get into the directory needed to start your backup ERROR: 7709";
        }

        if (file_exists($backup_file)) {
            // remove sql file
            // $database_file = $this->backupHelper->_get_storage_path('current');
            // $database_file .= $this->backupHelper->get_current_backup_file();

            // unlink($database_file);

            $current_backup_start_time = $this->get_current_backup_start_time();

            $data['name'] = $this->backupHelper->get_current_backup_file();
            $data['name'] = str_replace('sql', 'tgz', $data['name']);

            $data['end_time'] = time();

            //check to see if end time and start time are the same.  If so we set the backup time to 1 second
            if ($data['end_time'] == $current_backup_start_time) {
                $data['end_time'] += 1;
            }

            $data['backup_time'] = $data['end_time'] - $current_backup_start_time;

            clearstatcache();
            $file_stat = stat($backup_file);

            if (!empty($file_stat)) {
                $data['file_ctime'] = $file_stat['ctime'];
            } else {
                $data['file_ctime'] = time();
            }


            if ($backup_type == 'DIFFERENTIAL') {
                $data['full_backup_id'] = $last_full_backup['backup_id'];
            }

            $data['status'] = 'current_full';
            $data['backup_size'] = $this->get_backup_size($backup_file);
            $data['backup_dbsize'] = $this->get_backup_size($database_file);
            $data['md5_hash'] = md5_file($backup_file);
            $data['backup_type'] = strtolower($backup_type);

            $data['local'] = 1;
            $data['backup_transfer_failed'] = 0;

            $current_backup_name = $this->backupHelper->get_current_backup_file();

            $this->check_for_status($data['status']);
            $this->change_backup_status($current_backup_name, '', $data);

            unset($return_data);

            $return_data['size'] = $data['backup_size'];
            $return_data['file_name'] = $data['name'];

            if ($backup_type != 'FULL') {
                $last_full_backup = $this->get_last_full_backup();
                $return_data['full_backup_name'] = $last_full_backup['name'];
            }

            $return_data['backup_end_time'] = $data['end_time'];
            $return_data['backup_start_time'] = $current_backup_start_time;
            $return_data['backup_type'] = $data['backup_type'];
        }

        return $return_data;
    }

    private function archive_current_backup()
    {
        $path = $this->backupHelper->_get_storage_path('current');

        $return_data = null;

        $this->backupHelper->sync_backups_db();

        $this->manage_space();

        $old_backups_directory = $this->backupHelper->_get_storage_path('old');

        // list of backups which need to be archived.
        $backups = array();
        $dir_listing = scandir($path);

        foreach ($dir_listing as $key => $val) {
            if ($val == '.' || $val == '..') {
                unset($dir_listing[$key]);
            }
        }

        if (empty($dir_listing)) {
            $path = $this->backupHelper->_get_storage_path('default_current');
            $dir_listing = scandir($path);
        }

        foreach ($dir_listing as $var) {
            if (preg_match('/^safeharbor/', $var)) {
                $backups[] = $var;
            }
        }

        if (!empty($backups)) {
            foreach ($backups as $file) {
                $old_file = $path.$file;

                if (!file_exists($old_file)) {
                    // we will go ahead and change the path to the default
                    $path = $this->backupHelper->_get_storage_path('default_current');
                    $old_file = $path.$file;
                }

                $new_file = $old_backups_directory.$file;

                if (@rename($old_file, $new_file)) {
                    $this->change_backup_status($file, 'archived');
                    $return_data = true;
                } else {
                    $return_data = false;
                }
            }
        } else {
            $return_data = true;
        }

        return $return_data;
    }

    /**
     * manage_space ()
     *
     * This function manages all the space used by backups. It will free up space by
     * deleting older backups if we have reached the limit or if we need more space to
     * run the current backup request. The limit is set in the control panel by the
     * user (which can be updated at any time).
     *
     * @access private
     * @return void
     */
    private function manage_space()
    {
        $backup_path = $this->backupHelper->_get_storage_path('base');

        // convert current space to float value
        $this->settingsArray['backup_space'] = (float) $this->settingsArray['backup_space'];
        $this->settingsArray['backup_space'] = round($this->settingsArray['backup_space']);

        $space_used = $this->size_format($this->get_dir_size($backup_path), 'GB');

        // check if we are utilizing all the allocated space for backups. If
        // user defined limit has been reached, clean up some old backups to
        // make room for the new ones.
        if ($space_used >= $this->settingsArray['backup_space']) {
            $removed = $this->remove_backup();

            if ($removed) {
                $this->manage_space();
            }
        } elseif ($space_used < $this->settingsArray['backup_space']) {
            // check if we have enough space available to run a new backup. If
            // not, delete old backups to make room.
            $space_available = $this->settingsArray['backup_space'] - $space_used;

            // TODO: we need to fix this estimated backup size calculation. If
            // a user was to add a 2GB file since the last backup, it would not
            // be accounted for and we would fall short of our calculation.
            // This would cause the (user defined) alloted space for backups to
            // be surpassed.
            ee()->db->order_by('backup_id', 'desc');
            $current_backup_details = ee()->db->get('safeharbor_backups', 1);
            $current_backup_details = $current_backup_details->row_array();

            if (!empty($current_backup_details)) {
                if ($space_available < $current_backup_details['backup_size']) {
                    if ($this->remove_backup()) {
                        $this->manage_space();
                    }
                }
            }
        }
    }

    private function remove_backup($default = '')
    {
        if (empty($default)) {
            $old_backups_path = $this->backupHelper->_get_storage_path('old');
        } elseif ($default == 'default_old') {
            $old_backups_path = $this->backupHelper->_get_storage_path('default_old');
        }

        // retrieve oldest backup
        ee()->db->where(array(
            'status' => 'archived',
            'backup_type' => 'full'
        ));
        ee()->db->order_by('start_time', 'ASC');
        $oldest_backup = ee()->db->get('safeharbor_backups', 1);
        $oldest_backup = $oldest_backup->row_array();

        if (!empty($oldest_backup)) {
            // check if there are any differential backups related to this full
            // backup. If so, delete them.
            ee()->db->where(array(
                'status' => 'archived',
                'backup_type' => 'differential',
                'full_backup_id' => $oldest_backup['backup_id']
            ));
            ee()->db->order_by('start_time', 'ASC');
            $oldest_differential = ee()->db->get('safeharbor_backups');
            $oldest_differential = $oldest_differential->row_array();

            if (!empty($oldest_differential)) {
                // delete oldest differential
                $file = $old_backups_path.$oldest_differential['name'];

                if ($this->backupHelper->remove_backup_file($file)) {
                    if ((bool)$this->settingsArray['amazon_s3_enabled']) {
                        // remove backup file from s3 bucket
                        $s3 = new s3($this->settingsArray['amazon_s3_access_key'], $this->settingsArray['amazon_s3_secret']);
                        $s3->delete_object('safeharbor-' . $this->settingsArray['auth_code'], $oldest_differential['name']);

                        // cleanup
                        $s3 = null;
                        unset($s3);
                    }

                    if ((bool)$this->settingsArray['ftp_enabled']) {
                        ee()->load->library('ftp');

                        ee()->ftp->connect(array(
                            'hostname' => $this->settingsArray['ftp_host'],
                            'username' => $this->settingsArray['ftp_username'],
                            'password' => $this->settingsArray['ftp_password'],
                            'port' => $this->settingsArray['port'],
                        ));

                        $remote_file = $this->settingsArray['ftp_path'].$oldest_differential['name'];

                        ee()->ftp->delete_file($remote_file);
                        ee()->ftp->close();
                    }
                    // change the status of the backup in database
                    $this->change_backup_status($oldest_differential['name'], 'Purged');

                    // remove search index
                    ee()->db->delete('safeharbor_indexes', array('backup_id' => $oldest_differential['backup_id']));

                    $this->backupHelper->sync_backups_db();

                    return true;
                }
            } else {
                // no more differentials, delete the full backup
                $file = $old_backups_path.$oldest_backup['name'];

                if ($this->backupHelper->remove_backup_file($file)) {
                    if ((bool)$this->settingsArray['amazon_s3_enabled']) {
                        // remove backup file from s3 bucket
                        $s3 = new s3($this->settingsArray['amazon_s3_access_key'], $this->settingsArray['amazon_s3_secret']);
                        $s3->delete_object('safeharbor-' . $this->settingsArray['auth_code'], $oldest_backup['name']);

                        // cleanup
                        $s3 = null;
                        unset($s3);
                    }

                    // if off site ftp backups is enabled go ahead and remove the full backup from there as well.
                    if ((bool)$this->settingsArray['ftp_enabled']) {
                        ee()->load->library('ftp');

                        ee()->ftp->connect(array(
                            'hostname' => $this->settingsArray['ftp_host'],
                            'username' => $this->settingsArray['ftp_username'],
                            'password' => $this->settingsArray['ftp_password'],
                            'port' => $this->settingsArray['port'],
                        ));

                        $remote_file = $this->settingsArray['ftp_path'].$oldest_backup['name'];

                        // remove oldest backup on FTP server
                        ee()->ftp->delete_file($remote_file);
                        ee()->ftp->close();
                    }

                    // change the status of the backup in database
                    $this->change_backup_status($oldest_backup['name'], 'Purged');

                    // remove search index
                    if (empty($oldest_differential['backup_id'])) {
                        ee()->db->delete('safeharbor_indexes', array('backup_id' => $oldest_differential['backup_id']));
                    }

                    $this->backupHelper->sync_backups_db();

                    return true;
                } elseif (!empty($this->settingsArray['storage_path'])) {
                    // call self and change path
                    $this->remove_backup('default_old');
                }
            }
        }

        return false;
    }

    private function get_dir_size($path)
    {
        $total_size = 0;

        if (is_dir($path)) {
            $io = popen('/usr/bin/du -sb '.$path, 'r');
            $total_size = intval(fgets($io, 80));
            pclose($io);
        }
        return $total_size;
    }

    public function change_backup_status($file, $action, $data = "")
    {
        if (empty($data)  && $action != 'Purged') {
            $data = array('status' => $action);
        } elseif ($action == 'Purged') {
            ee()->db->select('backup_id');
            ee()->db->from('safeharbor_backups');
            ee()->db->where('name', $file);
            $results = ee()->db->get();

            if ($results->num_rows() > 0) {
                $backup_id = $results->row('backup_id');

                ee()->db->delete('safeharbor_backups', array('backup_id' => $backup_id));
                ee()->db->delete('safeharbor_indexes', array('backup_id' => $backup_id));
            }


            return true;
        }

        ee()->db->where('name', $file);
        return ee()->db->update('safeharbor_backups', $data);
    }

    private function get_current_backup_start_time()
    {
        // ee()->db->reconnect();
        ee()->db->select('start_time');
        ee()->db->order_by('backup_id', 'desc');
        $results = ee()->db->get('safeharbor_backups', 1);
        $results = $results->result();

        if (!empty($results)) {
            return $results[0]->start_time;
        }
    }

    private function backup_full_or_differential()
    {
        $last_full_backup = $this->get_last_full_backup();
        $next_full_backup = strtotime('+1 week', $last_full_backup['start_time']);

        $current_time = time();

        $return = 'FULL';

        if ($current_time < $next_full_backup) {
            $return = 'DIFFERENTIAL';
        }

        return $return;
    }

    private function get_last_full_backup()
    {
        ee()->db->select('backup_id, name, start_time, file_ctime, status');
        ee()->db->where(array(
            'backup_type' => 'full',
            'status !=' => 'current_db_only',
            // 'status !=' => 'Purged',
        ));
        ee()->db->order_by('start_time', 'desc');
        $last_backup = ee()->db->get('safeharbor_backups', 1);
        $last_backup = $last_backup->result_array();

        if (!empty($last_backup[0]) && $last_backup[0]['status'] == 'Purged') {
            $last_backup[0]['start_time'] = 0;
        }
        if (empty($last_backup[0])) {
            $last_backup[0] = false;
        }

        return $last_backup[0];
    }

    private function get_path_to_backup()
    {
        $return_data = null;

        if (!empty($this->settingsArray['backup_path'])) {
            $return_data['full_path'] = $this->settingsArray['backup_path'];

            $parts = explode('/', $this->settingsArray['backup_path']);

            foreach ($parts as $key => $val) {
                if ($val == "") {
                    unset($parts[$key]);
                }
            }

            $parts = array_values($parts);
            $last = array_pop($parts).'/';

            $return_data['last'] = $last;

            $base_path = '';
            foreach ($parts as $key => $val) {
                $base_path .= '/'.$val;
            }
            $base_path .= '/';

            $return_data['base_path'] = $base_path;
        } else {
            $return_data = false;
        }

        return $return_data;
    }

    private function get_backup_size($file)
    {
        $command = 'ls -l '.$file.' | awk \'{print $5}\'';
        $size = trim(shell_exec($command));

        if ($size) {
            return $this->size_format($size, 'GB');
        } else {
            // ls -l command didn't work
            clearstatcache();
            $size = filesize($file);
            return $this->size_format($size, 'GB');
        }

        return "unk";
    }

    private function size_format($size, $format = 'MB')
    {
        if ($format == 'GB') {
            $size = round($size/(1024*1024*1024), 5);
        } else {
            $size = round($size/(1024*1024), 1);
        }

        return $size;
    }

    private function check_for_status($status)
    {
        $results = ee()->db->get_where('safeharbor_backups', array('status' => $status));
        $results = $results->result();

        if (!empty($results)) {
            foreach ($results as $row) {
                ee()->db->where('backup_id', $row->backup_id);
                ee()->db->update('safeharbor_backups', array('status' => 'Unk Error'));
            }
        }

        return true;
    }

    /**
     * Removes all tables from the DB
     *
     * @method _empty_database
     * @return bool based on if the DB user can preform this action
     */
    private function _empty_database()
    {
        $success = true;

        $mysqli = new mysqli($this->db_host, $this->db_user, $this->db_pass, $this->db_name);

        if ($result = $mysqli->query("SHOW TABLES")) {
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $dropped = $mysqli->query('DROP TABLE IF EXISTS ' . $row[0]);

                if (! $dropped) {
                    $success = false;
                }
            }
        }

        $mysqli->close();

        return $success;
    }


    /**
     * Check that all commands necessary to run exist
     *
     * @method commandsExist
     * @param  string $extention This is the extension of the file. More checks required for .zip and .tgz
     * @return boolean if commands exist
     */
    public function commandsExist($extension = false)
    {
        // make sure we can run a restore successfully
        // 1. check if exec exists. we cannot continue the restore process unless it does
        if (!function_exists('exec')) {
            $this->error = 'Failed. The PHP function exec() is unavailable.';
            return false;
        }

        // 2. check if mysql command is available. we cannot continue the restore process without it
        exec('command -v mysql', $mysql_output);
        if (empty($mysql_output)) {
            $this->error = 'Failed. The mysql command is not available on your server.';
            return false;
        }

        // 3. if our backup is a .zip, make sure the unzip command is available.
        if ($extension === '.zip') {
            // check if unzip exists
            exec('command -v unzip', $unzip_output);
            if (empty($unzip_output)) {
                $this->error = 'Failed. The unzip command is not available on your server.';
                return false;
            }
        }

        // 3. if our backup is a .tgz, make sure the tar command is available.
        if ($extension === '.tgz') {
            // check if unzip exists
            exec('command -v tar', $tar_output);
            if (empty($tar_output)) {
                $this->error = 'Failed. The tar command is not available on your server.';
                return false;
            }
        }

        return true;
    }

    /**
     * Unzip archived sql file
     *
     * @method unzip
     * @param  string $path Directory path of the file
     * @param  array $backup Array of information about the file.
     *              *Must include $backup['name'] as the file name, and $backup['extension'] as the extension.
     * @return boolean if success
     */
    public function unzip($backup)
    {
        // var_dump($backup);
        // identify the database file to be restored. decompress selected backup
        // if needed.
        if ($backup['extension'] === '.tgz') {
            // decompress file
            chdir($this->backup_path);
            exec("tar -pxzf " . $backup['name'].$backup['extension']." ".$backup['name'].'.sql');       // . " 2>&1", $output, $return_var ); <- for verifying command output
            // var_dump($output, $return_var);

            return true;
        } elseif ($backup['extension'] === '.zip') {
            // decompress file
            chdir($this->backup_path);
            exec("unzip " . $backup['name'].$backup['extension']);      // . " 2>&1", $output, $return_var ); <- for verifying command output
            // var_dump($output, $return_var);

            return true;
        } else {
            //no unzipping required.
            return true;
        }
    }


    /**
     * Log events of the backup creation
     *
     * @method _log
     * @param  string $msg The message we would like added to the log
     * @return string Log of backup
     */
    public function _log($msg = null)
    {
        if (!empty($msg)) {
            $this->log[] = date('Y-m-d H:i:s').' --> '.strtolower($msg);
        } else {
            return implode("\n", $this->log);
        }
    }
    private function _send_backup_to_s3($backup_file, $backup_path)
    {
        ee()->load->library('logger');

        //transfer to amazon S3 to be put into a function
        if ((bool)$this->settingsArray['amazon_s3_enabled']) {
            $bucket = isset($this->settingsArray['amazon_s3_bucket'])? $this->settingsArray['amazon_s3_bucket'] : 'safeharbor-' . $this->settingsArray['auth_code'];

            // create bucket to make sure it's there for backup storage
            $S3 = new S3($this->settingsArray['amazon_s3_access_key'], $this->settingsArray['amazon_s3_secret'], $this->settingsArray['amazon_s3_endpoint']);

            $bucket_exists = $S3->bucket_exists($bucket);
            $bucket_create_error = '';

            if (!$bucket_exists) {
                $S3->create_bucket($bucket);
                $bucket_response = $S3->response_info();

                if ($bucket_response['http_code'] == 200) {
                    $bucket_exists = true;
                } else {
                    $bucket_create_error = 'Bucket Create Error:<pre>'.var_export($bucket_response, true).'</pre><br />';
                }
            }

            // cleanup
            $S3 = null;
            unset($S3);

            if ($bucket_exists === true) {
                // transfer backup to amazon s3
                $S3 = new S3($this->settingsArray['amazon_s3_access_key'], $this->settingsArray['amazon_s3_secret'], $this->settingsArray['amazon_s3_endpoint']);
                $create_result = $S3->create_object($bucket, $backup_file, $backup_path);
                $object_response = $S3->response_info();
                $response = $S3->response();
                $response_error = $S3->response_error();

                // cleanup
                $S3 = null;
                unset($S3);

                if ($object_response['http_code'] == 200) {
                    // ee()->db->reconnect();
                    ee()->db->where('name', $backup_file);
                    ee()->db->update('safeharbor_backups', array('transferred_amazon_s3' => 1));
                } elseif ($object_response['http_code'] == 301) {
                    ee()->logger->developer('Safe Harbor: Backup to S3 Failed - Your Endpoint / URL Prefix is incorrect:<br />Bucket: '.$bucket.'<br />Bucket Exists: '.($bucket_exists ? 'true' : 'false').'<br />Entered Endpoint: '.$S3->get_endpoint().'<br />S3 Response Endpoint: '.$create_result->Endpoint.'<br />'.$bucket_create_error.'Save Error: <pre>'.var_export($object_response, true).'</pre>');
                } else {
                    ee()->logger->developer('Safe Harbor: Backup to S3 Failed:<br />Bucket: '.$bucket.'<br />Bucket Exists: '.($bucket_exists ? 'true' : 'false').'<br />'.$bucket_create_error.'Save Error: <pre>'.var_export($object_response, true).'</pre>');
                }
            } else {
                ee()->logger->developer('Safe Harbor: Find/Create S3 Bucket Failed:<br />Bucket: '.$bucket.'<br />Bucket Exists: '.($bucket_exists ? 'true' : 'false').'<br />'.$bucket_create_error);
            }
        }
        //end amazon s3
    }

    private function _send_backup_to_ftp($backup_file, $backup_path)
    {
        // Transfer to FTP needs to be put into a funciton
        if ((bool) $this->settingsArray['ftp_enabled']) {
            ee()->load->library('ftp');

            $creds = array(
                'hostname'  => $this->settingsArray['ftp_host'],
                'username'  => $this->settingsArray['ftp_username'],
                'password'  => $this->settingsArray['ftp_password'],
                'port'      => $this->settingsArray['ftp_port'],
            );

            $res = ee()->ftp->connect($creds);

            $remote_file = $this->settingsArray['ftp_path'].$backup_file;

            // change directory
            if (ee()->ftp->changedir($this->settingsArray['ftp_path'], true)) {
                if (ee()->ftp->upload($backup_path, $backup_file, 'binary', 0775)) {
                    ee()->db->where('name', $backup_file);
                    ee()->db->update('safeharbor_backups', array('transferred_ftp' => 1));
                } else {
                    ee()->db->where('name', $backup_file);
                    ee()->db->update('safeharbor_backups', array('transferred_ftp' => 2));
                }
            } elseif (ee()->ftp->mkdir($this->settingsArray['ftp_path'], 0777)) {
                if (ee()->ftp->changedir($this->settingsArray['ftp_path'], true)) {
                    if (ee()->ftp->upload($backup_path, $backup_file, 'binary', 0775)) {
                        ee()->db->where('name', $backup_file);
                        ee()->db->update('safeharbor_backups', array('transferred_ftp' => 1));
                    } else {
                        ee()->db->where('name', $backup_file);
                        ee()->db->update('safeharbor_backups', array('transferred_ftp' => 2));
                    }
                }
            } else {
                $ftp_failed = 1;
            }

            ee()->ftp->close();
        }
        //end transfer to FTP
    }

    // private function transfer_backup()
    // {
    //  $latest_backup = $this->backupHelper->get_backups(1);
    //  $latest_backup = $latest_backup[0];
    //  $latest_backup = $this->backupHelper->_get_storage_path('current') . $latest_backup['name'];

    //  header('Pragma: public');
    //  header('Expires: 0');
    //  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    //  header('Cache-Control: private', FALSE);

    //  header('Content-Disposition: attachment; filename="'.basename($latest_backup).'";');
    //  header('Content-Type: application/x-tar');
    //  header('Content-Transfer-Encoding: binary');
    //  header('Content-Length: '.@$this->get_backup_size($latest_backup));

    //  return readfile($latest_backup);
    // }
}

/* End of File: mod.safeharbor.php */
