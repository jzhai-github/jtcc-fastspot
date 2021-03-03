<div class="box panel">
    <h1 class="panel-heading">Remove <?= $migration->name ?></h1>
    <form class='panel-body settings' method="post" action="<?= $flux->moduleURL('post_remove_source') ?>">
        <input type="hidden" name="seeo_context" value="<?php echo $migration->shortname; ?>" />
        <input type="hidden" name="<?=$csrf_token_name?>" value="<?=$csrf_token_value?>" />

        <h3>MAKE A DATABASE BACKUP BEFORE PROCEEDING!</h3><br />

        <strong><?=lang('seeo_are_you_sure')?> This will completely remove <?php echo $migration->name; ?> and all data associated with it!</strong>
        <br />

        <p>The following items will be removed:</p>

        <div class="txt-wrap">
            <h2>Publish Tabs</h2>
            <?php if (empty($migration->remove_data['tabs'])) { ?>
                <em>No publish tabs will be removed.</em>
            <?php } else { ?>
                <ul class="checklist" style="margin-left:20px;">
<?php
foreach ($migration->remove_data['tabs'] as $tabName) {
    echo '<li class="last">' . $tabName . '</li>';
}
?>
                </ul>
            <?php } ?>
        </div>
        <br />

        <div class="txt-wrap">
            <h2>Database Tables</h2>
            <?php if (empty($migration->remove_data['tables'])) { ?>
                <em>No database tables will be removed.</em>
            <?php } else { ?>
                <ul class="checklist" style="margin-left:20px;">
<?php
foreach ($migration->remove_data['tables'] as $tableName) {
    echo '<li class="last">' . ee()->db->dbprefix($tableName) . '</li>';
}
?>
                </ul>
            <?php } ?>
        </div>
        <br />

        <div class="txt-wrap">
            <h2>Database Rows</h2>
            <?php if (empty($migration->remove_data['rows'])) { ?>
                <em>No database rows will be removed.</em>
            <?php } else { ?>
                <em>The following database tables will have rows removed that match the field listed:</em>
                <br /><br />

                <div style="margin-left:20px;">
<?php
foreach ($migration->remove_data['rows'] as $tableName => $rows) {
    echo '<strong>' . ee()->db->dbprefix($tableName) . '</strong><br />';
    echo '<ul class="checklist" style="margin-left:20px;">';

    foreach ($rows as $row) {
        echo '<li class="last">', $row['field'], ' == ', $row['value'], '</li>', "\n";
    }

    echo '</ul><br />', "\n";
}
?>
                </div>
            <?php } ?>
        </div>
        <br />

        <?php //include 'partials/hidden_confirm_fields.php'; ?>

        <fieldset class="form-ctrls">
            <input class="btn" type="submit" value="<?=lang('seeo_confirm')?>">
            <a class="btn remove" href="<?= $flux->moduleURL('setup_migrate') ?>"><?=lang('seeo_cancel_go_back')?></a>
        </fieldset>

    </form>
</div>
