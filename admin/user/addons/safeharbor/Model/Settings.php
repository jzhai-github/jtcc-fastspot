<?php
namespace EEHarbor\Safeharbor\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class Settings extends Model
{
    protected static $_primary_key = 'id';
    protected static $_table_name = 'safeharbor_settings';

    protected $id;
    protected $site_id;

    protected $auth_code;
    protected $notify_email_address;
    protected $backup_space;
    protected $backup_path;
    protected $backup_time;
    protected $transfer_type;
    protected $db_backup;
    protected $disable_remote;
    protected $time_diff;
    protected $backup_plan;
    protected $time_saved;
    protected $storage_path;
    protected $off_site_backup_auth_code;

    // protected $ee_root;              // not in db
    // protected $backup_time_hour;     // not in db
    // protected $backup_time_min;      // not in db
    // protected $cron_url;             // not in db

    // amazon s3 options
    protected $amazon_s3_enabled;
    protected $amazon_s3_access_key;
    protected $amazon_s3_secret;
    protected $amazon_s3_bucket;
    protected $amazon_s3_endpoint;

    // ftp options
    protected $ftp_enabled;
    protected $ftp_username;
    protected $ftp_password;
    protected $ftp_host;
    protected $ftp_port;
    protected $ftp_path;

    //public funciton to build out the trigger URL
    public function get__cron_url($type='full')
    {
        // build trigger url
        $trigger_url = ee()->functions->fetch_site_index(0);

        $action_id = ee()->db->get_where('actions', array('class'=>'Safeharbor', 'method'=>'_backup_cron'), 1);
        $action_id = $action_id->row('action_id');

        if (strpos($trigger_url, 'index.php') === false) {
            $trigger_url .= 'index.php';
        }

        $trigger_url .= QUERY_MARKER.'ACT='.$action_id;
        $trigger_url .= '&auth='.$this->get__auth_code();
        $trigger_url .= '&type='.$type;

        return $trigger_url;
    }

    public function get__auth_code()
    {
        $auth_code =  $this->auth_code;

        if (empty($auth_code)) {
            $auth_code = strtoupper(substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, 8));
            $this->auth_code =  $auth_code;
        }
        return $this->auth_code;
    }

    public function get__backup_time($return_part = null)
    {
        list($hours, $mins) = explode(':', $this->backup_time);

        $time['hours'] = (int) $hours;
        $time['mins'] = (int) $mins;

        if ($return_part === 'hours') {
            return $time['hours'];
        }

        if ($return_part === 'mins') {
            return $time['mins'];
        }

        return $time;
    }

    public function set__backup_time($time)
    {

        // $time = (int) $hour . ':' . (int) $mins;

        $this->setRawProperty('backup_time', $time);

        return true;
    }

    public function setBackupTimeFromParts($hour, $mins)
    {
        $time = (int) $hour . ':' . (int) $mins;

        $this->setRawProperty('backup_time', $time);

        return true;
    }

    public function set__amazon_s3_enabled($amazon_s3_enabled)
    {
        if ($amazon_s3_enabled === 'n') {
            $this->setRawProperty('amazon_s3_enabled', '0');
        } else {
            $this->setRawProperty('amazon_s3_enabled', '1');
        }
    }

    public function set__ftp_enabled($ftp_enabled)
    {
        if ($ftp_enabled === 'n') {
            $this->setRawProperty('ftp_enabled', '0');
        } else {
            $this->setRawProperty('ftp_enabled', '1');
        }
    }
}
