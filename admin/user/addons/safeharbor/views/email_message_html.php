<html>
<head>
    <title>Safe Harbor Backup Report</title>
</head>
<body>

    <h1>Safe Harbor Backup Report</h1>

    <table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50">Date:</td>
            <td><?php echo $date; ?></td>
        </tr>
        <tr>
            <td width="50">Site:</td>
            <td><?php echo $site; ?></td>
        </tr>
    </table>

    <p>======================================================================</p>

    <table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100">Backup Status:</td>
            <td><span style="font-weight:bold; color:<?php echo $backup_status == 'complete' ? 'green' : 'red'; ?>;"><?php echo $backup_status; ?></span></td>
        </tr>
    </table>

    <p>======================================================================</p>

    <table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100">Backup File:</td>
            <td><?php echo $backup_file; ?></td>
        </tr>
        <tr>
            <td width="100">Backup Size:</td>
            <td><?php echo $backup_size; ?></td>
        </tr>
    </table>

    <p></p>

    <table border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="100">Started:</td>
            <td><?php echo $backup_start; ?></td>
        </tr>
        <tr>
            <td width="100">Completed:</td>
            <td><?php echo $backup_end; ?></td>
        </tr>
        <tr>
            <td width="100">Duration:</td>
            <td><?php echo $backup_duration; ?></td>
        </tr>
    </table>

    <p>======================================================================</p>

    <p>Thank you for using Safe Harbor!</p>

    <p>If you have any questions or comments, feel free to send us an email at <a href="mailto:help@eeharbor.com">help@eeharbor.com</a></p>

    <p><a href="http://eeharbor.com">http://eeharbor.com</a><br/>
    <a href="http://twitter.com/eeharbor">http://twitter.com/eeharbor</a></p>

</body>
</html>
