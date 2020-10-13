<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['multiple_sites_enabled'] = 'n';
$config['show_ee_news'] = 'n';
// Most code copied from:
// https://u.expressionengine.com/article/creating-a-multi-environment-config-in-expressionengine-5
$config['save_tmpl_files'] = 'y';

$protocol           = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") ? "https://" : "http://";
$base_url           = $protocol . $_SERVER['HTTP_HOST'];
$base_path          = $_SERVER['DOCUMENT_ROOT'];
$system_folder      = "admin";

$images_folder      = "images";
$images_path        = $base_path . "/" . $images_folder;
$images_url         = $base_url . "/" . $images_folder;

$config['cp_url']               = $base_url.'/'.$system_folder.'/index.php';
$config['doc_url']              = 'http://expressionengine.com/user_guide/';
$config['is_system_on']         = 'y';
$config['allow_extensions']     = 'y';
$config['site_label']           = 'Site Label';
$config['site_name']            = 'Site Name';
$config['cookie_prefix']        = '';

$config['index_page']           = "index.php";
$config['base_url']             = $base_url . "/";
$config['site_url']             = $config['base_url'];
$config['cp_url']               = $config['base_url'].$system_folder."/index.php";
$config['theme_folder_path']    = $base_path . "/themes/";
$config['theme_folder_url']     = $base_url . "/themes/";
$config['cp_theme']             = "default";
$config['emoticon_path']        = $images_url . "/smileys/";
$config['captcha_path']         = $images_path . "/captchas/";
$config['captcha_url']          = $images_url . "/captchas/";
$config['avatar_path']          = $images_path . "/avatars/";
$config['avatar_url']           = $images_url . "/avatars/";
$config['photo_path']           = $images_path . "/member_photos/";
$config['photo_url']            = $images_url . "/member_photos/";
$config['sig_img_path']         = $images_path . "/signature_attachments/";
$config['sig_img_url']          = $images_url . "/signature_attachments/";
$config['prv_msg_upload_path']  = $images_path . "/pm_attachments/";

$config['index_page'] = 'index.php';
$config['save_tmpl_files'] = 'y';
// ExpressionEngine Config Items
// Find more configs and overrides at
// https://docs.expressionengine.com/latest/general/system_configuration_overrides.html

$config['app_version'] = '5.3.2';
$config['encryption_key'] = '78e307bb286224bd336e71417c800997eb9612a5';
$config['session_crypt_key'] = 'f4b860726dbfee5bb8dddda232142261abbb8550';
// $config['database'] = array(
// 	'expressionengine' => array(
// 		'hostname' => 'localhost',
// 		'database' => 'jtcc_dev',
// 		'username' => 'jtcc_dev',
// 		'password' => 'VdleL7N_krUS',
// 		'dbprefix' => 'exp_',
// 		'char_set' => 'utf8mb4',
// 		'dbcollat' => 'utf8mb4_unicode_ci',
// 		'port'     => ''
// 	),
// );

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
$config['database'] = array(
	'expressionengine' => $dbConnection
);

// EOF