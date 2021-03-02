var seeo = {

    init: function () {
        this.copyToField('.js-seeo-copy-to-og-title');
        this.copyToField('.js-seeo-copy-to-twitter-title');
        this.copyToField('.js-seeo-copy-to-og-description');
        this.copyToField('.js-seeo-copy-to-og-url');
        this.copyToField('.js-seeo-copy-to-twitter-description');
    },

    initCountdowns: function () {
        this.fieldCountdown('.js-seeo-description', 150);
        this.fieldCountdown('.js-seeo-keywords', 150);
        this.fieldCountdown('.js-seeo-og-description', 300);
        this.fieldCountdown('.js-seeo-twitter-description', 200);
    },

    copyToField: function(selector) {

        $(selector).change(function(){
            var target = "#" + $(this).data('target');
            var source = "#" + $(this).data('source');
            if($(this).is(":checked")){

                if(body.find(source).val() != body.find(target).val())
                {
                    $(target).val($(source).val());
                }
            } else {
                $(target).val('');
            }
        });

        source = "#" + $(selector).data('source');
        target = "#" + $(selector).data('target');

        $(source).keyup(function(){

            if(body.find(selector).is(":checked"))
            {
                source = "#" + $(selector).data('source');
                target = "#" + $(selector).data('target');
                $(target).val($(source).val());
            }
        })


    },

    fieldCountdown: function (field, length) {
        var counter = "#" + $(field).data('counter');
        body.find(field).on('input', function () {
            console.log(counter);
            $(counter).text((length - $(this).val().length) + " chars left");
        })
    },
}

$(document).ready(function () {
    body = $('body');
    seeo.init();
    seeo.initCountdowns();
});