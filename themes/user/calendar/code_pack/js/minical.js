$(function () {
    var $cal = $("#mini-cal-wrapper");

    $cal.on({
        click: function(e) {
            var $self = $(this);
            var url   = $self.attr("href");

            var updatedUrl = url.replace(/\/(\w+)main\/month(\/\d+\/\d+)/, "\/$1inc\/mini_cal$2", url);

            console.log(url, updatedUrl);
            $.ajax({
                url: updatedUrl,
                type: "GET",
                success: function(response) {
                    $cal.html(response);
                }
            });

            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, ".table thead a");
});
