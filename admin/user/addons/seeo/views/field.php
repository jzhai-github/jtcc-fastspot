 <div class="seeo">
    <div class="seeo box">
        <h1 class="panel-heading">Standard Meta</h1>
        <div class="panel-body settings seeo">
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_title')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_input(array(
                        "id" => "{$input_prefix}_title",
                        "name" => "{$input_prefix}[title]",
                        "value" => $entry_meta["title"]
                    ))
                    ?>
                    <div class="copy" style="float: right;">
                        <?php if (! $hide_open_graph_fields) : ?>
                        <label for="copy-to-og-title" style="display: inline-block; margin: 6px 6px 0;">
                            <input type="checkbox" name="copy-to-og-title" value="y" class="js-seeo-copy-to-og-title" id="copy-to-og-title" />
                            <?=lang('seeo_copy_to_og_title')?>
                        </label>
                        <?php endif; ?>

                        <?php if (! $hide_twitter_fields) : ?>
                        <label for="copy-to-twitter-title" style="display: inline-block; margin: 6px 6px 0;">
                            <input type="checkbox" name="copy-to-twitter-title" value="y" class="js-seeo-copy-to-twitter-title" id="copy-to-twitter-title"/>
                            <?=lang('seeo_copy_to_twitter_title')?>
                        </label>
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label>
                        <?=lang('seeo_description')?>
                        <span class="seeo__instructions" id="<?=$input_prefix?>_description_count">&nbsp;</span>
                    </label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_textarea(array(
                        "id" => "{$input_prefix}_description",
                        "name" => "{$input_prefix}[description]",
                        "value" => $entry_meta["description"],
                        'rows' => 2
                    ))
                    ?>
                    <div class="copy" style="float: right;">
                        <?php if (! $hide_open_graph_fields) : ?>
                        <label for="copy-to-og-description" style="display: inline-block; margin: 6px 6px 0;">
                            <input type="checkbox" name="copy-to-og-description" value="y" class="js-seeo-copy-to-og-description" id="copy-to-og-description"/>
                            <?=lang('seeo_copy_to_og_description')?>
                        </label>
                        <?php endif; ?>

                        <?php if (! $hide_twitter_fields) : ?>
                        <label for="copy-to-twitter-description" style="display: inline-block; margin: 6px 6px 0;">
                            <input type="checkbox" name="copy-to-twitter-description" value="y" class="js-seeo-copy-to-twitter-description" id="copy-to-twitter-description"/>
                            <?=lang('seeo_copy_to_twitter_description')?>
                        </label>
                        <?php endif; ?>
                    </div>
                </div>
            </fieldset>

            <?php if (! $hide_keywords) : ?>
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label>
                        <?=lang('seeo_keywords')?>
                        <span class="seeo__instructions" id="<?=$input_prefix?>_keywords_count">&nbsp;</span>
                    </label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_textarea(array(
                        "id" => "{$input_prefix}_keywords",
                        "name" => "{$input_prefix}[keywords]",
                        "value" => $entry_meta["keywords"],
                        "rows" => 2
                    ))?>
                </div>
            </fieldset>
            <?php endif; ?>

            <?php if (! $hide_author) : ?>
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_author')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_input(array(
                        "id" => "{$input_prefix}_author",
                        "name" => "{$input_prefix}[author]",
                        "value" => $entry_meta["author"]
                    ))
                    ?>
                </div>
            </fieldset>
            <?php endif; ?>

            <?php if (! $hide_canonical_url) : ?>
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_canonical_url')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_input(array(
                        "id" => "{$input_prefix}_canonical_url",
                        "name" => "{$input_prefix}[canonical_url]",
                        "value" => $entry_meta["canonical_url"]
                    ))?>

                    <div class="copy" style="float: right;">
                        <?php if (! $hide_open_graph_fields) : ?>
                            <label for="copy-to-og-url" style="display: inline-block; margin: 6px 6px 0;">
                                <input type="checkbox" name="copy-to-og-url" value="y" class="js-seeo-copy-to-og-url" id="copy-to-og-url"/>
                                <?=lang('seeo_copy_to_og_url')?>
                            </label>
                        <?php endif; ?>

                    </div>
                </div>
            </fieldset>
            <?php endif; ?>

            <?php if (! $hide_robots) : ?>
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_robots_directive')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_dropdown("{$input_prefix}[robots]",
                        array(
                            'INDEX, FOLLOW' => 'INDEX, FOLLOW',
                            'NOINDEX, FOLLOW' => 'NOINDEX, FOLLOW',
                            'INDEX, NOFOLLOW' => 'INDEX, NOFOLLOW',
                            'NOINDEX, NOFOLLOW' => 'NOINDEX, NOFOLLOW'
                        ),
                        $entry_meta["robots"] ? : $channel_settings['default_robots'],
                        $entry_meta['robots'], array(
                        "id" => "{$input_prefix}_robots",
                    ))
                    ?>
                </div>
            </fieldset>
            <?php endif; ?>
        </div>
    </div>

    <?php if (! $hide_open_graph_fields) : ?>
    <div class="seeo box">
        <h1 class="panel-heading"><?=lang('seeo_open_graph_fields')?></h1>
        <div class="panel-body settings seeo">
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_og_title')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_input(array(
                        "id" => "{$input_prefix}_og_title",
                        "name" => "{$input_prefix}[og_title]",
                        "value" => $entry_meta["og_title"]
                    ))?>
                </div>
            </fieldset>
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_og_image')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?php if ($file_manager == 'assets'  && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) : ?>
                        <?php if (is_array($entry_meta['og_image'])) {
                            $og_image_data = array($entry_meta['og_image'][0]);
                        } else {
                            $og_image_data = array($entry_meta['og_image']);
                        }
                        echo $assets_og_image->display_field($og_image_data);
                        ?>
                    <?php else : ?>
                        <div class="publish publish_file">
                            <p>Native file areas are not currently supported.</p>
                            <p>To upload images, please install <a href="https://eeharbor.com/assets">Assets</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label>
                        <?=lang('seeo_og_description')?>
                        <span class="seeo__instructions" id="<?=$input_prefix?>_og_description_count">&nbsp;</span>
                    </label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_textarea(array(
                        "id" => "{$input_prefix}_og_description",
                        "name" => "{$input_prefix}[og_description]",
                        "value" => $entry_meta["og_description"],
                        'rows' => 2
                    ))
                    ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_og_type')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_dropdown("{$input_prefix}[og_type]",
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
                        $entry_meta["og_type"],
                        $entry_meta['og_type'], array(
                            "id" => "{$input_prefix}_og_type",
                        ))
                    ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label>
                        <?=lang('seeo_og_url')?>
                        <?php if (!empty($entry_meta['og_url'])) : ?>
                            <span class="seeo__instructions"><a href="https://developers.facebook.com/tools/debug/og/object?q=<?=$entry_meta['og_url']?>" target="_blank">View In OG Debugger</a></span>
                        <?php endif; ?>
                    </label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_input(array(
                        "id" => "{$input_prefix}_og_url",
                        "name" => "{$input_prefix}[og_url]",
                        "value" => $entry_meta["og_url"]
                    ))
                    ?>
                </div>
            </fieldset>
        </div>
    </div>
    <?php endif; ?>

    <?php if (! $hide_twitter_fields) : ?>
    <div class="seeo box">
        <h1 class="panel-heading"><?=lang('seeo_twitter_cards')?></h1>
        <div class="panel-body settings seeo">
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_twitter_title')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_input(array(
                        "id" => "{$input_prefix}_twitter_title",
                        "name" => "{$input_prefix}[twitter_title]",
                        "value" => $entry_meta["twitter_title"]
                    ))
                    ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label>
                        <?=lang('seeo_twitter_description')?>
                        <span class="seeo__instructions" id="<?=$input_prefix?>_twitter_description_count">&nbsp;</span>
                    </label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_textarea(array(
                        "id" => "{$input_prefix}_twitter_description",
                        "name" => "{$input_prefix}[twitter_description]",
                        "value" => $entry_meta["twitter_description"],
                        'rows' => 2
                    ))
                    ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_twitter_content_type')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_dropdown("{$input_prefix}[twitter_content_type]",
                        array(
                            '' => '- Select -',
                            'summary' => 'Summary',
                            'summary_large_image' => 'Summary - Large Image',
                            'photo' => 'Photo',
                            'app' => 'App',
                            'product' => 'Product'
                        ),
                        $entry_meta["twitter_content_type"],
                        $entry_meta['twitter_content_type'], array(
                            "id" => "{$input_prefix}_twitter_content_type",
                        ))
                    ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_twitter_image')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?php if ($file_manager == 'assets' && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) { ?>
                        <?php if (is_array($entry_meta['twitter_image'])) {
                            $twitter_image_data = array($entry_meta['twitter_image'][0]);
                        } else {
                            $twitter_image_data = array($entry_meta['twitter_image']);
                        }
                        echo $assets_twitter_image->display_field($twitter_image_data);
                        ?>
                    <?php } else { ?>
                        <div class="publish publish_file">
                            <p>Native file areas are not currently supported.</p>
                            <p>To upload images, please install <a href="https://eeharbor.com/assets">Assets</a>.</p>
                        </div>
                    <?php } ?>
                </div>
            </fieldset>
        </div>
    </div>
    <?php endif; ?>

    <?php if (! $hide_sitemap_fields) : ?>
    <div class="seeo box">
        <h1 class="panel-heading"><?=lang('seeo_sitemap_options')?></h1>
        <div class="panel-body settings seeo">
            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_sitemap_priority')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_dropdown("{$input_prefix}[sitemap_priority]",
                        array(
                            '0.0' => '0.0',
                            '0.1' => '0.1',
                            '0.2' => '0.2',
                            '0.3' => '0.3',
                            '0.4' => '0.4',
                            '0.5' => '0.5 - Default',
                            '0.6' => '0.6',
                            '0.7' => '0.7',
                            '0.8' => '0.8',
                            '0.9' => '0.9',
                            '1.0' => '1.0',
                        ),
                        $entry_meta["sitemap_priority"] ? : $channel_settings['default_sitemap_priority'],
                        $entry_meta['sitemap_priority'], array(
                            "id" => "{$input_prefix}_sitemap_priority",
                        ))
                    ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_sitemap_change_frequency')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <?= form_dropdown("{$input_prefix}[sitemap_change_frequency]",
                        array(
                            'always' => 'Always',
                            'hourly' => 'Hourly',
                            'daily' => 'Daily',
                            'weekly' => 'Weekly',
                            'monthly' => 'Monthly',
                            'yearly' => 'Yearly',
                            'never' => 'Never'
                        ),
                        $entry_meta["sitemap_change_frequency"] ? $entry_meta["sitemap_change_frequency"] : $channel_settings['default_sitemap_change_frequency'],
                        $entry_meta['sitemap_change_frequency'], array(
                            "id" => "{$input_prefix}_sitemap_change_frequency",
                        ))
                    ?>
                </div>
            </fieldset>

            <fieldset class="col-group">
                <div class="setting-txt col w-4">
                    <label><?=lang('seeo_include_in_sitemap')?></label>
                </div>
                <div class="setting-field col w-12 last">
                    <label style="display: inline-block; margin-right: 1em;" for="<?= $input_prefix ?>_sitemap_include_y">
                        <input type="radio"
                               id="<?=$input_prefix?>_sitemap_include_y"
                               name="<?=$input_prefix?>[sitemap_include]"
                               value="y"
                               class="seeo_enable_disable"
                            <?php if ($entry_meta['sitemap_include']) : ?>
                                <?php if ($entry_meta['sitemap_include'] == 'y') : ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if (isset($channel_settings['sitemap_include'])) : ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php endif; ?>
                            /> Yes
                    </label>

                    <label style="display: inline-block" for="<?= $input_prefix ?>_sitemap_include_n">
                        <input type="radio"
                               id="<?=$input_prefix?>_sitemap_include_n"
                               name="<?=$input_prefix?>[sitemap_include]"
                               value="n"
                               class="seeo_enable_disable"
                            <?php if ($entry_meta['sitemap_include']) : ?>
                                <?php if ($entry_meta['sitemap_include'] == 'n') : ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php else : ?>
                                <?php if (! isset($channel_settings['sitemap_include'])) : ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php endif; ?>
                            /> No
                    </label>
                </div>
            </fieldset>
        </div>
    </div>
<?php endif; ?>
