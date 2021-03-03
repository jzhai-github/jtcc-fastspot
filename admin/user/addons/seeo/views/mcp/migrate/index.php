<style>
.migration-channel-container {
    margin-left: 20px;
    }
.pull-right {
    float: right;
    display: inline-block;
    }
.btn-sm {
    padding: 3px 5px 4px !important;
    font-size: 13px;
    }
</style>

<div class="box panel">
    <h1 class="panel-heading">Migrate Meta</h1>

    <div class="app-notice-wrap"><?php echo ee('CP/Alert')->getAllInlines(); ?></div>

    <div class="panel-body">
<?php if (empty($migration_sources) || count($migration_sources) === 0) : ?>
    <div class="txt-wrap">
        <div class="txt-wrap">
            <strong>No Migration Sources Found</strong>
            <br /><br />

            <p>We can't find any legitimate sources to migrate from. SEEO is currently capable of migrating from the following sources:</p>

            <ul class="checklist">
                <li class="last">SEO Lite</li>
                <li class="last">NSM Better Meta</li>
                <li class="last">ZC Meta</li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <div class="tbl-ctrls settings">
        Select the sources below to migrate into SEEO.
    </div>
    <br />

    <?php foreach ($migration_sources as $source) : ?>
        <form method='post' action='<?= $flux->moduleURL('confirm_migrate') ?>' class=" settings">
            <input type="hidden" name="<?=$csrf_token_name?>" value="<?=$csrf_token_value?>" />
            <input type="hidden" name="source" value="<?=$source->shortname?>" />
            <input type="hidden" name="channel_based_migration" value="<?=$source->channel_based_migration?>" />

            <h2>
                <a href="<?php echo $flux->moduleURL('confirm_remove_source&source=' . $source->shortname); ?>" class="btn btn-sm remove pull-right">Remove Source</a>
                <?=$source->name?>
            </h2>

            <?php if ($source->channel_based_migration) : ?>
                <div class="tbl-ctrls settings">
                    <?php if (empty($channels)) : ?>
                        <strong>There are no channels to migrate to.</strong><br />
                    <?php else : ?>
                        <?php if ($source->name === 'Better Meta') : ?>
                            <div class="setting-txt"><b>NOTE:</b> Better Meta's "default settings" can be imported into SEEO. The default values are displayed when individual values are not entered for entries.</div>
                            <br />

                            <a class="btn action" href="<?= $flux->moduleURL('migrate_default_data&seeo_context=better_meta') ?>">Migrate Default Settings</a>
                            <br /><br />

                            <hr>
                            <br />
                        <?php endif;?>

                        Choose channels to migrate: <span onClick="toggleAll()" class="btn btn-sm action pull-right"> Toggle All Channels </span>
                        <br /><br />

                        <div class="migration-channel-container">
                        <?php foreach ($channels as $channel_id => $channel_title) : ?>
                            <div class="migration-channel-box">
                                <label><input type="checkbox" class="migration-channel" name="seeo_channels[<?php echo $channel_id; ?>][seeo_migrate]" value="y"> <?php echo $channel_title; ?></label>
                                <div class="migration-detail" style="display: none;">
                                    <?php include 'partials/migration_paths_table.php'; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                            <br />

                            <div class="btn-migrate-box" style="display:none;">
                                <input class="btn btn-migrate" type="submit" value="Migrate data from <?=$source->name?> to SEEO"><br />
                                <br />
                                You will have a chance to confirm the data before migrating.
                            </div>
                        </div>
                        <br />
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="settings">
                    <p>When migrating from ZC Meta, all existing meta data will be wiped out.</p>

                    <table id="zcMetaTable" class="" border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class=""></th>
                                <th class="">Select things you want to migrate from ZC Meta:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="">
                                <td class=""><input type="checkbox" checked name="zc_meta_migrate[meta_data]" value="y"></td>
                                <td class="">Meta Data</td>
                            </tr>
                            <tr class="">
                                <td class=""><input type="checkbox" checked name="zc_meta_migrate[listings]" value="y"></td>
                                <td class="">Meta Listings (Template Pages)</td>
                            </tr>
                            <tr class="">
                                <td class=""><input type="checkbox" checked name="zc_meta_migrate[settings]" value="y"></td>
                                <td class="">Settings</td>
                            </tr>
                        </tbody>
                    </table>

                    <input class="btn" type="submit" value="Migrate data from <?=$source->name?> to SEEO">
                </div>
            <?php endif; ?>
        </form>
    <?php endforeach; ?>
<?php endif; ?>
</div>
</div>


<script type="text/javascript">
function toggleAll() {
    $('.migration-channel:not(checked)').click();
}
</script>
