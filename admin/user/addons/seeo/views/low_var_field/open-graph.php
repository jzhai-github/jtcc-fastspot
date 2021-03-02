<div class="">
    <label for="seeo_hide_open_graph_fields">
        <?=form_checkbox('seeo[hide_open_graph_fields]',
            true,
            (isset($hide_open_graph_fields) && $hide_open_graph_fields == true) ? true : false,
            'id="seeo_hide_open_graph_fields"') ?> &nbsp; <?= lang('seeo_hide_open_graph_fields') ?>
    </label>
</div>