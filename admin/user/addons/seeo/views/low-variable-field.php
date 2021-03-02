<?php if($file_manager == 'default') {
    ee()->file_field->browser();
} ?>

<div class="seeo">
    <table>
        <caption>Standard Meta</caption>
        <col width="250px"/>
        <tbody>
        <tr>
            <th scope="row">
                <label for="var_<?=$var_id?>_title">
                    <?=lang('seeo_title')?>
                </label>
            </th>
            <td colspan="2">
                <?= form_input(array(
                    "id" => "var_{$var_id}_title",
                    "name" => "var[$var_id][title]",
                    "value" => $var_data['title'],
                ))
                ?>
                <div class="copy" style="float: right;">
                    <?php if( ! $hide_open_graph_fields ): ?>
                        <label style="display: inline-block; margin-top: 6px;">
                            <input type="checkbox" name="copy-to-og-title" value="y" class="js-seeo-copy-to-og-title" data-target="var_<?=$var_id?>_og_title" data-source="var_<?=$var_id?>_title" />
                            <?=lang('seeo_copy_to_og_title')?>
                        </label>
                    <?php endif; ?>
                    <?php if( ! $hide_twitter_fields ): ?>
                        <label style="display: inline-block; margin: 6px 6px 0;">
                            <input type="checkbox" name="copy-to-twitter-title" value="y" class="js-seeo-copy-to-twitter-title"  data-target="var_<?=$var_id?>_twitter_title" data-source="var_<?=$var_id?>_title" />
                            <?=lang('seeo_copy_to_twitter_title')?>
                        </label>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <tr>
            <th scope="row">
                <label for="var_<?=$var_id?>_description">
                    <?=lang('seeo_description')?>
                </label>
                <span class="seeo__instructions" id="var_<?=$var_id?>_description_count">&nbsp;</span>
            </th>
            <td colspan="2">
                <?= form_textarea(array(
                    "id" => "var_{$var_id}_description",
                    "name" => "var[$var_id][description]",
                    "value" => $var_data["description"],
                    'rows' => 2,
                    'class' => "js-seeo-description",
                    'data-counter' => "var_{$var_id}_description_count"
                ))
                ?>
                <div class="copy" style="float: right;">
                    <?php if( ! $hide_open_graph_fields ): ?>
                        <label style="display: inline-block; margin: 6px 6px 0;">
                            <input type="checkbox" name="copy-to-og-description" value="y" class="js-seeo-copy-to-og-description"  data-target="var_<?=$var_id?>_og_description" data-source="var_<?=$var_id?>_description" />
                            <?=lang('seeo_copy_to_og_description')?>
                        </label>
                    <?php endif; ?>

                    <?php if( ! $hide_twitter_fields ): ?>
                        <label style="display: inline-block; margin: 6px 6px 0;">
                            <input type="checkbox" name="copy-to-twitter-description" value="y" class="js-seeo-copy-to-twitter-description"  data-target="var_<?=$var_id?>_twitter_description" data-source="var_<?=$var_id?>_description" />
                            <?=lang('seeo_copy_to_twitter_description')?>
                        </label>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <?php if( ! $hide_keywords ): ?>
            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_keywords">
                        <?=lang('seeo_keywords')?>
                    </label>
                    <span class="seeo__instructions" id="var_<?=$var_id?>_keywords_count">&nbsp;</span>
                </th>
                <td colspan="2">
                    <?= form_textarea(array(
                        "id" => "var_{$var_id}_keywords",
                        "name" => "var[$var_id][keywords]",
                        "value" => $var_data["keywords"],
                        "rows" => 2,
                        'class' => "js-seeo-keywords",
                        'data-counter' => "var_{$var_id}_keywords_count"
                    ))?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if( ! $hide_author ): ?>
            <tr>
                <th scope="row">
                    <label for="var_<?= $var_id ?>_author">
                        <?=lang('seeo_author')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_input(array(
                        "id" => "var_{$var_id}_author",
                        "name" => "var[$var_id][author]",
                        "value" => $var_data["author"]
                    ))
                    ?>
                </td>
            </tr>
        <?php endif; ?>

        <?php if( ! $hide_canonical_url ): ?>
            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_canonical_url">
                        <?=lang('seeo_canonical_url')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_input(array(
                        "id" => "var_{$var_id}_canonical_url",
                        "name" => "var[$var_id][canonical_url]",
                        "value" => $var_data["canonical_url"]
                    ))?>

                    <div class="copy" style="float: right;">
                        <?php if( ! $hide_open_graph_fields ): ?>
                            <label style="display: inline-block; margin: 6px 6px 0;">
                                <input type="checkbox" name="copy-to-og-url" value="y" class="js-seeo-copy-to-og-url" id="copy-to-og-url" data-target="var_<?=$var_id?>_og_url" data-source="var_<?=$var_id?>_canonical_url" />
                                <?=lang('seeo_copy_to_og_url')?>
                            </label>
                        <?php endif; ?>

                    </div>
                </td>
            </tr>
        <?php endif; ?>

        <?php if( ! $hide_robots ): ?>
            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_robots">
                        <?=lang('seeo_robots_directive')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_dropdown("var[$var_id][robots]",
                        array(
                            'INDEX, FOLLOW' => 'INDEX, FOLLOW',
                            'NOINDEX, FOLLOW' => 'NOINDEX, FOLLOW',
                            'INDEX, NOFOLLOW' => 'INDEX, NOFOLLOW',
                            'NOINDEX, NOFOLLOW' => 'NOINDEX, NOFOLLOW'
                        ),
                        $var_data["robots"],
                        $var_data['robots'], array(
                            "id" => "var_{$var_id}_robots",
                        ))
                    ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if( ! $hide_open_graph_fields ): ?>
    <div class="seeo">
        <table>
            <caption>
                <?=lang('seeo_open_graph_fields')?>
            </caption>
            <col width="250px"/>

            <tbody>
            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_og_title">
                        <?=lang('seeo_og_title')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_input(array(
                        "id" => "var_{$var_id}_og_title",
                        "name" => "var[$var_id][og_title]",
                        "value" => $var_data["og_title"]
                    ))?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="">
                        <?=lang('seeo_og_image')?>
                    </label>
                </th>
                <td colspan="2">
                    <?php if($file_manager == 'assets'  && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()): ?>
                        <?php if(is_array($var_data['og_image'])) {
                            $og_image_data = array($var_data['og_image'][0]);
                        } else {
                            $og_image_data = array($var_data['og_image']);
                        }
                        echo $assets_og_image->display_field($og_image_data);
                        ?>
                    <?php else: ?>
                        <div class="publish publish_file">
                            <?php if(is_array($var_data['og_image'])) {
                                if(isset($var_data['og_image'][1])) {
                                    $og_image_data = ee()->file_field->format_data($var_data['og_image'][0], $var_data['og_image'][1]);
                                } else {
                                    $og_image_data = null;
                                }
                            } else {
                                $og_image_data = $var_data['og_image'];
                            }
                            echo ee()->file_field->field(
                                "var[$var_id][og_image][]",
                                $og_image_data,
                                'all',
                                'image',
                                true,
                                null
                            )
                            ?>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_og_description">
                        <?=lang('seeo_og_description')?>
                    </label>
                    <span class="seeo__instructions" id="var_<?=$var_id?>_og_description_count">&nbsp;</span>
                </th>
                <td colspan="2">
                    <?= form_textarea(array(
                        "id" => "var_{$var_id}_og_description",
                        "name" => "var[$var_id][og_description]",
                        "value" => $var_data["og_description"],
                        'rows' => 2,
                        'class' => "js-seeo-og-description",
                        'data-counter' => "var_{$var_id}_og_description_count"
                    ))
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_og_type">
                        <?=lang('seeo_og_type')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_dropdown("var[$var_id][og_type]",
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
                        $var_data["og_type"],
                        $var_data['og_type'], array(
                            "id" => "var_{$var_id}_og_type",
                        ))
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_og_url">
                        <?=lang('seeo_og_url')?>
                    </label>
                    <?php if( !empty($var_data['og_url'])): ?>
                        <span class="seeo__instructions"><a href="https://developers.facebook.com/tools/debug/og/object?q=<?=$var_data['og_url']?>" target="_blank">View In OG Debugger</a></span>
                    <?php endif; ?>
                </th>
                <td colspan="2">
                    <?= form_input(array(
                        "id" => "var_{$var_id}_og_url",
                        "name" => "var[$var_id][og_url]",
                        "value" => $var_data["og_url"]
                    ))
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if( ! $hide_twitter_fields ): ?>
    <div class="seeo">
        <table>
            <caption>
                <?=lang('seeo_twitter_cards')?>
            </caption>
            <col width="250px"/>

            <tbody>
            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_twitter_title">
                        <?=lang('seeo_twitter_title')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_input(array(
                        "id" => "var_{$var_id}_twitter_title",
                        "name" => "var[$var_id][twitter_title]",
                        "value" => $var_data["twitter_title"]
                    ))
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_twitter_description">
                        <?=lang('seeo_twitter_description')?>
                    </label>
                    <span class="seeo__instructions" id="var_<?=$var_id?>_twitter_description_count">&nbsp;</span>
                </th>
                <td colspan="2">
                    <?= form_textarea(array(
                        "id" => "var_{$var_id}_twitter_description",
                        "name" => "var[$var_id][twitter_description]",
                        "value" => $var_data["twitter_description"],
                        'rows' => 2,
                        'class' => "js-seeo-twitter-description",
                        'data-counter' => "var_{$var_id}_twitter_description_count"
                    ))
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_twitter_content_type">
                        <?=lang('seeo_twitter_content_type')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_dropdown("var[$var_id][twitter_content_type]",
                        array(
                            '' => '- Select -',
                            'summary' => 'Summary',
                            'summary_large_image' => 'Summary - Large Image',
                            'photo' => 'Photo',
                            'app' => 'App',
                            'product' => 'Product'
                        ),
                        $var_data["twitter_content_type"],
                        $var_data['twitter_content_type'], array(
                            "id" => "var_{$var_id}_twitter_content_type",
                        ))
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="">
                        <?=lang('seeo_twitter_image')?>
                    </label>
                </th>
                <td colspan="2">
                    <?php if($file_manager == 'assets' && ee('Addon')->get('assets') && ee('Addon')->get('assets')->isInstalled()) { ?>
                        <?php if(is_array($var_data['twitter_image'])) {
                            $twitter_image_data = array($var_data['twitter_image'][0]);
                        } else {
                            $twitter_image_data = array($var_data['twitter_image']);
                        }
                        echo $assets_twitter_image->display_field($twitter_image_data);
                        ?>
                    <?php } else { ?>
                        <div class="publish publish_file">
                            <?php
                            if(is_array($var_data['twitter_image']))
                            {
                                if(isset($var_data['twitter_image'][1])) {
                                    $twitter_image_data = ee()->file_field->format_data($var_data['twitter_image'][0], $var_data['twitter_image'][1]);
                                } else {
                                    $twitter_image_data = null;
                                }
                            } else {
                                $twitter_image_data = $var_data['twitter_image'];
                            }
                            echo ee()->file_field->field(
                                "var[$var_id][twitter_image][]",
                                $twitter_image_data,
                                'all',
                                'image',
                                true,
                                null
                            )
                            ?>
                        </div>
                    <?php } ?>
                </td>
            </tr>

            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if(! $hide_sitemap_fields ): ?>
    <div class="seeo">
        <table>
            <caption>
                <?=lang('seeo_sitemap_options')?>
            </caption>
            <col width="250px"/>
            <tbody>
            <tr>
                <th scope="row">
                    <label for="var_<?=$var_id?>_sitemap_priority">
                        <?=lang('seeo_sitemap_priority')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_dropdown("var[$var_id][sitemap_priority]",
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
                        $var_data["sitemap_priority"] ? : $default_sitemap_priority,
                        $var_data['sitemap_priority'], array(
                            "id" => "var_{$var_id}_sitemap_priority",
                        ))
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="var_<?= $var_id ?>_sitemap_change_frequency">
                        <?=lang('seeo_sitemap_change_frequency')?>
                    </label>
                </th>
                <td colspan="2">
                    <?= form_dropdown("var[$var_id][sitemap_change_frequency]",
                        array(
                            'always' => 'Always',
                            'hourly' => 'Hourly',
                            'daily' => 'Daily',
                            'weekly' => 'Weekly',
                            'monthly' => 'Monthly',
                            'yearly' => 'Yearly',
                            'never' => 'Never'
                        ),
                        $var_data["sitemap_change_frequency"] ? $var_data["sitemap_change_frequency"] : 'daily',
                        $var_data['sitemap_change_frequency'], array(
                            "id" => "var_{$var_id}_sitemap_change_frequency",
                        ))
                    ?>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="var_<?= $var_id ?>_include_in_sitemap">
                        <?=lang('seeo_include_in_sitemap')?>
                    </label>
                </th>
                <td colspan="2">
                    <label style="display: inline-block; margin-right: 1em;" for="seeo_sitemap_include_y">
                        <input type="radio"
                               id="var_<?=$var_id?>_sitemap_include_y"
                               name="var[<?=$var_id?>][sitemap_include]"
                               value="y"
                               class="seeo_enable_disable"
                            <?php if($var_data['sitemap_include']): ?>
                                <?php if($var_data['sitemap_include'] == 'y'): ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if(isset($var_data['sitemap_include'])): ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php endif; ?>
                            /> Yes
                    </label>

                    <label style="display: inline-block" for="seeo_sitemap_include_n">
                        <input type="radio"
                               id="var_<?=$var_id?>_sitemap_include_n"
                               name="var[<?=$var_id?>][sitemap_include]"
                               value="n"
                               class="seeo_enable_disable"
                            <?php if($var_data['sitemap_include']): ?>
                                <?php if($var_data['sitemap_include'] == 'n'): ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if( ! isset($var_data['sitemap_include'])): ?>
                                    checked="checked"
                                <?php endif; ?>
                            <?php endif; ?>
                            /> No
                    </label>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
<?php endif; ?>