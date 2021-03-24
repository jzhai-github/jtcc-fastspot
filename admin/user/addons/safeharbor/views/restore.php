
<?php if (empty($backup)) : ?>

    <h2>Invalid Backup</h2>
    <p>The backup you selected does not exist.<br/><a href="<?php echo ee('CP/URL', 'addons/settings/safeharbor') ?>">Go Back</a></p>

<?php else : ?>

    <style type="text/css">
    .pageContents .checkbox { padding:10px 0px; overflow:hidden; zoom:1.0; }
    .pageContents .checkbox input { display:block; float:left; margin:5px; }
    .pageContents .checkbox label { display:block; float:left; margin:0; padding:3px; }
    .pageContents .buttons a { padding:4px 9px; }
    </style>

    <h2>Backup Information</h2>
    <p>The backup you selected to restore from ('<?php echo $backup['name']; ?>') was created on <?php echo unix_to_human($backup['timestamp_start']); ?> (<?php echo timespan($backup['timestamp_start'], time()); ?> ago). If you choose to restore, all changes after this backup will be lost. When the restore is complete you will need to log in to the Control Panel again. It is recommended you run another backup now before proceeding.</p>

    <?php echo form_open(ee('CP/URL', 'addons/settings/safeharbor/restore/' . $backup['id'])); ?>
        <div class="checkbox">
            <input type="checkbox" name="confirm" id="confirm" value="1" />
            <label for="confirm">Yes, restore my backup!</label>
        </div>
        <div class="buttons">
            <?php echo form_submit(array('name'=>'submit', 'value'=>lang('safeharbor_label_restore'), 'class'=>'submit')); ?>
            <a href="<?php echo ee('CP/URL', 'addons/settings/safeharbor/') ?>">Cancel</a>
        </div>
    <?php echo form_close(); ?>

<?php endif; ?>

<?php include PATH_THIRD.'safeharbor/views/plug.php'; ?>
