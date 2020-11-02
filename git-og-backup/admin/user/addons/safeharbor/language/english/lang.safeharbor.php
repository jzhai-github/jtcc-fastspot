<?php
$lang = array(

// Required for MODULES page
'safeharbor_module_name' => 'Safe Harbor',
'safeharbor_module_description' => 'Automated, daily backups of your ExpressionEngine website.',

//----------------------------------------


//
// Side Nav
//
'settings'                          =>  'Settings',
'home'                              =>  'Home',


//
// Settings Page
//
'safeharbor_module_name_settings'   =>  'Safe Harbor Settings',

// Table Categories:
'auth_code'                         =>  'Auth Code',
'notify_email_address'              =>  'Notification Email Address',
'backup_space'                      =>  'Local backup space to use in GB. <small>(minimum 1GB)</small>',
'backup_path'                       =>  'Root path of your ExpressionEngine install. <small>(full server path)</small>',
'storage_path'                      => 'Server path to store backups <small>(default is EE cache dir)</small>',
'backup_time'                       =>  'Your local time to execute backup',
'db_backup'                         =>  'DataBase backup options',
    // options:
    'command'                           =>  'MySQLdump',
    'php'                               =>  'PHP',
'trigger_intro'                     =>  'Trigger URL <small>(used by <a href="http://eeharbor.com">http://eeharbor.com</a>)</small>',
'amazon_s3'                         =>  'Amazon S3',
'amazon_s3_enabled'                 => 'Use Amazon S3 to store backups',
'amazon_s3_enabled_label'           => 'Enabled?',
'amazon_s3_access_key'              => 'Access Key ID',
'amazon_s3_secret'                  => 'Secret',
'amazon_s3_bucket'                  => 'Bucket Name',
'amazon_s3_endpoint'                => 'Endpoint / URL Prefix',
'ftp'                               => 'FTP',
'ftp_no_php_ftp'                    => 'You do not have FTP functionality on your server.',
'ftp_enabled'                       => 'Save Backups to another server via FTP',
'ftp_enabled_label'                 => 'Enabled?',
'ftp_username'                      => 'FTP Username',
'ftp_password'                      => 'FTP Password',
'ftp_host'                          => 'FTP Host',
'ftp_port'                          => 'Port',
'ftp_path'                          => 'Remote server path to store backups',
'ftp_test'                          => 'FTP Connection Test',
'ftp_test_button'                   => 'Test FTP Connection',
'ftp_test_button_waiting'           => 'Testing...',
'ftp_test_remote_success'           => 'Connection Successful.',
'ftp_test_remote_path_success'      => 'Backup Folder OK',
'ftp_test_failed'                   => 'FAILED: Could not complete connection:',
'ftp_error_remote_path'             => 'Make sure your Remote Server Path is correct and is writable',
'ftp_error_remote_delete_test'      => 'We were unable to delete the Safe Harbor test folder. Please check your FTP path and folder permissions.',

// Banner messages
'safeharbor_config_save_success'                        => 'Settings Saved!',
'safeharbor_config_save_failure'                        => 'Unable to save configuration',
'backup_path_not_writable_install'                      => 'We were unable to write to your cache folder please check the permissions and save your Safe Harbor settings.',
'backup_path_not_writable'                              => 'We were unable to write to the folder you specified to store backups please check the permissions and save your settings again.',
// validations:
'safeharbor_settings_validation_notify_email_address'   => 'Unable to save configuration: Notification email address is required and must be a valid email address.',
'safeharbor_settings_validation_backup_space'           => 'Unable to save configuration: Backup space must be a number larger than 1 (in GB).',
'safeharbor_settings_validation_backup_path'            => 'Unable to save configuration: Backup Path required.',
'safeharbor_settings_validation_storage_path'           => 'Unable to save configuration: Storage Path required.',
'safeharbor_settings_validation_backup_time_hour'       => 'Unable to save configuration: Backup hour must be an integer between 0 - 23 and is required.',
'safeharbor_settings_validation_backup_time_min'        => 'Unable to save configuration: Backup minutes must be an integer between 0 - 59 and is required.',
'safeharbor_settings_validation_db_backup'              => 'Unable to save configuration: DB Backup must be either PHP or MySQLdump.',
'safeharbor_settings_validation_amazon_s3_enabled'      => 'Unable to save configuration: Please select enabled or disabled for Amazon S3.',
'safeharbor_settings_validation_ftp_enabled'            => 'Unable to save configuration: Please select enabled or disabled for FTP.',
'safeharbor_settings_validation_ftp_port'               => 'Unable to save configuration: FTP Port must be an integer.',


//
// Homepage
//
'backup_full_now'                   =>  'Run Full Backup',
'backup_differential_now'           =>  'Run Differential Backup',
'backup_loading'                    =>  'Starting backup, please wait...',

// Table Headings
'backup_date'                       =>  'Backup Date',
'time_to_backup'                    =>  'Duration',
'backup_status'                     =>  'Status',
'backup_type'                       =>  'Type',
'backup_size'                       =>  'Size (files / db)',
'backup_note'                       =>  'Note',
    'backup_add_note'                   =>  'Add Note',
    'backup_cancel'                     =>  'Cancel',
    'backup_save_note'                  =>  'Save Note',
    'backup_note_save_success'          =>  'Note Saved!',
    'backup_note_save_failure'          =>  'Unable to save note.',
    'backup_edit'                       =>  'Edit',
'download'                          =>  'Download',
    'download_files'                    =>  'Files',
    'download_diff'             =>  'Differential',
    'download_sql'                      =>  'SQL',
'actions'                           =>  'Actions',
    'search'                            => 'Search',
    'delete'                            => 'Delete',

'no_backups'                            =>  'There are currently no backups on file',
'none'                              =>  '',

'backup_start_failed'               => 'Backup Failed to Start',

'backup_status_pending'             => 'Pending...',
'backup_status_inprogress'          => 'In Progress...',
'backup_status_complete'            => 'Complete',

// Download
'backup_running'                    => 'Backup Running',
'download_failed_file_not_found'    => 'The download has failed we were unable to locate your backup',

// Delete
'backup_deleted'                    => 'Backup Deleted',
'backup_delete_failed'              => 'We failed to delete your backup, please manually delete it!',




// "safeharbor_module_name_search"      =>  "Safe Harbor Search Backup",
// "safeharbor_menu"                    =>  "Backup_cp",
// "add_backup"                         =>  "Add a new backup",
// "view_backups"                       =>  "View All Backups",
// "all_backups"                        =>  "All Backups",
// "backup_now"                         =>  "Backup Now",
// "transfer_status_link"               =>  "Transfer Status Link",
// "file_backup_link"                   =>  "File Trigger",
// "db_backup_link"                     =>  "DB Trigger",
// "home"                               =>  "Home",
// "trigger_intro"                      =>  'Trigger URL <small>(used by <a href="http://eeharbor.com">http://eeharbor.com</a>)</small>',

// "no_auth"                            =>  "Please fill out all of the fields below",
// "set_auth_error"                     =>  "There has been an error saving your Auth Code ERROR:1001",
// "update_auth_error"                  =>  "There has been an error saving your Auth Code ERROR:1002",
// "denotes"                            =>  "Please note * denotes a required field",
// "current_backups"                    =>  "Your Current Backups",
// "backup_history"                     =>  "Backup History",
// "time_diff"                          =>  "Your time difference from server in hours.",
// "server_time"                        =>  "Server Time:",
// "local_time"                     =>  "Local Time:",
// "calculated_difference"              =>  "Calculated Difference:",
// "config"                         =>  "Config Parameters:",
// "cant_mk_dir"                        =>  "Cant't create directory needed in the cache directory please make sure permissions are set to 777 on ".PATH_DICT."cache/",
// "chmod_error"                        =>  "Couldn't change the permissions on the backups directory".PATH_DICT."cache/safeharbor_backups",
// "remove"                         =>  "Remove Module",
// "remove_title"                       =>  "Remove Safe Harbor",
// "important"                          =>  "Important!",
// "remove_backups"                 =>  "Please note all local backups will be removed upon uninstall.  If this presents any issues please copy them to a secure location before proceeding",
// "remove_mod"                     =>  "I Understand, Delete my Backups and Remove the Module",
// "test_failed"                        =>  'The directory test failed.  Please place a "deny from all" htaccess file in each folder in your system/cache/safeharbor_backups/ and confirm it is working correctly.',
// "no_write_file"                      =>  "We were unable to write the file to the newly create cache directory please confirm the permissions on your /system/cache directory are set to 777",
// "install"                            =>  "Install",
// "ok_to_install_safeharbor"           =>  "Were good to install",
// "safe_mode_on"                       =>  "PHP is currently running in Safe mode on your server.  This module can't work with Safe mode On.  Please turn it off to continue.",
// "permissions_bad"                    =>  'Your CACHE folder is NOT writeable.  Please change the permissions so the folder is writabe per Ellis Lab please see step 4 <br /><br /><a href="http://expressionengine.com/docs/installation/installation.html" target="_blank">ExpressionEngine Install doc</a>',
// "install_error"                      =>  "Safe Harbor Installation ERROR",
// "error"                              =>  "Error",
// "mod_name_error"                 =>  "Safe Harbor error",
// "chg_permisssions_error"         =>  "Unable to change permissions on items in your cache folder.  Please confirm permissions are set to 777 on the /system/cache/ folder",
// "cant_delete"                        =>  "We were unable to delete a file.  Please check the permissions of your /system/cache/ directory to confirm they are set to 777",
// "cant_create_file"                   =>  "We were unable to create a file in Your /system/cache folder.  Please confirm your permissions are set to 777",
// "base_trigger"                       =>  "Trigger",
// "backup_service"                 =>  "Backup Service Trigger",
// "backup_plan"                        =>  "Backup options",
// "full_on_friday"                 =>  "Full backup Friday incrimentals every other day",
// "full_3"                         =>  "Full backup Monday, Wednesday, Friday",
// "full_weekley"                       =>  "Full backup every Friday (Weekley Backups)",
// "all_full"                           =>  "Full backup everyday",
// "off_site_backup_auth_code"          =>  "Off Site Backup Auth Code",
// "more_then_one_current_backup"       =>  "We found more then one current backup in your cache/current_backup folder.  Please check to make sure it was moved to the old_backups folder correctly.  Also please confirm the permissions are 777 per Ellis Lab.",
// "no_delete_backup"                   =>  "We were unable to delete your onsite backup.  Please delete it yourself from your cache folder.  Taking no action on this can cause your server to run out of space!!!  Also please confirm your cache folder is set to 777",
// "backup_not_file"                    =>  "Your oldest backup was not found to be a file.  Pease confirm your cache folder is set to 777.  Also please delete the following file",
// "bad_size_defined"                   =>  "Please set your alloted space to be used in the Safe Harbor settings",
// "db_backup_not_found"                =>  "We were unable to find your DB backup on the server. Please confirm it was preformed, or manually preform a database backup",
// "cant_remove_sql"                    =>  "We couldn't remove your SQL file in your system/cache/safeharbor_backups/current_backup folder",
// "no_space"                           =>  "Safe Harbor tried to run a backup on your site and found that the backup folder has consumed too much space, and could't bring the folder below the space allowed.  Please increase the space allowed in your settings, or delete one of the existing backups manually.",
// "run_now"                            =>  "Run Off-Site Backup",
// "backup_onsite_now"                  =>  "Run Local Backup",
// "documentation"                      =>  "Documentation",
// "local_backup"                       =>  "Local Only",
// "ssl_test"                           =>  "Test HTTPS",
// "trigger_setting"                    =>  "Trigger Settings:",
// "transfer_type"                      =>  "Transfer Type",
// "download"                           =>  "Download",
// "download_local"                 =>  "Local",
// "download_remote"                    =>  "Remote",
// "remote_transfer_failed"         =>  "Backup failed to transfer to our server",
// "file_name"                          =>  "File Name",
// "md5"                                =>  "MD5 Hash",
// "http"                               =>  "HTTP",
// "https"                              =>  "HTTPS",

// 'test_https'                     => 'Test HTTPS',
// 'test_https_pass'                    => 'HTTPS Test Passed',
// 'test_https_fail'                    => 'HTTPS Test Failed',
// // 'config_save_success'             => 'Configuration Saved!',
// 'config_save_remote_failure'     => 'Failed to save settings on http://eeharbor.com. Make sure your website is publicly accessible.',
// 'backup_running'                 => 'Backup is running',
// 'download_file'                      => 'Download File',
// 'file_path'                          => 'File Path',
// 'view_all_results'                   => 'View All Results',
// '#'                                  => '#',
// 'backup_failed'                      => 'Backup Failed',
// 'index_failed'                       => 'We failed to index your backup. This is usually caused from being unable to access the backup',
// 'download_failed'                    => 'Download Failed',
'cron_url'                          => 'CRON URL',
'eeharbor_remote_scheduling'        => 'EEHarbor Remote Scheduling',
'eeharbor_remote_disabled'          => '<strong>Please Note:</strong> Remote scheduling via EEHarbor is no longer available.<br />For scheduled backups, you must setup a CRON task through your hosting provider or a third-party service.',
// 'disable_remote'                 => 'Disable remote scheduling of backups',
// 'disable_remote_desc'                => '(In order to have automated backups you will need to setup a cron job to execute the CRON URL)',
// 'no'                             => 'No',
// 'yes'                                => 'Yes',
// 'backup_deleted'                 => 'Backup Deleted',
// 'backup_delete_failed'               => 'We failed to delete your backup, please manually delete it!',
// 'delete_differentials'               => '**NOTE deleting this FULL backup will remove all Differentials related to it!',
// 'config_saved_locally'               => 'Configuration Saved Locally!'


// END
'' => ''
);

/* End of File: lang.safeharbor.php */
