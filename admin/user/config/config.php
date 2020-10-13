<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['is_system_on'] = 'y';
$config['cache_driver'] = 'file';
$config['doc_url'] = 'http://expressionengine.com/user_guide/';
$config['index_page'] = '';
$config['app_version'] = "X.X.X"; // Fill version number here

$system_folder                  = "system";

$protocol                       = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
$base_url                       = $protocol . $_SERVER['HTTP_HOST'];
$base_path                      = $_SERVER['DOCUMENT_ROOT'];

$images_folder                  = "images";
$images_path                    = $base_path . "/" . $images_folder;
$images_url                     = $base_url . "/" . $images_folder;


$config['install_lock']         = "";
$config['cp_url']               = $base_url.'/'.$system_folder.'/index.php';
$config['doc_url']              = 'http://expressionengine.com/user_guide/';
$config['is_system_on']         = 'y';
$config['allow_extensions']     = 'y';
$config['site_label']           = 'Site Label';
$config['site_name']            = 'Site Name';
$config['cookie_prefix']        = '';

$config['profile_trigger']      = substr(md5(microtime()),rand(0,26),8);

$config['disable_all_tracking'] = 'y'; # y/n

$config['index_page']           = "";
$config['base_url']             = $base_url . "/";
$config['site_url']             = $config['base_url'];
$config['cp_url']               = $config['base_url'].$system_folder."/index.php";
$config['theme_folder_path']    = $base_path . "/themes/";
$config['theme_folder_url']     = $base_url . "/themes/";
$config['cp_theme']             = "default";


$config['show_profiler']        = 'n'; # y/n
$config['template_debugging']   = 'n'; # y/n
$config['save_tmpl_files']      = "y";  # y/n
$config['debug']                = "1"; # 0: no errors shown. 1: Errors shown to Super Admins. 2: Errors shown to everyone.
$config['enable_sql_caching']   = 'n'; # Cache Dynamic Channel Queries?
$config['email_debug']          = 'n'; # y/n


switch($_SERVER['HTTP_HOST']) {

    case 'newjtcc.local':
        $dbConnection = array (
            'hostname' => 'localhost',
            'username' => 'root',
            'password' => 'root',
            'database' => 'jtcc_dev_local',
                );
    break;

    case 'dev.jtcc.edu':
        $dbConnection = array (
            'hostname' => 'localhost',
            'username' => 'jtcc_dev',
            'password' => 'VdleL7N_krUS',
            'database' => 'jtcc_dev',
                );
    break;
}

$config['database'] = array (
  'expressionengine' => $dbConnection
);

$config['enable_devlog_alerts']      = 'n';
$config['app_version'] = '5.3.2';
$config['encryption_key'] = '78e307bb286224bd336e71417c800997eb9612a5';
$config['session_crypt_key'] = 'f4b860726dbfee5bb8dddda232142261abbb8550';
$config['share_analytics']           = 'y';
$config['show_ee_news']              = 'y';
$config['multiple_sites_enabled']    = 'n';

// END EE config items