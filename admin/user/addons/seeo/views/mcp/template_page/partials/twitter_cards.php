<h2><?=lang('seeo_twitter_fields')?></h2>

<!-- Twitter: Title -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_twitter_title'><?=lang('seeo_twitter_title')?></label>
    </div>
    <div class="field-control">
        <input id='seeo__seeo_twitter_title' type="text" name="meta[twitter_title]" value="<?= $meta->twitter_title ?>"/>
    </div>
</fieldset>

<!-- Twitter:Description -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_twitter_description'><?=lang('seeo_twitter_description')?></label>
        <span class="seeo__instructions" id="seeo__seeo_twitter_description_count"></span>
    </div>
    <div class="field-control">
        <textarea id="seeo__seeo_twitter_description" name="meta[twitter_description]" cols="90" rows="2"><?= $meta->twitter_description ?></textarea>
    </div>
</fieldset>

<!-- Twitter: Content Type -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_twitter_content_type'><?=lang('seeo_twitter_content_type')?></label>
    </div>
    <div class="field-control">
        <?= form_dropdown(
            "meta[twitter_content_type]",
            array(
                        '' => '- Select -',
                        'summary' => 'Summary',
                        'summary_large_image' => 'Summary - Large Image',
                        'photo' => 'Photo',
                        'app' => 'App',
                        'product' => 'Product'
                    ),
            $meta->twitter_content_type,
            'id="seeo__seeo_twitter_content_type"'
        )
        ?>
    </div>
</fieldset>

<!-- Twitter:Image -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_twitter_image'><?=lang('seeo_twitter_image')?></label>
    </div>
    <div class="field-control">
        <?php if (ee()->seeo_settings->get(0, 'file_manager') == 'assets' && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) : ?>
            <?= $meta->assets_twitter_image->display_field(array($twitter_image)); ?>
        <?php else : ?>
            <input name="seeo__seeo_twitter_image" id="seeo__seeo_twitter_image" type="hidden" value='<?= $twitter_image ?>'>
            <img src="<?= $twitter_image_thumbnail ?>" max-height="100" max-width="100">
            <p><?= $twitter_image_button ?></p>
        <?php endif; ?>

    </div>
</fieldset>
