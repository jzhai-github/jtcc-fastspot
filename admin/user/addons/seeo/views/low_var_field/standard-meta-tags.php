<div class="">
    <label for="seeo_hide_keywords">
        <?=form_checkbox('seeo[hide_keywords]',
            true,
            (isset($hide_keywords) && $hide_keywords == true) ? true : false,
            'id="seeo_hide_keywords"') ?> &nbsp; <?= lang('seeo_hide_standard_keywords_field') ?>
    </label>
</div>

<div class="">
    <label for="seeo_hide_author">
        <?=form_checkbox('seeo[hide_author]',
            true,
            (isset($hide_author) && $hide_author == true) ? true : false,
            'id="seeo_hide_author"') ?> &nbsp; <?= lang('seeo_hide_author_field') ?>
    </label>
</div>

<div class="">
    <label for="seeo_hide_canonical_url">
        <?=form_checkbox('seeo[hide_canonical_url]',
            true,
            (isset($hide_canonical_url) && $hide_canonical_url == true) ? true : false,
            'id="seeo_hide_canonical_url"') ?> &nbsp; <?= lang('seeo_hide_canonical_url_field') ?>
    </label>
</div>

<div class="">
    <label for="seeo_hide_robots">
        <?=form_checkbox('seeo[hide_robots]',
            true,
            (isset($hide_robots) && $hide_robots == true) ? true : false,
            'id="seeo_hide_robots"') ?> &nbsp; <?= lang('seeo_hide_robots_field') ?>
    </label>
</div>