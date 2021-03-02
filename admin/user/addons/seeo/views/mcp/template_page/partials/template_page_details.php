
        <fieldset>
            <div class="field-instruct">
                <label for='seeo__seeo_template_page_path'><?=lang('seeo_template_page_path')?></label>
            </div>
            <div class="field-control">
                <input id="seeo__seeo_template_page_path" type="text" placeholder='Example: /blog' name="template_page[path]" value="<?= $template_page->path ?>"/>
            </div>
        </fieldset>
        <fieldset>
            <div class="field-instruct">
                <label for='seeo__seeo_template_page_channel_id'><?=lang('seeo_template_page_channel')?></label>
                <em><?=lang('seeo_template_page_channel_description')?></em>
            </div>
            <div class="field-control">
                <?= form_dropdown("template_page[channel_id]",
                    $channels,
                    $template_page->channel_id,
                    'id="seeo__seeo_template_page_channel_id"'
                )?>
            </div>
        </fieldset>

        <input id="seeo__seeo_template_page_id" type="hidden" name="template_page[id]" value="<?= $template_page->id ?>"/>
        <input id="seeo__seeo_meta_id" type="hidden" name="meta[id]" value="<?= $meta->id ?>"/>

