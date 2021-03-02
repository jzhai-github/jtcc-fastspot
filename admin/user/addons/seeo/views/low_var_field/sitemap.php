<div class="">
    <label for="seeo_hide_sitemap_fields">
        <?=form_checkbox('seeo[hide_sitemap_fields]',
            true,
            (isset($hide_sitemap_fields) && $hide_sitemap_fields == true) ? true : false,
            'id="seeo_hide_sitemap_fields"') ?> &nbsp; <?= lang('seeo_hide_sitemap_fields') ?>
    </label>
</div>

<div class="">
    <label for="seeo_default_sitemap_priority">
        <?= form_dropdown(
            "seeo[default_sitemap_priority]",
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
        ),
            (isset($default_sitemap_priority) ? $default_sitemap_priority : '0.5'),
        '"id" => "seeo_default_sitemap_priority"'
        )
    ?> &nbsp; <?=lang('seeo_default_sitemap_priority')?>
    </label>
</div>