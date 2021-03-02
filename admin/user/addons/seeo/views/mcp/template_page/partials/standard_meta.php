<h2><?=lang('seeo_standard_meta_tags')?></h2>

<!-- Title -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_title'><?=lang('seeo_title')?></label>
        <span id="seeo__seeo_title_count" class="seeo__instructions"></span>

    </div>
    <div class="field-control">
        <input id='seeo__seeo_title' type="text" name="meta[title]" value="<?= $meta->title ?>"/>
    </div>
    <div class="copy" style="float: right;">
        <label for="copy-to-og-title" style="display: inline-block; margin: 6px 6px 0;">
        <input type="checkbox" name="copy-to-og-title" value="y" class="js-seeo-copy-to-og-title" id="copy-to-og-title">
            <?=lang('seeo_copy_to_og_title')?>
        </label>

        <label for="copy-to-twitter-title" style="display: inline-block; margin: 6px 6px 0;">
        <input type="checkbox" name="copy-to-twitter-title" value="y" class="js-seeo-copy-to-twitter-title" id="copy-to-twitter-title">
            <?=lang('seeo_copy_to_twitter_title')?>
        </label>
    </div>
</fieldset>

<!-- Description -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_description'><?=lang('seeo_description')?></label>
        <span class="seeo__instructions" id="seeo__seeo_description_count"></span>
    </div>
    <div class="field-control">
        <textarea id="seeo__seeo_description" name="meta[description]" cols="90" rows="2"><?= $meta->description ?></textarea>
    </div>
    <div class="copy" style="float: right;">
        <label for="copy-to-og-description" style="display: inline-block; margin: 6px 6px 0;">
            <input type="checkbox" name="copy-to-og-description" value="y" class="js-seeo-copy-to-og-description" id="copy-to-og-description">
            <?=lang('seeo_copy_to_og_description')?>
        </label>

        <label for="copy-to-twitter-description" style="display: inline-block; margin: 6px 6px 0;">
            <input type="checkbox" name="copy-to-twitter-description" value="y" class="js-seeo-copy-to-twitter-description" id="copy-to-twitter-description">
            <?=lang('seeo_copy_to_twitter_description')?>
        </label>
    </div>
</fieldset>

<!-- Keywords -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_keywords'><?=lang('seeo_keywords')?></label>
        <span id="seeo__seeo_keywords_count" class="seeo__instructions"></span>
    </div>
    <div class="field-control">
        <textarea id="seeo__seeo_keywords" name="meta[keywords]" cols="90" rows="2"><?= $meta->keywords ?></textarea>
    </div>
</fieldset>

<!-- Author -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_author'><?=lang('seeo_author')?></label>
    </div>
    <div class="field-control">
        <input id="seeo__seeo_author" type="text" name="meta[author]" value="<?= $meta->author ?>"/>
    </div>
</fieldset>

<!-- Canonical URL -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_canonical_url'><?=lang('seeo_canonical_url')?></label>
    </div>
    <div class="field-control">
        <input id="seeo__seeo_canonical_url" type="text" name="meta[canonical_url]" value="<?= $meta->canonical_url ?>"/>
    </div>
    <div class="copy" style="float: right;">
        <label for="copy-to-og-url" style="display: inline-block; margin: 6px 6px 0;">
            <input type="checkbox" name="copy-to-og-url" value="y" class="js-seeo-copy-to-og-url" id="copy-to-og-url">
            <?=lang('seeo_copy_to_og_url')?>
        </label>
    </div>
</fieldset>

<!-- Robots Directive -->
<fieldset>
    <div class="field-instruct">
        <label for='seeo__seeo_robots'><?=lang('seeo_robots_directive')?></label>
    </div>
    <div class="field-control">
        <?= form_dropdown("meta[robots]",
            array(
                'INDEX, FOLLOW' => 'INDEX, FOLLOW',
                'NOINDEX, FOLLOW' => 'NOINDEX, FOLLOW',
                'INDEX, NOFOLLOW' => 'INDEX, NOFOLLOW',
                'NOINDEX, NOFOLLOW' => 'NOINDEX, NOFOLLOW'
            ),
            $meta->robots,
            'id="seeo__seeo_robots"'
        )
        ?>
    </div>
</fieldset>
