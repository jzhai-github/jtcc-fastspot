
<?php echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=safeharbor'.AMP.'method=show_search_results'.AMP.'O='.$backup_id, array('id'=>'live_search')); ?>

<table style="width:100%;">
    <thead>
        <tr>
            <th>
                <?php echo form_input(array('name'=>'search', 'id'=>'filesearch', 'class'=>'search', 'placeholder'=>'Search files by name...' )); ?>
            </th>
            <th>
                <?php echo form_submit(array('name'=>'submit', 'value'=>lang('submit'), 'class'=>'submit')); ?>
            </th>
    </thead>
    <tbody>
        <tr>
            <td colspan="2"><div id="display" style="padding-top:20px;"></div></td>
        <tr>
    </tbody>
</table>

<?php echo form_close(); ?>
