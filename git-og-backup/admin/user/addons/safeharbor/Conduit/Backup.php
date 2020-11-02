<?php

namespace EEHarbor\Safeharbor\Conduit;

class Backup
{
    public $settings;

    public function __construct()
    {
    }

    public function initializeSettings()
    {
        $this->settings = ee('Model')->get('safeharbor:Settings')->first();

        $this->storage_path = PATH_THIRD.'safeharbor/backups/';

        if (! $this->settings) {
            // Set default settings
            $this->settings = ee('Model')
                ->make(
                    'safeharbor:Settings',
                    array(
                        'site_id'                   => ee()->config->config['site_id'],
                        'auth_code'                 => strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8)),
                        'off_site_backup_auth_code' => '',
                        'time_diff'                 => 0,
                        'db_backup'                 => 'command',
                        'notify_email_address'      => '',
                        'backup_time'               => '2:30',
                        'backup_plan'               => '',
                        'time_saved'                => '',
                        'backup_space'              => '1',
                        'backup_path'               => FCPATH,
                        'storage_path'              => $this->storage_path,
                        'transfer_type'             => 'http',
                        'disable_remote'            => false,
                        'amazon_s3_enabled'         => 'n',
                        'amazon_s3_access_key'      => '',
                        'amazon_s3_secret'          => '',
                        'amazon_s3_bucket'          => '',
                        'amazon_s3_endpoint'        => '',
                        'ftp_enabled'               => 'n',
                        'ftp_username'              => '',
                        'ftp_password'              => '',
                        'ftp_host'                  => '',
                        'ftp_port'                  => 21,
                        'ftp_path'                  => '',
                    )
                );

