$(document).ready(function () {
    var body = $('body');

    var seeo = {

        init: function () {
            this.toggleSettingsPane();
            this.toggleSitemapFields();
            // this.toggleMigrateOptionsPane();
            this.channelQuickSwitcher();

            this.copyToField('.js-seeo-copy-to-og-title', '#seeo__seeo_title', '#seeo__seeo_og_title');
            this.copyToField('.js-seeo-copy-to-twitter-title', '#seeo__seeo_title', '#seeo__seeo_twitter_title');
            this.copyToField('.js-seeo-copy-to-og-description', '#seeo__seeo_description', '#seeo__seeo_og_description');
            this.copyToField('.js-seeo-copy-to-og-url', '#seeo__seeo_canonical_url', '#seeo__seeo_og_url');
            this.copyToField('.js-seeo-copy-to-twitter-description', '#seeo__seeo_description', '#seeo__seeo_twitter_description');

        },

        initCountdowns: function () {
            this.fieldCountdown('#seeo__seeo_title', '#seeo__seeo_title_count', 250);
            this.fieldCountdown('#seeo__seeo_description', '#seeo__seeo_description_count', 150);
            this.fieldCountdown('#seeo__seeo_keywords', '#seeo__seeo_keywords_count', 150);
            this.fieldCountdown('#seeo__seeo_og_description', '#seeo__seeo_og_description_count', 300);
            this.fieldCountdown('#seeo__seeo_twitter_description', '#seeo__seeo_twitter_description_count', 200);
        },

        copyToField: function(selector, source, target) {
            $(selector).change(function(){
                if($(this).is(":checked")){
                    if(body.find(source).val() != body.find(target).val())
                    {
                        $(target).val($(source).val());
                    }
                } else {
                    $(target).val('');
                }
            });

            $(source).keyup(function(){
                if(body.find(selector).is(":checked"))
                {
                    $(target).val($(source).val());
                }
            });
        },

        toggleSettingsPane: function () {
            body.find(".js-seeo-enable-disable").change(function () {

                if ($(this).val() == 'y') {
                    $(this).parent().siblings('.js-seeo-channel-options').slideDown();
                } else {
                    $(this).parent().siblings('.js-seeo-channel-options').slideUp();
                }
            });
        },

        toggleSitemapFields: function() {
            body.find(".js-seeo-hide-sitemap-fields").change(function () {
                if ($(this).is(":checked")) {
                    $(this).parent().parent().siblings('.js-seeo-sitemap-field').slideUp();
                } else {
                    $(this).parent().parent().siblings('.js-seeo-sitemap-field').slideDown();
                }
            });
        },

        // toggleMigrateOptionsPane: function()
        // {
        //     body.find(".js-seeo-migrate-options-checkbox").change(function () {

        //         if($(this).is(":checked")){
        //             $(this).parent().next('.js-seeo-migrate-options').slideDown();
        //         } else {
        //             $(this).parent().next('.js-seeo-migrate-options').slideUp();
        //         }
        //     });
        // },

        channelQuickSwitcher: function () {
            body.find('.js-seeo-channel-quick-switcher').change(function (e) {
                window.location = $(this).val();
                e.preventDefault();
            });
        },

        fieldCountdown: function (field, counter, length) {
            body.find(field).on('input', function () {
                $(counter).text((length - $(this).val().length) + " chars left");
            });
        },

        checkMigrateOptions: function () {
            $('.migration-channel-container').each(function() {
                if ($(this).find('input.migration-channel:checked').length > 0) {
                    $(this).closest('.settings').find('.btn-migrate').show();
                } else {
                    $(this).closest('.settings').find('.btn-migrate').hide();
                }
            });
        },
    };

    // Set the callback for the filepicker
    if (typeof $('.seeo-filepicker').FilePicker === "function") {
        $('.seeo-filepicker').FilePicker({
            callback: function(data, references) {
                // Close the modal
                references.modal.find('.m-close').click();

                // Find the input, and set the value
                $(references.input_img.selector).attr("value",data.file_id);

                // Find the input, and then get the img sibling, then set the thumbnail
                $(references.input_img.selector).siblings('img').attr("src",data.thumb_path);
            }
        });
    }

    // When a channel <tr> in a migration channel table is clicked, check the checkbox
    // $('.migrationChannelBox tr.channel_row').click(function(event) {
    //     if (event.target.type !== 'checkbox') {
    //         $(':checkbox', this).trigger('click');
    //     }
    // });

    // When a <tr> in zc_meta table is clicked, check the checkbox
    // $('#zcMetaTable tr').click(function(event) {
    //     if (event.target.type !== 'checkbox') {
    //         $(':checkbox', this).trigger('click');
    //     }
    // });

    // When a migration path <tr> in a migration path table is clicked, check the checkbox
    // $('.migrationPathTable tr').click(function(event) {

    //     target = $(event.target)

    //     // if we click an actual checkbox
    //     if (event.target.type === 'checkbox') { return; }

    //     // if we click in the additional options
    //     if (target.parents('.additional_options').length) { return; }

    //     // if we click additional options
    //     if (target.is('div.additional_options')) { return; }

    //     // NOTE: TODO: this still does not work. Its not a show stopper, but come back here eventually and fix this.

    //     // Trigger a click!
    //     $('.migrationPathCheckbox :checkbox', this).trigger('click');
    // });

    // When a checkbox is clicked in a migration channel table, show/hide the next row (which is a migration path row)
    $('.migration-channel-container .migration-channel').change(function() {
        let $box = $(this).closest('.migration-channel-box');
        let $next = $box.find('.migration-detail');
        let $btn = $box.siblings('.btn-migrate-box');

        if (this.checked) {
            $next.show();
            $btn.show();
        } else {
            $next.hide();
        }

        seeo.checkMigrateOptions();
    });

    // Start with the migrate rows hidden
    $('.migrate_detail').hide();

    seeo.init();
    seeo.initCountdowns();
});