<?php echo form_open($flux->moduleUrl('save_meta_settings'), array('id'=>'ext_settings_seeo', 'class' => 'settings')); ?>
<input type="hidden" name="channel_id" value="<?php echo (!empty($channel_id) ? $channel_id : 0); ?>" />
<input type="hidden" id="reset_url" name="reset_url" value="<?php echo $reset_url; ?>" />

<div class="box panel">
    <h1 class="panel-heading"><?php echo !empty($channel_title) ? lang('seeo_channel_settings') : lang('seeo_default_standard_meta'); ?></h1>

    <div class="app-notice-wrap"><?php echo ee('CP/Alert')->getAllInlines(); ?></div>

    <div class="panel-body settings seeo">
    <?php
    if (!empty($channel_id)) {
        echo '<big><strong>' . $channel_title . '</strong></big><br /><br />';
        echo lang('seeo_channel_settings_instructions');
    } else {
        echo lang('seeo_default_settings_instructions');
    }
    ?>
    </div>
</div>

<?php
if (ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) {
    include "partials/file_manager.php";
}

include "partials/default_standard_meta.php";
include "partials/default_open_graph.php";
include "partials/default_twitter_cards.php";
include "partials/template.php";
?>

<div class="box panel">
    <h1 class="panel-heading"><?php echo lang('seeo_standard_robots_directive') ?></h1>
    <div class="panel-body settings seeo">
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="robots"><?=lang('seeo_default_robots_directive')?></label>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_standard_robots_index_instructions')?></em></span>
            </div>
            <div class="setting-field col w-12 last">
                <select name="robots">
                    <?php if (!empty($channel_id)) { ?><option value="d" <?php if ($robots == 'd' || empty($robots)) : ?> selected="selected"<?php endif; ?>>Use Global Default</option><?php } ?>
                    <option value="INDEX, FOLLOW" <?php if ($robots == 'INDEX, FOLLOW') : ?> selected="selected"<?php endif; ?>>INDEX, FOLLOW</option>
                    <option value="NOINDEX, FOLLOW" <?php if ($robots == 'NOINDEX, FOLLOW') : ?> selected="selected"<?php endif; ?>>NOINDEX, FOLLOW</option>
                    <option value="INDEX, NOFOLLOW" <?php if ($robots == 'INDEX, NOFOLLOW') : ?> selected="selected"<?php endif; ?>>INDEX, NOFOLLOW</option>
                    <option value="NOINDEX, NOFOLLOW" <?php if ($robots == 'NOINDEX, NOFOLLOW') : ?> selected="selected"<?php endif; ?>>NOINDEX, NOFOLLOW</option>
                </select>
                <br />
                <?php if (!empty($channel_id)) { ?>
                    <br /><div><button type="button" class="btn action reset-action" data-channel="<?php echo $channel_id; ?>" data-type="robots">Reset Entry Settings</button></div>
                    <div class="seeo__instructions field-instruct"><em>This will reset all entries in this channel to "Use Channel Default"</em></div><br />
                <?php } ?>
            </div>
        </fieldset>
    </div>
</div>

