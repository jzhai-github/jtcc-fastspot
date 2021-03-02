<div class="">
    <label for="seeo_hide_twitter_fields">
        <?=form_checkbox('seeo[hide_twitter_fields]',
            true,
            (isset($hide_twitter_fields) && $hide_twitter_fields == true) ? true : false,
            'id="seeo_hide_twitter_fields"') ?> &nbsp; <?= lang('seeo_hide_twitter_card_fields') ?>
    </label>
</div>