<div class="box panel">
    <h1 class="panel-heading">Confirm Meta Data Migration From <?= $migration->name ?>:</h1>
    <form class='panel-body settings' method="post" action="<?= $flux->moduleURL('post_migrate') ?>">

        <h2><strong><?=lang('seeo_are_you_sure')?></strong> This will overwrite any SEEO information for the following:</h2>

        <div class="txt-wrap">
            <ul class="checklist">
                <?php foreach ($migration->migration_data as $key => $data) {
                    if ($data['active']) {
                        echo '<li class="last">' . $data['name'] . '</li>';
                    }
                } ?>
            </ul>
        </div>

        <?php include 'partials/hidden_confirm_fields.php'; ?>

        <fieldset class="form-ctrls">
            <input class="btn" type="submit" value="<?=lang('seeo_confirm')?>">
            <a class="btn remove" href="<?= $flux->moduleURL('setup_migrate') ?>"><?=lang('seeo_cancel_go_back')?></a>
        </fieldset>

    </form>
</div>
