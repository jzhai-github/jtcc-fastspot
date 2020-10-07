<?php
namespace EEHarbor\Safeharbor\Model;

use EllisLab\ExpressionEngine\Service\Model\Model;

class Backups extends Model
{
    protected static $_primary_key = 'backup_id';
    protected static $_table_name = 'safeharbor_backups';

    protected $backup_id;
    protected $site_id;
    protected $name;
    protected $backup_time;
    protected $start_time;
    protected $end_time;
    protected $status;
    protected $db_backup_mode;
    protected $backup_size;
    protected $backup_dbsize;
    protected $md5_hash;
    protected $local;
    protected $backup_transfer_failed;
    protected $backup_type;
    protected $file_ctime;
    protected $full_backup_id;
    protected $transferred_amazon_s3;
    protected $transferred_ftp;
    protected $note;
}
