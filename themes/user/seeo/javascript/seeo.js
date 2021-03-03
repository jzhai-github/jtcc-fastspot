$(document).ready(function() {

    $("#seeo__seeo_title").keyup(function () {
        $("#seeo__seeo_title_count").text((250 - $(this).val().length) + " chars left");
    });

    $("#seeo__seeo_description").keyup(function () {
        $("#seeo__seeo_description_count").text((150 - $(this).val().length) + " chars left");
    });

    $("#seeo__seeo_keywords").keyup(function () {
        $("#seeo__seeo_keywords_count").text((150 - $(this).val().length) + " chars left");
    });

    $("#seeo__seeo_og_description").keyup(function () {
        $("#seeo__seeo_og_description_count").text((300 - $(this).val().length) + " chars left");
    });

    $("#seeo__seeo_twitter_description").keyup(function () {
        $("#seeo__seeo_twitter_description_count").text((200 - $(this).val().length) + " chars left");
    });

    $('.reset-action').on('click', function(e) {
        e.preventDefault();

        if (confirm("Are you sure you want to reset this channel's entries?")) {
            var channel_id = $(this).data('channel');
            var type = $(this).data('type');

            $.post($('#reset_url').val(), { channel_id: channel_id, type: type }, function (response) {
                console.log(response);

            });
        }
    })
});