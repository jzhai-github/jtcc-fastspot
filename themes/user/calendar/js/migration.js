$(function() {
  $(".form-btns > .btn[type=submit]").on({
    click: function(e) {
      e.preventDefault();
      e.stopPropagation();

      $("input[name=migrate]")
        .val(1)
        .parents("form")
        .submit();
    }
  });

  $("#cleanup-button").on({
    click: function() {
      var link = $(this).attr("href");

      $("input[name=cleanup]")
        .val(1)
        .parents("form")
        .attr("action", link)
        .submit();
    }
  });
});