    <h2><?=lang('seeo_open_graph_fields')?></h2>

        <!-- OG: Title -->
        <fieldset>
            <div class="field-instruct">
                <label for='seeo__seeo_og_title'><?=lang('seeo_og_title')?></label>
            </div>
            <div class="field-control">
                <input id='seeo__seeo_og_title' type="text" name="meta[og_title]" value="<?= $meta->og_title ?>"/>
            </div>
        </fieldset>

        <!-- OG:Image -->
        <fieldset>
            <div class="field-instruct">
                <label for='seeo__seeo_og_image'><?=lang('seeo_og_image')?></label>
            </div>
            <div class="field-control">
                <?php if (ee()->seeo_settings->get(0, 'file_manager') == 'assets' && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) : ?>
                    <?= $meta->assets_og_image->display_field(array($og_image)); ?>
                <?php else : ?>
                    <input name="seeo__seeo_og_image" id="seeo__seeo_og_image" type="hidden" value='<?= $og_image ?>'>
                    <img src="<?= $og_image_thumbnail ?>" max-height="100" max-width="100">
                    <p><?= $og_image_button ?></p>
                <?php endif; ?>
            </div>
        </fieldset>

        <!-- OG:Description -->
        <fieldset>
            <div class="field-instruct">
                <label for='seeo__seeo_og_description'><?=lang('seeo_og_description')?></label>
                <span class="seeo__instructions field-instruct"><em><span id="seeo__seeo_og_description_count"></span></em></span>
            </div>
            <div class="field-control">
                <textarea id="seeo__seeo_og_description" name="meta[og_description]" cols="90" rows="2"><?= $meta->og_description ?></textarea>
            </div>
        </fieldset>

        <!-- OG:Type -->
        <fieldset>
            <div class="field-instruct">
                <label for='seeo__seeo_og_type'><?=lang('seeo_og_type')?></label>
            </div>
            <div class="field-control">
                <?= form_dropdown(
                    "meta[og_type]",
                    array(
                                '' => '- Select -',
                                'article' => 'Article',
                                'book' => 'Book',
                                'music.song' => 'Music - Song',
                                'music.album' => 'Music - Album',
                                'music.playlist' => 'Music - Playlist',
                                'music.radio_station' => 'Music - Radio Station',
                                'profile' => 'Profile',
                                'video.movie' => 'Video - Movie',
                                'video.episode' => 'Video - Episode',
                                'video.tv_show' => 'Video - TV Show',
                                'video.other' => 'Video - Other',
                                'website' => 'Website'
                            ),
                    $meta->og_type,
                    'id="seeo__seeo_og_type"'
                )
                ?>
            </div>
        </fieldset>

        <!-- OG:URL -->
        <fieldset>
            <div class="field-instruct">
                <label for='seeo__seeo_og_url'><?=lang('seeo_og_url')?></label>
            </div>
            <div class="field-control">
                <input id="seeo__seeo_og_url" type="text" name="meta[og_url]" value="<?= $meta->og_url ?>"/>
            </div>
        </fieldset>
