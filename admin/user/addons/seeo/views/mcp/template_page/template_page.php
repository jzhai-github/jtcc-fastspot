<div>
    <div class="box panel">
        <h1 class="panel-heading">Template Page Meta</h1>

        <?php echo ee('CP/Alert')->getAllInlines(); ?>

        <div class="panel-body">

        <div class="tbl-ctrls">
            <p><?php echo lang('seeo_template_page_meta_instructions'); ?></p>
            <table cellspacing="0">
                <thead>
                    <tr>
                        <th><?=lang('seeo_template_page_meta_path')?></th>
                        <th><?=lang('seeo_template_page_meta_title')?></th>
                        <th class="check-ctrl"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($template_pages as $template_page) : ?>
                        <tr>
                            <td><a href='<?= $flux->moduleURL('template_page_meta_create_edit', array('template_page_id' => $template_page->id)) ?>'><?= $template_page->path ?></a></td>
                            <td><a href='<?= $flux->moduleURL('template_page_meta_create_edit', array('template_page_id' => $template_page->id)) ?>'><?= $template_page->Meta->title ?></a></td>
                            <td>
                                <div class="toolbar-wrap">
                                    <ul class="toolbar">
                                        <li class="remove"><a href="<?= $flux->moduleURL('template_page_meta_delete', array('template_page_id' => $template_page->id)) ?>" onclick="return confirm('Are you sure you want to delete this Template Page Meta?');"></a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($template_pages) || count($template_pages) === 0) : ?>
                        <tr class="no-results">
                            <td class="solo" colspan="3">
                                <p>No <b>Template Page Meta</b> found.</p>

                                <p><a class="btn" href="<?= $flux->moduleURL('template_page_meta_create_edit') ?>"><?php echo lang('seeo_template_page_meta_create'); ?></a></p>
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>

        <?php if (!empty($template_pages) && count($template_pages) !== 0) : ?>
            <div class="form-btns">
                <a class="btn" href="<?= $flux->moduleURL('template_page_meta_create_edit') ?>"><?php echo lang('seeo_template_page_meta_create'); ?></a>
            </div>
        <?php endif; ?>
        </div>

        </div>
    </div>
</div>