<div class="box panel">
    <h1 class="panel-heading seeo__additional-channel-options-heading"><?=lang('seeo_sitemap_default_options')?></h1>
    <div class="panel-body settings seeo">
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="sitemap_include"><?=lang('seeo_sitemap_include')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <select name="sitemap_include">
                    <?php if (!empty($channel_id)) { ?><option value="d" <?php echo (empty($sitemap_include) || $sitemap_include === 'd' ? 'selected="selected"' : ''); ?>>Use Global Default</option><?php } ?>
                    <option value="y" <?php echo ($sitemap_include === 'y' ? 'selected="selected"' : ''); ?>>Yes, include all entries in this <?php echo (!empty($channel_id) ? 'channel' : 'site'); ?></option>
                    <option value="n" <?php echo ($sitemap_include === 'n' ? 'selected="selected"' : ''); ?>>No, do not include all entries in this <?php echo (!empty($channel_id) ? 'channel' : 'site'); ?></option>
                </select>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_sitemap_include_instructions_' .  (!empty($channel_id) ? 'channel' : 'global'))?></em></span>
                <?php if (!empty($channel_id)) { ?>
                    <br /><div><button type="button" class="btn action reset-action" data-channel="<?php echo $channel_id; ?>" data-type="sitemap-include">Reset Entry Settings</button></div>
                    <div class="seeo__instructions field-instruct"><em>This will reset all entries in this channel to "Use Channel Default"</em></div><br />
                <?php } ?>
            </div>
        </fieldset>

        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="sitemap_priority"><?=lang('seeo_sitemap_priority')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <select name="sitemap_priority">
                    <?php if (!empty($channel_id)) { ?><option value="d" <?php if (empty($sitemap_priority) || $sitemap_priority == 'd') : ?> selected="selected"<?php endif; ?>>Use Global Default</option><?php } ?>
                    <option value="0.0" <?php if ($sitemap_priority == '0.0') : ?> selected="selected"<?php endif; ?>>0.0</option>
                    <option value="0.1" <?php if ($sitemap_priority == '0.1') : ?> selected="selected"<?php endif; ?>>0.1</option>
                    <option value="0.2" <?php if ($sitemap_priority == '0.2') : ?> selected="selected"<?php endif; ?>>0.2</option>
                    <option value="0.3" <?php if ($sitemap_priority == '0.3') : ?> selected="selected"<?php endif; ?>>0.3</option>
                    <option value="0.4" <?php if ($sitemap_priority == '0.4') : ?> selected="selected"<?php endif; ?>>0.4</option>
                    <option value="0.5" <?php if ($sitemap_priority == '0.5') : ?> selected="selected"<?php endif; ?>>0.5 (default)</option>
                    <option value="0.6" <?php if ($sitemap_priority == '0.6') : ?> selected="selected"<?php endif; ?>>0.6</option>
                    <option value="0.7" <?php if ($sitemap_priority == '0.7') : ?> selected="selected"<?php endif; ?>>0.7</option>
                    <option value="0.8" <?php if ($sitemap_priority == '0.8') : ?> selected="selected"<?php endif; ?>>0.8</option>
                    <option value="0.9" <?php if ($sitemap_priority == '0.9') : ?> selected="selected"<?php endif; ?>>0.9</option>
                    <option value="1.0" <?php if ($sitemap_priority == '1.0') : ?> selected="selected"<?php endif; ?>>1.0</option>
                </select>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_sitemap_priority_instructions')?></em></span>
                <?php if (!empty($channel_id)) { ?>
                    <br /><div><button type="button" class="btn action reset-action" data-channel="<?php echo $channel_id; ?>" data-type="sitemap-priority">Reset Entry Settings</button></div>
                    <div class="seeo__instructions field-instruct"><em>This will reset all entries in this channel to "Use Channel Default"</em></div><br />
                <?php } ?>
            </div>
        </fieldset>

        <fieldset class="col-group last">
            <div class="setting-txt col w-4">
                <label for="sitemap_change_frequency"><?=lang('seeo_sitemap_change_frequency')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <select name="sitemap_change_frequency">
                    <?php if (!empty($channel_id)) { ?><option value="d" <?php if (empty($sitemap_change_frequency) || $sitemap_change_frequency == 'd') : ?> selected="selected"<?php endif; ?>>Use Global Default</option><?php } ?>
                    <option value="always" <?php if ($sitemap_change_frequency == 'always') : ?> selected="selected"<?php endif; ?>>Always</option>
                    <option value="hourly" <?php if ($sitemap_change_frequency == 'hourly') : ?> selected="selected"<?php endif; ?>>Hourly</option>
                    <option value="daily" <?php if ($sitemap_change_frequency == 'daily') : ?> selected="selected"<?php endif; ?>>Daily</option>
                    <option value="weekly" <?php if ($sitemap_change_frequency == 'weekly' || $sitemap_change_frequency == '') : ?> selected="selected"<?php endif; ?>>Weekly</option>
                    <option value="monthly" <?php if ($sitemap_change_frequency == 'monthly') : ?> selected="selected"<?php endif; ?>>Monthly</option>
                    <option value="yearly" <?php if ($sitemap_change_frequency == 'yearly') : ?> selected="selected"<?php endif; ?>>Yearly</option>
                    <option value="never" <?php if ($sitemap_change_frequency == 'never') : ?> selected="selected"<?php endif; ?>>Never</option>
                </select>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_sitemap_change_frequency_instructions')?></em></span>
                <?php if (!empty($channel_id)) { ?>
                    <br /><div><button type="button" class="btn action reset-action" data-channel="<?php echo $channel_id; ?>" data-type="sitemap-change-frequency">Reset Entry Settings</button></div>
                    <div class="seeo__instructions field-instruct"><em>This will reset all entries in this channel to "Use Channel Default"</em></div><br />
                <?php } ?>
            </div>
        </fieldset>
    </div>
</div>

<fieldset class="form-btns">
    <input class="btn" type="submit" value="<?=lang('seeo_save_settings')?>" />
</fieldset>

<?php echo form_close(); ?>
