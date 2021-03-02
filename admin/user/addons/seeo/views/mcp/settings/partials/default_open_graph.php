<div class="box panel">
    <h1 class="panel-heading"><?=lang('seeo_default_open_graph_data')?></h1>

    <div class="panel-body settings seeo">
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_og_title"><?=lang('seeo_default_og_title')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="og_title" id="default_og_title" value="<?=htmlspecialchars($og_title)?>"/>
            </div>
        </fieldset>
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_og_description"><?=lang('seeo_default_og_description')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <textarea name="og_description" id="default_og_description" cols="30" rows="3"><?=htmlspecialchars($og_description)?></textarea>
            </div>
        </fieldset>
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_og_type"><?=lang('seeo_default_og_type')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <select name="og_type" id="default_og_type">
                    <option value=""<?php if ($og_type == '') : ?> selected="selected"<?php endif; ?>>-- Select --</option>
                    <option value="article"<?php if ($og_type == 'article') : ?> selected="selected"<?php endif; ?>>Article</option>
                    <option value="book" <?php if ($og_type == 'book') : ?> selected="selected"<?php endif; ?>>Book</option>
                    <option value="music.song" <?php if ($og_type == 'music.song') : ?> selected="selected"<?php endif; ?>>Music - Song</option>
                    <option value="music.album" <?php if ($og_type == 'music.album') : ?> selected="selected"<?php endif; ?>>Music - Album</option>
                    <option value="music.playlist" <?php if ($og_type == 'music.playlist') : ?> selected="selected"<?php endif; ?>>Music - Playlist</option>
                    <option value="music.radio_station" <?php if ($og_type == 'music.radio_station') : ?> selected="selected"<?php endif; ?>>Music - Radio Station</option>
                    <option value="profile" <?php if ($og_type == 'profile') : ?> selected="selected"<?php endif; ?>>Profile</option>
                    <option value="video.movie" <?php if ($og_type == 'video.movie') : ?> selected="selected"<?php endif; ?>>Video - Movie</option>
                    <option value="video.episode" <?php if ($og_type == 'video.episode') : ?> selected="selected"<?php endif; ?>>Video - Episode</option>
                    <option value="video.tv_show" <?php if ($og_type == 'video.tv_show') : ?> selected="selected"<?php endif; ?>>Video - TV Show</option>
                    <option value="video.other" <?php if ($og_type == 'video.other') : ?> selected="selected"<?php endif; ?>>Video - Other</option>
                    <option value="website" <?php if ($og_type == 'website') : ?> selected="selected"<?php endif; ?>>Website</option>
                </select>
            </div>
        </fieldset>
        <fieldset class="col-group">
            <div class="setting-txt col w-4">
                <label for="default_og_url"><?=lang('seeo_default_og_url')?></label>
            </div>
            <div class="setting-field col w-12 last">
                <input type="text" name="og_url" id="default_og_url" value="<?=htmlspecialchars($og_url)?>"/>
            </div>
        </fieldset>
        <fieldset class="col-group last">
            <div class="setting-txt col w-4">
                <label for="default_og_image"><?=lang('seeo_default_og_image')?></label>
                <span class="seeo__instructions field-instruct"><em><?=lang('seeo_default_og_image_instructions')?></em></span>
            </div>
            <div class="setting-field col w-12 last">
                <div class="field-control">
                <?php
                if ($file_manager == 'assets' && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) {
                    echo $assets_og_image->display_field($og_image);
                } else {
                    if ($flux->ver_gte(5)) {
                        echo ee()->file_field->dragAndDropField('og_image', $og_image, 'all', 'image');
                    } else {
                        $fp = ee('CP/FilePicker')->make('all');

                        $fp_link = $fp->getLink()
                            ->withValueTarget('og_image')
                            ->withNameTarget('og_image')
                            ->withImage('og_image');

                        ee()->lang->loadfile('filemanager');

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

                        if (preg_match('/^{filedir_(\d+)}/', $og_image, $matches)) {
                            // Set upload directory ID and file name
                            $dir_id = $matches[1];
                            $file_name = str_replace($matches[0], '', $og_image);

                            $file = ee('Model')->get('File')
                                ->filter('file_name', $file_name)
                                ->filter('upload_location_id', $dir_id)
                                ->filter('site_id', ee()->config->item('site_id'))
                                ->first();
                        } elseif (! empty($og_image) && is_numeric($og_image)) {
                            // If file field is just a file ID
                            $file = ee('Model')->get('File', $og_image)->first();
                        }

                        echo ee('View')->make('file:publish')->render(array(
                            'field_name' => 'og_image',
                            'value' => $og_image,
                            'file' => $file,
                            'title' => ($file) ? $file->title : '',
                            'is_image' => ($file && $file->isImage()),
                            'thumbnail' => ee('Thumbnail')->get($file)->url,
                            'fp_url' => $fp->getUrl(),
                            'fp_upload' => $fp_upload,
                            'fp_edit' => $fp_edit
                        ));
                    }
                }
                ?>
                </div>
            </div>
        </fieldset>
    </div>
</div>
