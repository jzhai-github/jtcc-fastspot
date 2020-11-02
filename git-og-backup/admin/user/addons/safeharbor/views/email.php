<html>
<head>
    <title>Safe Harbor Backup Report: <?php echo $site ?></title>
</head>
<body>

<h1>Safe Harbor Backup Report</h1>

<strong>Date:</strong><br />
<?php echo $date; ?>
<br /><br />

<strong>Site:</strong><br />
<?php echo $site; ?>
<br />

<br />
<hr>
<br />

<strong>Local Backup Status:</strong><br />
<div<?php if ($backup_status_local == 'Completed Successfully.') : ?> style="color:green;"<?php else : ?> style="color:red;"<?php endif; ?>><strong><?php echo $backup_status_local; ?></strong></div>
<br />

<strong>Amazon S3 Backup Status:</strong><br />
<div<?php if ($backup_status_amazon_s3 == 'Completed Successfully.') : ?> style="color:green;"<?php else : ?> style="color:red;"<?php endif; ?>><strong><?php echo $backup_status_amazon_s3; ?></strong></div>
<br />

<strong>FTP Backup Status:</strong><br />
<div<?php if ($backup_status_ftp == 'Completed Successfully.') : ?> style="color:green;"<?php else : ?> style="color:red;"<?php endif; ?>><strong><?php echo $backup_status_ftp; ?></strong></div>
<br />

<hr>
<br />

<strong>Backup File:</strong><br />
<?php echo $backup_filename; ?>
<br /><br />

<strong>Backup Size:</strong><br />
<?php echo $backup_size; ?>
<br /><br />

<hr>
<br />

<strong>Backup Type:</strong><br />
<?php echo $backup_type; ?>
<br /><br />

<strong>Backup Started:</strong><br />
<?php echo $backup_time_start; ?>
<br /><br />

<strong>Backup Completed:</strong><br />
<?php echo $backup_time_end; ?>
<br /><br />

<strong>Backup completed in:</strong><br />
<?php echo $backup_time_total ?>
<br /><br />

<strong>Database Backup Method:</strong><br />
<?php echo $backup_database_mode; ?>
<br /><br />

<hr>
<br />

<p>Thank you for using Safe Harbor!</p>

<p>If you have any questions or comments, feel free to send us an email at <a href="mailto:help@eeharbor.com">help@eeharbor.com</a></p>

<p><a href="http://eeharbor.com">http://eeharbor.com</a><br/>
<a href="http://twitter.com/eeharbor">http://twitter.com/eeharbor</a></p>

</body>
</html>
