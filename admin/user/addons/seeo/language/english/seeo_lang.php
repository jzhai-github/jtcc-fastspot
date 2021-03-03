<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * SEEO language file
 *
 * @package         SEEO
 * @author          Tom Jaeger <Tom@EEHarbor.com>
 * @link            https://eeharbor.com
 * @copyright       Copyright (c) 2018, Tom Jaeger/EEHarbor
 */

$lang = array(
    'seeo_module_name'                              => "SEEO",
    'seeo_page_title'                               => "SEEO (sē'ō): Search ExpressionEngine Optimization",
    'seeo_module_description'                       => 'Modern Meta Management',
    'seeo'                                          => 'SEEO',
    'seeo_settings'                                 => 'Settings',
    'seeo_changes_saved'                            => 'Changes Saved',
    'seeo_entries_missing_meta'                     => 'Audit Entries',

    // --------------------------------------
    // Template Page Meta Nav
    // --------------------------------------
    'seeo_nav_audit'                                => 'Audit Entries',
    'seeo_nav_settings'                             => 'SEEO Settings',
    'seeo_nav_template_page_meta'                   => 'Template Page Meta',
    'seeo_nav_migrate'                              => 'Migration Tool',
    'seeo_nav_documentation'                        => 'Documentation',
    'seeo_nav_license'                              => 'License',
    'seeo_nav_default_settings'                     => 'Default Meta Settings',
    'seeo_nav_channel_settings'                     => 'Channel Settings',

    // --------------------------------------
    // Template Page Meta
    // --------------------------------------
    'seeo_template_page_meta'                       => 'Template Page Meta',
    'seeo_template_page_meta_instructions'          => 'Template Page Meta allows you to create meta data for a page that may not have an Entry associated with it like a blog listing page, member profile, or other dynamic page.',
    'seeo_template_page_meta_create'                => 'Create Template Page Meta',
    'seeo_template_page_meta_update'                => 'Update Template Page Meta',
    'seeo_template_page_meta_path'                  => 'Template Page Path',
    'seeo_template_page_meta_title'                 => 'Meta Title',
    'seeo_template_page_settings'                   => 'Template Page Meta Settings',
    'seeo_template_page_path'                       => 'Template Page Meta Path',
    'seeo_template_page_path_description'           => 'Enter the path that matches the page you want (i.e. /blog or /login)',
    'seeo_template_page_channel'                    => 'Template Page Meta Channel',
    'seeo_template_page_channel_description'        => 'Selecting a template page channel will find the most recently edited entry from that channel, and use it\'s date for the &lt;lastmod&gt; tag. This is useful, for example, if you have a Blog Listing page, choose the channel your blog entries are in and the Blog Listing page will have a "last modified date" of the last time a blog entry was added / edited.',

    'seeo_migrate'                                  => 'Migrate Meta',
    'seeo_remove_source'                            => 'Remove Source',
    'seeo_file_manager'                             => 'File Manager',
    'seeo_default_file_manager'                     => 'Default File Manager',
    // 'seeo_default_file_manager_instructions'        => 'Changing this will affect all of your SEEO enabled channels and will cause data loss for any previously selected images!',
    'seeo_default_file_manager_instructions'        => 'Choose which file manager to use.<br /><br />If you change this, you must save your settings before the file fields below will change.',
    'seeo_are_you_sure'                             => 'Are you sure? You can\'t undo this action.',
    'seeo_cancel_go_back'                           => 'Cancel, go back',
    'seeo_confirm'                                  => 'Proceed',
    'seeo_title_divider'                            => 'Title Divider',
    'seeo_title_divider_instructions'               => 'The character that will be used to separate elements of the Meta Title',

    'seeo_open_graph'                               => 'Open Graph',
    'seeo_twitter_cards'                            => 'Twitter Cards',
    'seeo_sitemap'                                  => 'Sitemap',

    // --------------------------------------
    // Settings
    // --------------------------------------
    'seeo_channel_settings'                         => 'Channel Settings Meta',
    'seeo_channel_settings_instructions'            => 'Change the default settings for this channel below.<br /><br />SEEO will display content in the following order:<br /><ol style="margin-left:30px;margin-bottom:0;"><li>Specific Entry Values</li><li><strong>Channel Default Values (this page)</strong></li><li>SEEO Default Values</li></ol>',
    'seeo_enabled'                                  => 'Enabled',
    'seeo_hide_standard_keywords_field'             => 'Hide standard keywords field?',
    'seeo_hide_author_field'                        => 'Hide author field?',
    'seeo_hide_canonical_url_field'                 => 'Hide canonical url field?',
    'seeo_hide_robots_field'                        => 'Hide robots field?',
    'seeo_hide_open_graph_fields'                   => 'Hide Open Graph fields?',
    'seeo_hide_twitter_card_fields'                 => 'Hide Twitter Card fields?',
    'seeo_display_sitemap_fields'                   => 'Display Sitemap fields?',
    'seeo_default_template'                         => 'Default Template',
    'seeo_default_template_description'             => 'Enter your custom template code below. Leave blank to use the global default.',
    'seeo_save_settings'                            => 'Save Settings',

    // --------------------------------------
    // Fieldtype Specific
    // --------------------------------------
    'seeo_copy_to_og_title'                         => 'Copy to OG: Title?',
    'seeo_copy_to_twitter_title'                    => 'Copy to Twitter Title?',
    'seeo_copy_to_og_description'                   => 'Copy to OG: Description?',
    'seeo_copy_to_og_url'                           => 'Copy to OG: URL?',
    'seeo_copy_to_twitter_description'              => 'Copy to Twitter Description?',

    // --------------------------------------
    // Default Fields
    // --------------------------------------
    'seeo_standard_meta_tags'                       => 'Standard Meta Tags',
    'seeo_default_settings_instructions'            => 'Change the default settings for SEEO below.<br /><br />SEEO will display content in the following order:<br /><ol style="margin-left:30px;margin-bottom:0;"><li>Specific Entry Values</li><li>Channel Default Values</li><li><strong>SEEO Default Values (this page)</strong></li></ol>',
    'seeo_default_standard_meta'                    => 'Default Standard Meta',
    'seeo_default_site_title'                       => 'Default Site Title',
    'seeo_default_site_title_instructions'          => 'The title displayed in search engines results. Maximum length 70 characters. Will be appended to the Entry SEEO Title with the "Title Divider" unless you pass <code>hide_site_title="y"</code> in your <code>{exp:seeo:single}</code> tag.<br />Ex: Your Entry SEEO Title - Default Site Title',
    'seeo_default_description'                      => 'Default Description',
    'seeo_default_description_instructions'         => 'Description displayed in search engine results. Maximum length 155 characters.',
    'seeo_default_keywords'                         => 'Default Keywords',
    'seeo_default_author'                           => 'Default Author',
    'seeo_default_title_prefix'                     => 'Default Title Prefix',
    'seeo_default_title_prefix_instructions'        => 'Added to the beginning of the Entry SEEO Title using the "Title Divider".<br />Ex: Title Prefix - Entry SEEO Title - Default Site Title',
    'seeo_default_title_suffix'                     => 'Default Title Suffix',
    'seeo_default_title_suffix_instructions'        => 'Added to the end of the Entry SEEO Title using the "Title Divider". Will appear BEFORE the "Default Site Title".<br />Ex: Entry SEEO Title - Title Suffix - Default Site Title',
    'seeo_default_robots_directive'                 => 'Default Robots Directive',

    // --------------------------------------
    // Standard Meta
    // --------------------------------------
    'seeo_standard_title'                           => 'Title',
    'seeo_standard_title_instructions'              => 'Enter a custom title for this entry',
    'seeo_standard_description'                     => 'Description',
    'seeo_standard_description_instructions'        => 'Enter description for this entry',
    'seeo_standard_keywords'                        => 'Keywords',
    'seeo_standard_keywords_instructions'           => 'Enter comma separated list of keywords',
    'seeo_standard_author'                          => 'Author',
    'seeo_standard_canonical_url'                   => 'Canonical URL',
    'seeo_standard_canonical_url_instructions'      => 'Specific the canonical or "primary" URL for this page. For more information about canonical URLs, visit <a href="https://developers.facebook.com/docs/sharing/webmasters/getting-started/versioned-link/" target="_blank">Facebook: Specify a Canonical URL</a>',
    'seeo_standard_robots_directive'                => 'Robots Directive',
    'seeo_standard_robots_index_instructions'       => 'How should robots treat this entry?<br><br>',

    // --------------------------------------
    // Open Graph
    // --------------------------------------
    'seeo_open_graph_fields'                        => 'Open Graph <small>- Facebook, Pinterest, &amp; Google + </small>',
    'seeo_og_title'                                 => 'OG: Title',
    'seeo_og_image'                                 => 'OG: Image',
    'seeo_og_description'                           => 'OG: Description',
    'seeo_og_type'                                  => 'OG: Type',
    'seeo_og_url'                                   => 'OG: URL',
    'seeo_default_open_graph_data'                  => 'Default Open Graph Data <small> - Facebook, Google +, Pinterest</small>',
    'seeo_default_og_title'                         => 'Default OG: Title',
    'seeo_default_og_description'                   => 'Default OG: Description',
    'seeo_default_og_type'                          => 'Default OG: Type',
    'seeo_default_og_url'                           => 'Default OG: URL',
    'seeo_default_og_image'                         => 'Default OG: Image',
    'seeo_default_og_image_instructions'            => 'The "share" image. 1200 x 630 pixels minimum is best, 600 x 315 pixels minimum is next best.',

    // --------------------------------------
    // Twitter
    // --------------------------------------
    'seeo_twitter_fields'                           => 'Twitter Cards',
    'seeo_twitter_fields_instructions'              => 'With Twitter Cards, you can attach rich photos, videos and media experiences to Tweets, helping to drive traffic to your website.',
    'seeo_twitter_title'                            => 'Twitter: Card Title',
    'seeo_twitter_description'                      => 'Twitter: Card Description',
    'seeo_twitter_content_type'                     => 'Twitter: Card Type',
    'seeo_twitter_content_type_instructions'        => 'Choose the type of Twitter Card to display. For information and examples of each type, visit <a target="_blank" href="https://developer.twitter.com/en/docs/tweets/optimize-with-cards/overview/abouts-cards">Twitter: About Twitter Cards</a>',
    'seeo_twitter_image'                            => 'Twitter: Image',
    'seeo_default_twitter_cards'                    => 'Default Twitter Card Data',
    'seeo_default_twitter_title'                    => 'Default Twitter Title',
    'seeo_default_twitter_title_instructions'       => 'Summary title less than 70 characters',
    'seeo_default_twitter_description'              => 'Default Twitter Description',
    'seeo_default_twitter_description_instructions' => 'Page description less than 200 characters',
    'seeo_default_twitter_type'                     => 'Default Twitter Content Type',
    'seeo_default_twitter_image'                    => 'Default Twitter Image',
    'seeo_default_twitter_image_instructions'       => '',
    'seeo_default_twitter_image_instructions'       => 'Unique summary image. Must be at least 120px 120px and less than 1MB in size.',

    // --------------------------------------
    // Sitemap
    // --------------------------------------
    'seeo_hide_sitemap_fields'                      => 'Hide sitemap fields?',
    'seeo_sitemap_default_options'                  => 'Default Sitemap Options',
    'seeo_sitemap_options'                          => 'Sitemap Options',
    'seeo_sitemap_priority'                         => 'Sitemap: Priority',
    'seeo_sitemap_priority_instructions'            => 'The priority of this URL relative to other URLs on your site. Valid values range from 0.0 to 1.0. This value does not affect how your pages are compared to pages on other sites—it only lets the search engines know which pages you deem most important for the crawlers. The default priority of a page is 0.5. Please note that the priority you assign to a page is not likely to influence the position of your URLs in a search engine\'s result pages. Search engines may use this information when selecting between URLs on the same site, so you can use this tag to increase the likelihood that your most important pages are present in a search index. Also, please note that assigning a high priority to all of the URLs on your site is not likely to help you. Since the priority is relative, it is only used to select between URLs on your site.',
    'seeo_sitemap_change_frequency'                 => 'Sitemap: Change Frequency',
    'seeo_sitemap_change_frequency_instructions'    => 'How frequently the page is likely to change. This value provides general information to search engines and may not correlate exactly to how often they crawl the page. The value "always" should be used to describe documents that change each time they are accessed. The value "never" should be used to describe archived URLs. Please note that the value of this tag is considered a hint and not a command. Even though search engine crawlers may consider this information when making decisions, they may crawl pages marked "hourly" less frequently than that, and they may crawl pages marked "yearly" more frequently than that. Crawlers may periodically crawl pages marked "never" so that they can handle unexpected changes to those pages.',
    'seeo_sitemap_include'                          => 'Sitemap: Include in Sitemap?',
    'seeo_sitemap_include_simple'                   => 'Include in Sitemap',
    'seeo_sitemap_include_instructions'             => 'Choose whether this page is displayed in the Sitemap or not. For some pages, you may want SEO data applied but not necessarily have them show up in the full sitemap.',
    'seeo_sitemap_include_instructions_channel'     => 'Choose whether all entries in this channel are displayed in the Sitemap or not. Note: Individual pages can override this setting.',
    'seeo_sitemap_include_instructions_global'      => 'Choose whether all entries in the site are displayed in the Sitemap or not. Note: Individual channels and/or pages can override this setting.',
    'seeo_default_sitemap_priority'                 => 'Default Sitemap Priority',
    'seeo_default_sitemap_priority_instructions'    => 'The priority given to this item in the XML sitemap',
    'seeo_default_sitemap_change_frequency'         => 'Default Sitemap Change Frequency',
    'seeo_default_sitemap_include'                  => 'Default to Including Entries in Sitemap?',

    // --------------------------------------
    // Validation
    // --------------------------------------
    'seeo_template_page_path_required'              => 'You must enter a value for the Template Page Meta Path',

    // --------------------------------------
    // Audit
    // --------------------------------------
    'seeo_audit_channel' => 'Channel',
    'seeo_audit_num_entries' => '# Entries',
);
