<style>
th.collapse, td.collapse {
    margin: 0;
    padding: 0;
    }
</style>
<div class="tbl-ctrls">
    <table id="" class="migrationPathTable" border="0" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th class=""><?php echo $source->name; ?></th>
                <th class=""></th>
                <th class="">SEEO</th>
                <th class="collapse"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($source->migrationPaths) || count($source->migrationPaths) === 0) : ?>
                <tr>
                    <td><em>No migration paths found for this source.</em></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            <?php endif; ?>

            <?php foreach ($source->migrationPaths as $migrationPath) : ?>
                <tr class="">
                    <td class="" valign="top"><?php echo $migrationPath->from; ?></td>
                    <td class="" valign="top">→</td>
                    <td class="" valign="top">
                        <label><input class='migrationPathCheckbox' checked value='y' type="checkbox" name="seeo_channels[<?php echo $channel_id; ?>][<?php echo $migrationPath->name; ?>]"> <?php echo $migrationPath->to; ?></label>

                        <?php if (!empty($migrationPath->options) && count($migrationPath->options)) : ?>
                            <div class="additional_options" style="padding: 12px; margin-top: 12px; background-color: #fff; border: 1px solid #eee;">
                                <?php foreach ($migrationPath->options as $option) : ?>
                                    <label>
                                        <input type="checkbox" name="seeo_channels[<?php echo $channel_id; ?>][<?php echo $option->name; ?>]" value="y">
                                        <?php echo $option->text; ?>
                                    </label>
                                <?php endforeach;?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td class="collapse"></td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>
</div>