<h2><?=lang('seeo_sitemap_options')?></h2>

<!-- Sitemap Priority -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_sitemap_priority'><?=lang('seeo_sitemap_priority')?></label>
    </div>
    <div class="field-control">
        <?= form_dropdown("meta[sitemap_priority]",
                array(
                    '0.0' => '0.0',
                    '0.1' => '0.1',
                    '0.2' => '0.2',
                    '0.3' => '0.3',
                    '0.4' => '0.4',
                    '0.5' => '0.5 - Default',
                    '0.6' => '0.6',
                    '0.7' => '0.7',
                    '0.8' => '0.8',
                    '0.9' => '0.9',
                    '1.0' => '1.0',
                ), $meta->sitemap_priority, 'id="seeo__seeo_sitemap_priority"'
            )
            ?>
    </div>
</fieldset>

<!-- Change Frequency -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_sitemap_change_frequency'><?=lang('seeo_sitemap_change_frequency')?></label>
    </div>
    <div class="field-control">
        <?= form_dropdown("meta[sitemap_change_frequency]",
                array(
                    'always' => 'Always',
                    'hourly' => 'Hourly',
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                    'yearly' => 'Yearly',
                    'never' => 'Never'
                ), $meta->sitemap_change_frequency, 'id="seeo__seeo_sitemap_change_frequency"'
            )
            ?>
    </div>
</fieldset>

<!-- Include in Sitemap? -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_sitemap_include'><?=lang('seeo_include_in_sitemap')?></label>
    </div>
    <div class="field-control">
        <label style="display: inline-block; margin-right: 1em;" for="seeo__seeo_sitemap_include_y">
            <input type="radio"
                   id="seeo__seeo_sitemap_include_y"
                   name="meta[sitemap_include]"
                   value="y"
                   class="seeo_enable_disable"
                   <?= ($meta->sitemap_include === 'y') ? 'checked="checked"' : ''; ?>
                /> Yes
        </label>

        <label style="display: inline-block" for="seeo__seeo_sitemap_include_n">
            <input type="radio"
                   id="seeo__seeo_sitemap_include_n"
                   name="meta[sitemap_include]"
                   value="n"
                   class="seeo_enable_disable"
                   <?= ($meta->sitemap_include === 'n') ? 'checked="checked"' : ''; ?>
                /> No
        </label>
    </div>
</fieldset>
