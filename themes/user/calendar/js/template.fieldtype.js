$(function(){
    $("label.choice > input:checkbox").on({
        click: function() {
            var $self = $(this);
            var $parent = $self.parents("label:first");

            if ($self.is(":checked")) {
                $parent.addClass('chosen');
            } else {
                $parent.removeClass('chosen');
            }
        }
    });

    $("label.choice > input:radio").on({
        click: function() {
            var $self = $(this);
            var $parent = $self.parents("label:first");

            if ($self.is(":checked")) {
                $parent
                    .addClass('chosen')
                    .siblings()
                    .removeClass('chosen');
            }
        }
    });
});
