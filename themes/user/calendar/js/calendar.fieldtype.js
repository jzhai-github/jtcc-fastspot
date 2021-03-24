$(function () {
  var $fieldType = $('.calendarFieldType');

  $fieldType.each(function () {
    var $context = $(this);

    $('.calendarTime', $context).timepicker({
      timeFormat: calendarData.time_format,
      maxTime: '11:30pm',
      step: calendarData.time_interval
    });
    $('.calendarDate', $context).datepicker({
      dateFormat: calendarData.date_format,
      firstDay: calendarData.first_day,
      onSelect: function () {
        $('.calendarCalendarSelect', $context).trigger("change");
      }
    });

    var $startDate = $('.calendarStartDay', $context);
    var $startTime = $('.calendarStartTime', $context);
    var $endDate   = $('.calendarEndDay', $context);
    var $endTime   = $('.calendarEndTime', $context);
    var $untilDate = $('.calendarUntil', $context);

    $startDate.datepicker('option', 'onSelect', function (dateText) {
      $endDate.datepicker('option', 'minDate', dateText);
      $endDate.val(dateText);
      $startTime.trigger('change');
      $untilDate.datepicker('option', 'defaultDate', dateText);
      $('.calendarCalendarSelect', $context).trigger("change");
    });

    $endDate.datepicker('option', 'onSelect', function () {
      $startTime.trigger('change');
      $('.calendarCalendarSelect', $context).trigger("change");
    });

    $startTime.on('change', function () {
      var startDate = $startDate.datepicker('getDate');
      var endDate   = $endDate.datepicker('getDate');

      var diffInDays = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24));

      var currentTime  = $(this).timepicker('getTime');
      var adjustedTime = $(this).timepicker('getTime');
      adjustedTime.setMinutes(currentTime.getMinutes() + parseInt(calendarData.duration));

      $endTime.timepicker('option', 'durationTime', currentTime);

      if (diffInDays == 0) {
        var minTime = $(this).timepicker('getTime');
        minTime.setMinutes(currentTime.getMinutes() + parseInt(calendarData.time_interval));

        $endTime.timepicker('option', 'showDuration', true);
        $endTime.timepicker('option', 'minTime', minTime);
        if ($(this).val()) {
          $endTime.timepicker('setTime', adjustedTime);
        }
      } else {
        $endTime.timepicker('option', 'showDuration', false);
        $endTime.timepicker('option', 'minTime', '00:00');
        if ($(this).val()) {
          $endTime.timepicker('setTime', currentTime);
        }
      }

      $('.calendarCalendarSelect', $context).trigger("change");
    });

    $endTime.on('change', function () {
      $('.calendarCalendarSelect', $context).trigger("change");
    });

    $('.toggle-btn', $context).on({
      click: function () {
        var $self = $(this);
        if (!$(this).hasClass('toggle-btn')) {
          $self = $self.parents('.toggle-btn:first');
        }

        var isChecked = $self.hasClass('on');

        if ($self.parents('.calendarFieldTypeFront').length) {
          var $input = $('input:hidden', $self);

          if (isChecked) {
            $self.removeClass('on')
                 .addClass('off');
            $input.val('');
          } else {
            $self.removeClass('off')
                 .addClass('on');
            $input.val('1');
          }
        }

        $self.trigger({type: 'toggled', isOn: !isChecked});
      }
    });


    // Time field toggling
    // ===================
    var $allDayToggle       = $('.toggle-btn.calendarAllDay', $context);
    var $timeInput          = $('.calendarTime', $context);
    var $targetDateWrappers = $('.target-date-wrapper', $context);

    $allDayToggle.on({
      toggled: function (event) {
        if (event.isOn) {
          $targetDateWrappers.addClass("no-time");
        } else {
          $targetDateWrappers.removeClass("no-time");
        }
      }
    });

    // Recurrence block toggle
    // =======================
    var $recurrenceToggle    = $('.toggle-btn.calendarIsRepeating', $context);
    var $recurrenceContainer = $('.calendarRecurrenceContainer', $context);
    var $recurrenceIntervals = $('.recurrence-intervals', $context);

    $('.calendar-checkbox-list input:checkbox', $recurrenceContainer).on({
      click: function () {
        var $parent = $(this).parent();
        if ($(this).is(':checked')) {
          $parent.addClass('chosen');
        } else {
          $parent.removeClass('chosen');
        }
      }
    });

    $recurrenceToggle.on({
      toggled: function (event) {
        if (event.isOn) {
          $recurrenceContainer.slideDown("fast");
          $recurrenceIntervals.fadeIn("fast");

            var frequencySelectValue = $('.calendarRepeatDropdown').first().val();

            if (frequencySelectValue && frequencySelectValue === 'dates') {
                $('.calendarUntilSection').hide();
                $('.calendarExceptSection').hide();
                $('.calendarIntervalWrapper').hide();
            }

        } else {
          $recurrenceContainer.slideUp("fast");
          $recurrenceIntervals.fadeOut("fast");
        }
      }
    });

    var $frequencySelect = $('.calendarRepeatDropdown', $context);
    var $recurrenceList  = $('ul.calendarRecurrence', $context);

    $frequencySelect.on({
      change: function () {

        var selectedValue = $(this).val();

        if (selectedValue === 'dates') {
            $('.calendarUntilSection').hide();
            $('.calendarExceptSection').hide();
            $('.calendarIntervalWrapper').hide();
        } else {

            var $recurrenceToggleIsOn = $('.toggle-btn.calendarIsRepeating').first().hasClass('on');

            if ($recurrenceToggleIsOn) {
                $('.calendarUntilSection').show();
                $('.calendarExceptSection').show();
                $('.calendarIntervalWrapper').show();
            }
        }

        $('> li', $recurrenceList).slideUp("fast", function () {
          $(this).removeClass('calendarVisible');
        });

        var targetClassName = selectedValue.charAt(0).toUpperCase() + selectedValue.slice(1);
        $('> li.calendar' + targetClassName, $recurrenceList).slideDown("fast", function () {
          $(this).addClass('calendarVisible');
        });
      }
    });


    // Toggle sub-views for recurrency items
    // ====================================
    $(".calendarMonthlyByMonthDay, .calendarMonthlyByDay", $context).on({
      click: function () {
        var $target = $($(this).attr("data-target"), $context);
        $('.calendarMonthlyOption:visible').not($target).hide("fast");

        $target.show("fast");
      }
    });
    $(".calendarYearlyIsByDay", $context).on({
      click: function () {
        toggleTargetVisibility($(this), $context);
      }
    });


    $('.calendarExclude', $context).datepicker("option", "onSelect", function (dateString, obj) {
      var date = $(this).datepicker("getDate");
      $(this).val('');
      addToExcludeList(date, dateString, $context);
    });

    $('.calendarDates', $context).datepicker("option", "onSelect", function (dateString, obj) {
      var date = $(this).datepicker("getDate");
      $(this).val('');
        addToDatesList(date, dateString, $context);
    });

    $($context).on({
      click: function (event) {
        $(this).parents("li:first").remove();

        event.stopPropagation();
        return false;
      }
    }, 'ul.exclusion-list a.close-button:not(.disabled)');

    $($context).on({
      click: function (event) {
        $(this).parents("li:first").remove();

        event.stopPropagation();
        return false;
      }
    }, 'ul.dates-list a.close-button:not(.disabled)');

    $allDayToggle.trigger({type: 'toggled', isOn: $allDayToggle.hasClass('on')});
    $recurrenceToggle.trigger({type: 'toggled', isOn: $recurrenceToggle.hasClass('on')});
    $frequencySelect.trigger('change');
    $(".calendarMonthlyByMonthDay, .calendarMonthlyByDay", $context).filter(':checked').trigger('click');
    toggleTargetVisibility($(".calendarYearlyIsByDay", $context).filter(':checked'), $context);

    // There's an issue in Safari, which causes the select inside a label trigger the checkbox input
    // To prevent this - we're unchecking the checkbox after select's change event, so that it gets
    // re-checked after.
    if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
      $("label.choice > .calendarByDayInterval", $context).on({
        change: function () {
          var $siblingCheckbox = $(this).siblings('input:checkbox');

          if ($siblingCheckbox.is(':checked')) {
            $siblingCheckbox.prop('checked', false);
          }
        }
      });
    }


    // If the user isn' allowed to edit this field
    // Disable all input fields
    if ($context.hasClass('disabled')) {
      $('ul.exclusion-list a.close-button').addClass('disabled');
      $('input, select, textarea', $context).prop('disabled', true);
    }

    if ($context.hasClass('disabled')) {
      $('ul.dates-list a.close-button').addClass('disabled');
      $('input, select, textarea', $context).prop('disabled', true);
    }
  });

  /**
   * Toggles target display property based on if the $element checkbox is checked or not
   *
   * @param $element
   * @param $context
   */
  function toggleTargetVisibility($element, $context) {
    var $target = $($element.attr("data-target"), $context);

    if ($element.is(":checked")) {
      $target.show("fast");
    } else {
      $target.hide("fast");
    }
  }


  /**
   * Adds dates to exclusion list
   *
   * @param date
   * @param dateString
   * @param $context
   */
  function addToExcludeList(date, dateString, $context) {
    var $excludeList  = $("ul.exclusion-list", $context);
    var formattedDate = $.datepicker.formatDate("DD, MM d, yy", date);

    // Do not add if an item exists already
    if ($('input[value="' + dateString + '"]', $excludeList).length) {
      return;
    }

    var input = $('<input />', {
      name: $context.attr('id') + '[exclude][]',
      type: "hidden",
      value: dateString
    });

    var closeButton = $('<a>').addClass("close-button");

    var item = $('<li>', {data: {date: dateString}})
      .append(input)
      .append(formattedDate)
      .append(closeButton);

    $excludeList.append(item);
  }

  /**
   * Adds dates to exclusion list
   *
   * @param date
   * @param dateString
   * @param $context
   */
  function addToDatesList(date, dateString, $context) {
    var $excludeList  = $("ul.dates-list", $context);
    var formattedDate = $.datepicker.formatDate("DD, MM d, yy", date);

    // Do not add if an item exists already
    if ($('input[value="' + dateString + '"]', $excludeList).length) {
      return;
    }

    var input = $('<input />', {
      name: $context.attr('id') + '[dates][]',
      type: "hidden",
      value: dateString
    });

    var closeButton = $('<a>').addClass("close-button");

    var item = $('<li>', {data: {date: dateString}})
      .append(input)
      .append(formattedDate)
      .append(closeButton);

    $excludeList.append(item);
  }
});