            $this->settings->save();
        }

        return $this->settings;
    }

    public function get_current_backup_file()
    {
        // ee()->db->reconnect();
        ee()->db->select('name');
        ee()->db->order_by('backup_id', 'desc');
        $results = ee()->db->get('safeharbor_backups', 1);
        $results = $results->result();

        if (!empty($results)) {
            return $results[0]->name;
        }
    }

    public function remove_backup_file($file)
    {

        // this function can be switched to use the codeigniter helper
        // ee()->load->helper('file');
        //delete_files($backup_path_current.$riw['name']);

        if (!empty($file)) {
            // check if file exists
            if (file_exists($file)) {
                // delete the file
                if (@unlink($file)) {
                    // make sure the file was deleted
                    if (!file_exists($file)) {
                        return true;
                    } else {
                        // file was not deleted
                        return false;
                    }
                } else {
                    // file could not be deleted
                    return false;
                }
            } else {
                // file not found
                return true;
            }
        }

        return false;
    }


    public function sync_backups_db()
    {
        // $backup_path = $this->_get_storage_path('base');
        // get path  were going to start with the current path

        // dont really want to run all of these each time.  But we will to account for a changed directory location
        $backup_path['current'] = $this->_get_storage_path('current');
        $backup_path['default_current'] = $this->_get_storage_path('default_current');
        $backup_path['old'] = $this->_get_storage_path('old');
        $backup_path['default_old'] = $this->_get_storage_path('default_old');
        if ($backup_path['current'] == $backup_path['default_current']) {
            unset($backup_path['default_current']);
        }
        if ($backup_path['old']  == $backup_path['default_old']) {
            unset($backup_path['default_old']);
        }

        $this->manage_backup_files($backup_path);
    }

    // this function takes an array of paths to check
    public function manage_backup_files($paths)
    {
        ee()->load->helper('file');
        $prefx = ee()->db->dbprefix;

        // were going to go ahead and check too many backups being in the current backup folder

        foreach ($paths as $type => $path) {
            $files[$type] = get_filenames($path);

            if (($type == 'current' || $type == 'default_current')&& !empty($files[$type]) && count($files) > 1) {
                // we have more then 1 file in the current backups folder we need to go ahead and move the others over to the old_backups folder
                // then remove it from the current list
                $results = ee()->db->get_where('safeharbor_backups', array('status' => 'current_full'));

                if ($results->num_rows() > 0) {
                    $results = $results->result_array();

                    //this means we have our current backup, this is the one that gets to stay...the rest we will move.
                    foreach ($results as $row) {
                        if (!empty($row)) {
                            foreach ($files[$type] as $key => $file_name) {
                                if ($row['name'] != $file_name) {
                                    if (@rename($path.$file_name, $paths['default_old'].$file_name)) {
                                        unset($files[$type][$key]);
                                    } else {
                                        // theres been an error... should send email.
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($files[$type])) {
                foreach ($files[$type] as $id => $name) {
                    $files_quoted_for_query[] = '"'.$name.'"';
                }

                $query_file_names = implode(',', $files_quoted_for_query);

                $site_id = ee()->config->config['site_id'];

                $sql = 'SELECT * FROM '.$prefx.'safeharbor_backups WHERE site_id = '.$site_id.' AND name IN('.$query_file_names.')';
                $backup_data = ee()->db->query($sql);

                if ($backup_data->num_rows() > 0) {
                    $backup_data = $backup_data->result_array();

                    foreach ($backup_data as $key => $row) {
                        $names[] = $row['name'];

                        if ($row['status'] == 'current_db_only' || $row['status'] == 'archived') {
                            // doing a little global clean up here
                            $ext = substr($row['name'], -4, 4);

                            // cleaning up any remaining .sql files.  These get left behind if theres permissinons errors, or site errors while running a bacup
                            if ($ext == '.sql') {
                                $current = time();
                                $diff = $current - $row['start_time'];

                                // 7200 = seconds in 2 hours
                                // this means that the DB has been sitting around for over 2 hours.
                                // This represents a dead backup process.  We can go ahead and delete this DB
                                if ($diff > 7200) {
                                    $this->_remove_backup($path.$row['name'], $row['backup_id']);
                                }
                            } elseif ($ext == '.tgz') {
                                // this is the backup type we expect
                                if ($row['status'] == 'Purged') {
                                    $this->_remove_backup($path.$row['name'], $row['backup_id']);
                                }
                            }
                        }
                    }
                }
                //end foreach
            }
        }

        // Putting the current file backups file name in to variable. to compare below.  Sometimes when a backup completes REALLY fast, we find the .tgz file before the db is updated
        // and delete it, so were going to run a check against it
        $current_backup_name = $this->get_current_backup_file();
        $current_backup_parts = explode(".", $current_backup_name);

        $all_files = array();

        //put all files into a single list to check if we hae any extra files in the directories
        foreach ($files as $key => $file_list) {
            if (is_array($file_list) && !empty($names)) {
                $files_still_on_server = array_diff($file_list, $names);
            }

            if (!empty($files_still_on_server)) {
                $path_to_delete = $this->_get_storage_path($key);

                foreach ($files_still_on_server as $id => $file) {
                    $file_name = explode('.', $file);

                    if ($file_name[0] != $current_backup_parts[0]) {
                        // Commented out so that .sql file will not be removed
                        // $test = $this->_remove_backup($path_to_delete.$file);
                    }
                }
            }

            if (is_array($file_list)) {
                foreach ($file_list as $name) {
                    $all_files[] = $name;
                }
            }
        }

        //run check on all files that are marked as not purged
        $results = ee()->db->get_where('safeharbor_backups', array('status' => 'archived'));

        if ($results->num_rows() > 0) {
            $results = $results->result_array();

            foreach ($results as $key => $backup) {
                // if the files aren't on the server, mark them as perged
                if (empty($all_files) || !is_array($all_files) || !in_array($backup['name'], $all_files)) {
                    ee()->db->delete('safeharbor_backups', array('backup_id' => $backup['backup_id']));
                    ee()->db->delete('safeharbor_indexes', array('backup_id' => $backup['backup_id']));
                }
            }
        }

        //run check to see if we have differentials left over from a full that's been deleted
        // eventually we can refactor this section of the method as a whole to work from 1 query on the DB that gets updated to reflect the changes in the DB
        // for now we'll go ahead and query the DB to get the *updated* data
        ee()->db->select('sb.*, sb2.status AS full_status');
        ee()->db->from('safeharbor_backups as sb');
        ee()->db->join('safeharbor_backups AS sb2', 'sb.full_backup_id = sb2.backup_id', 'inner');
        ee()->db->where(array(
                'sb.status' => 'archived',
                'sb.backup_type' => 'differential',
            ));
        ee()->db->or_where(array('sb.status' => 'current_full'));
        ee()->db->where('sb.full_backup_id !=', '');
        ee()->db->order_by('sb.start_time', 'ASC');

        $differentials = ee()->db->get();

        if ($differentials->num_rows() > 0) {
            $differentials = $differentials->result_array();
            foreach ($differentials as $key => $diff) {
                if ($diff['status'] == 'current_full') {
                    $path_to_delete = $this->_get_storage_path('current');
                } else {
                    $path_to_delete = $this->_get_storage_path('old');
                }

                if ($diff['full_status'] == 'Purged') {
                    if ($this->_remove_backup($path_to_delete.$diff['name'])) {
                        $data = array('status' => 'Purged');
                        ee()->db->where('name', $diff['name']);
                        ee()->db->update('safeharbor_backups', $data);
                    }
                }
            }
        }
    }

    public function get_backups($limit=null, $where=null)
    {
        // current code gets all backups from the DB
        $site_id = ee()->config->config['site_id'];

        ee()->db->order_by('backup_id', 'desc');
        ee()->db->where(array('site_id'=>$site_id));
        ee()->db->where_not_in('status', array('current_db_only', 'Unk Error'));
        if (!empty($where)) {
            $backups = ee()->db->where($where);
        }
        ee()->db->limit($limit);
        $backups = ee()->db->get('safeharbor_backups');

        return $backups->result_array();
    }

    /**
     * Builds out module directory structure
     *
     * @method create_dir_structure
     * @param  string $backups_directory Server path to store backups
     * @param  int $install if this is the initial install
     * @return flash data
     */
    public function create_dir_structure($backups_directory = '')
    {
        $return = false;
        // add a check to see if the directory already exists on the intial install.

        if (empty($backups_directory)) {
            $backups_directory = PATH_THIRD.'safeharbor/backups/';
        }

        @mkdir($backups_directory);

        $current_backup_directory = $backups_directory.'current_backup/';
        @mkdir($current_backup_directory);

        $old_backups_directory = $backups_directory.'old_backups/';
        @mkdir($old_backups_directory);

        $htaccess = $backups_directory.'.htaccess';
        $written = @file_put_contents($htaccess, 'deny from all');

        // @TODO update the flashdata to the new EE way.
        if (!empty($written)) {
            $return = true;
        }

        return $return;
    }

    public function _remove_backup($file, $backup_id = '')
    {
        if ($this->remove_backup_file($file)) {
            //now we need to remove the entry from the db. if we have a backup_id
            if (!empty($backup_id)) {
                ee()->db->delete('safeharbor_backups', array('backup_id' => $backup_id));
                // remove cache
                ee()->db->delete('safeharbor_indexes', array('backup_id' => $backup_id));
            }

            return true;
        } else {
            //theres been an error... Send email here.
            return false;
        }
    }
    // function takes a param and returns the approprate path for storage locations
    public function _get_storage_path($type = 'parent')
    {
        $this->settings = $this->initializeSettings();

        if ($type == 'old') {
            return $this->settings->storage_path.'old_backups/';
        } elseif ($type == 'current') {
            return $this->settings->storage_path.'current_backup/';
        } elseif ($type == 'base') {
            return $this->settings->storage_path; //.'safeharbor_backups/';
        } elseif ($type == 'default_old') {
            return PATH_THIRD.'safeharbor/backups/old_backups/';
        } elseif ($type == 'default_current') {
            return PATH_THIRD.'safeharbor/backups/current_backup/';
        } else {
            return false;
        }
    }
}
