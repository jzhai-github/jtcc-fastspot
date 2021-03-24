<div class="cal-row">
    <div class="cal-col-2">
        <?php echo $form->label(
            array(
                "text"      => lang("calendar_calendar"),
                "className" => "calendarLabel",
            )
        ); ?>
    </div>
    <div class="cal-col-10">
        <?php echo $form->dropdown(
            array(
                'name'      => $fieldName . "[calendar_id]",
                'className' => 'calendarCalendarSelect',
                'options'   => $calendarSelection,
                'values'    => $calendar ? $calendar->id : null,
            )
        ); ?>
    </div>
</div>

<div class="cal-row">
    <div class="cal-col-2">
        <?php echo $form->label(
            array(
                "text"      => lang("calendar_starts"),
                "className" => "calendarLabel",
            )
        ); ?>
    </div>

    <div class="cal-col-flexible target-date-wrapper">
        <?php
        if ($event->start_date) {
            $startDay = $event->getStartDate()->format($dateFormat);
        } else {
            $startDay = '';
        }
        $startTime = $event->start_date ? $event->getStartDate()->format($timeFormat) : '';
        ?>

        <?php echo $form->input(
            array(
                "name"        => $fieldName . "[start_day]",
                "value"       => $startDay,
                "className"   => "calendarDate calendarStartDay datepicker",
                "placeholder" => $preference->getHumanReadableDateFormat(),
            )
        ); ?>
        <?php echo $form->input(
            array(
                "name"        => $fieldName . "[start_time]",
                "value"       => $startTime,
                "className"   => "calendarTime calendarStartTime",
                "placeholder" => $preference->getHumanReadableTimeFormat(),
            )
        ); ?>

    </div>
    <div class="cal-col-5 clearfix">
        <?php echo $form->switch_checkbox(
            array(
                "value"     => (bool)@$event->all_day,
                "name"      => $fieldName . "[all_day]",
                "className" => "calendarAllDay",
                "data"      => array(
                    "on"  => lang("calendar_yes"),
                    "off" => lang("calendar_no"),
                ),
            )
        ); ?>

        <?php echo $form->label(
            array(
                "text"      => lang("calendar_all_day"),
                "className" => "calendarLabel",
            )
        ); ?>
    </div>
</div>

<div class="cal-row">
    <div class="cal-col-2">
        <?php
        if ($event->end_date) {
            $endDay = $event->getEndDate()->format($dateFormat);
        } else {
            $endDay = '';
        }
        $endTime = $event->end_date ? $event->getEndDate()->format($timeFormat) : '';
        ?>
        <?php echo $form->label(
            array(
                "text"      => lang("calendar_ends"),
                "className" => "calendarLabel",
            )
        ); ?>
    </div>
    <div class="cal-col-flexible target-date-wrapper">
        <?php echo $form->input(
            array(
                "name"        => $fieldName . "[end_day]",
                "value"       => $endDay,
                "className"   => "calendarDate calendarEndDay",
                "placeholder" => $preference->getHumanReadableDateFormat(),
            )
        ); ?>
        <?php echo $form->input(
            array(
                "name"        => $fieldName . "[end_time]",
                "value"       => $endTime,
                "className"   => "calendarTime calendarEndTime",
                "placeholder" => $preference->getHumanReadableTimeFormat(),
            )
        ); ?>
    </div>
</div>
