<div class="box panel">
    <div class="tbl-ctrls panel-body">
        <h1 class="panel-heading">Audit Entries</h1>

        <p>Choose a channel to audit.</p>

        <div class="app-notice-wrap"><?php echo ee('CP/Alert')->getAllInlines(); ?></div>

        <table cellspacing="0">
            <thead>
                <tr>
                    <th><?=lang('seeo_audit_channel')?></th>
                    <th><?=lang('seeo_audit_num_entries')?></th>
                    <th><?=lang('seeo_channel_settings')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($channels as $channel_id => $channel_info) : ?>
                    <tr>
                        <td><a href="<?php echo $channel_info['url']; ?>"><?php echo $channel_info['channel_title']; ?></a></td>
                        <td><a href="<?php echo $channel_info['url']; ?>"><?php echo $channel_info['num_entries']; ?></a></td>
                        <td><a href="<?php echo $channel_info['settings_url']; ?>" class="btn action"><?php echo lang('seeo_settings'); ?></a></td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($channels) || count($channels) === 0) : ?>
                    <tr class="no-results">
                        <td class="solo" colspan="3">
                            <p>No <b>Channels</b> found.</p>
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>
