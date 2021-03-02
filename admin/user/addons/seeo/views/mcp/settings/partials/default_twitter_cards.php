<div class="box panel">
    <h1 class="panel-heading"><?=lang('seeo_default_twitter_cards')?></h1>

    <div class="panel-body settings seeo">
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_twitter_title"><?=lang('seeo_default_twitter_title')?></label>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_twitter_title_instructions')?></em></span>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="twitter_title" id="default_twitter_title" value="<?=htmlspecialchars($twitter_title)?>"/>
            </div>
        </fieldset>

        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_twitter_description"><?=lang('seeo_default_twitter_description')?></label>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_twitter_description_instructions')?></em></span>
            </div>
            <div class="setting-field col w-12 last">
                <textarea name="twitter_description" id="default_twitter_description" cols="30" rows="3"><?=htmlspecialchars($twitter_description)?></textarea>
            </div>
        </fieldset>

        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_twitter_type"><?=lang('seeo_default_twitter_type')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <select name="twitter_content_type" id="default_twitter_type">
                    <option value="" <?php if ($twitter_content_type == '') : ?> selected="selected"<?php endif; ?>>-- Select --</option>
                    <option value="summary" <?php if ($twitter_content_type == 'summary') : ?> selected="selected"<?php endif; ?>>Summary</option>
                    <option value="summary_large_image" <?php if ($twitter_content_type == 'summary_large_image') : ?> selected="selected"<?php endif; ?>>Summary w/ Large Image</option>
                    <option value="photo" <?php if ($twitter_content_type == 'photo') : ?> selected="selected"<?php endif; ?>>Photo</option>
                    <option value="app" <?php if ($twitter_content_type == 'app') : ?> selected="selected"<?php endif; ?>>App</option>
                    <option value="product" <?php if ($twitter_content_type == 'product') : ?> selected="selected"<?php endif; ?>>Product</option>
                </select>
            </div>
        </fieldset>

        <fieldset class="col-group last">
            <div class="setting-txt col w-4">
                <label for="default_twitter_image"><?=lang('seeo_default_twitter_image')?></label>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_twitter_image_instructions')?></em></span>
            </div>
            <div class="setting-field col w-12 last">
                <div class="field-control">
                <?php
                if ($file_manager == 'assets' && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) {
                    echo $assets_twitter_image->display_field($twitter_image);
                } else {
                    if ($flux->ver_gte(5)) {
                        echo ee()->file_field->dragAndDropField('twitter_image', $twitter_image, 'all', 'image');
                    } else {
                        $fp = ee('CP/FilePicker')->make('all');

                        $fp_link = $fp->getLink()
                            ->withValueTarget('twitter_image')
                            ->withNameTarget('twitter_image')
                            ->withImage('twitter_image');

                        $fp_upload = clone $fp_link;
                        $fp_upload
                            ->setText(lang('upload_file'))
                            ->setAttribute('class', 'btn action file-field-filepicker');

                        $fp_edit = clone $fp_link;
                        $fp_edit
                            ->setText('')
                            ->setAttribute('title', lang('edit'))
                            ->setAttribute('class', 'file-field-filepicker');

                        $file = null;

                        if (preg_match('/^{filedir_(\d+)}/', $twitter_image, $matches)) {
                            // Set upload directory ID and file name
                            $dir_id = $matches[1];
                            $file_name = str_replace($matches[0], '', $twitter_image);

                            $file = ee('Model')->get('File')
                                ->filter('file_name', $file_name)
                                ->filter('upload_location_id', $dir_id)
                                ->filter('site_id', ee()->config->item('site_id'))
                                ->first();
                        } elseif (! empty($twitter_image) && is_numeric($twitter_image)) {
                            // If file field is just a file ID
                            $file = ee('Model')->get('File', $twitter_image)->first();
                        }

                        echo ee('View')->make('file:publish')->render(array(
                            'field_name' => 'twitter_image',
                            'value' => $twitter_image,
                            'file' => $file,
                            'title' => ($file) ? $file->title : '',
                            'is_image' => ($file && $file->isImage()),
                            'thumbnail' => ee('Thumbnail')->get($file)->url,
                            'fp_url' => $fp->getUrl(),
                            'fp_upload' => $fp_upload,
                            'fp_edit' => $fp_edit
                        ));

                        echo '<input type="hidden" name="twitter_image" id="default_twitter_image" value="', $twitter_image, '" />';
                    }
                }
                ?>
                </div>
            </div>
        </fieldset>

    </div>
</div>
