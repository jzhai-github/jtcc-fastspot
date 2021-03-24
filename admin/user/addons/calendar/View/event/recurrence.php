<?php
/** @var \Solspace\Addons\Calendar\Model\Event $event */
?>
<div class="calendarSection borderless cal-row">
    <div class="cal-col-2">
        <?php echo $form->label(
            array(
                "text"      => lang("calendar_is_repeating"),
                "for"       => $fieldName . "[freq]",
                "className" => "calendarLabel",
            )
        ); ?>
    </div>
    <div class="cal-col-1-5">
        <?php echo $form->switch_checkbox(
            array(
                "title"     => lang("calendar_is_repeating"),
                "value"     => $event->isRecurring() ? 1 : '',
                "name"      => $fieldName . "[repeats]",
                "className" => "calendarIsRepeating",
                "data"      => array(
                    "on"  => lang("calendar_yes"),
                    "off" => lang("calendar_no"),
                ),
            )
        ); ?>
    </div>
    <div class="cal-col-7 clearfix recurrence-intervals">
        <?php echo $form->interval(
            array(
                "name"  => $fieldName . "[interval]",
                "value" => $event->getDaily() ? $event->getDaily()->interval :
                    ($event->getWeekly() ? $event->getWeekly()->interval :
                    ($event->getMonthly() ? $event->getMonthly()->interval :
                    ($event->getYearly() ? $event->getYearly()->interval : null))),
                "text"  => lang("calendar_days"),
            )
        ); ?>

        <?php echo $form->dropdown(
            array(
                "name"      => $fieldName . "[freq]",
                "options"   => array(
                    'daily'   => ucfirst(lang("calendar_days")),
                    'weekly'  => ucfirst(lang("calendar_weeks")),
                    'monthly' => ucfirst(lang("calendar_months")),
                    'yearly'  => ucfirst(lang("calendar_years")),
                    'dates'   => ucfirst(lang("calendar_dates")),
                ),
                "values"     => $event->getFreq() ?: 'daily',
                "className" => "calendarDropdown calendarRepeatDropdown",
            )
        ); ?>
    </div>
</div>
<div class="calendarRecurrenceContainer calendarSection">

    <ul class="calendarRecurrence">
        <li class="calendarWeekly">
            <div class="clearfix">
                <?php echo $form->checkbox_list(
                    array(
                        "name"   => $fieldName . "[weekly][byday][]",
                        "values" => $event->getWeekly() && isset($event->getWeekly()->byday) ? $event->getWeekly(
                        )->byday : null,
                        "id"     => $fieldName . "[weekly]" . "calendarByDay",
                        "items"  => $days,
                    )
                ); ?>
            </div>
        </li>

        <li class="calendarMonthly">
            <?php $monthPrefix = $fieldName . "[monthly]" ?>

            <div class="clearfix choice-list">
                <?php
                $monthlyRecurrence = $event->getMonthly();
                $byMonthDayOrByDay = "bymonthday";
                if ($monthlyRecurrence && isset($monthlyRecurrence->bymonthdayorbyday)) {
                    $byMonthDayOrByDay = $monthlyRecurrence->bymonthdayorbyday;
                }
                ?>
                <label class="">
                    <?php echo $form->input(
                        array(
                            "type"       => "radio",
                            "name"       => $monthPrefix . "[bymonthdayorbyday]",
                            "radioValue" => $byMonthDayOrByDay,
                            "value"      => "bymonthday",
                            "className"  => "calendarMonthlyByMonthDay calendarEachCheckbox",
                            "id"         => $fieldName . "calendarMonthlyByMonthDay",
                            "label"      => $form->label(
                                array(
                                    "for"       => $fieldName . "calendarMonthlyByMonthDay",
                                    "className" => "calendarText calendarEach",
                                    "text"      => lang("calendar_each"),
                                )
                            ),
                            "data"       => array(
                                "target" => ".calendarMonthlyOptionFirst",
                            ),
                        )
                    ); ?>
                </label>

                <label class="">
                    <?php
                    $byMonthDayOrByDay = "";
                    if ($monthlyRecurrence && isset($monthlyRecurrence->byday)) {
                        $byMonthDayOrByDay = "byday";
                    }
                    ?>
                    <?php echo $form->input(
                        array(
                            "type"       => "radio",
                            "name"       => $monthPrefix . "[bymonthdayorbyday]",
                            "radioValue" => $byMonthDayOrByDay,
                            "value"      => "byday",
                            "label"      => $form->label(
                                array(
                                    "text"      => lang("calendar_on_the"),
                                    "className" => "calendarText",
                                    "for"       => $fieldName . "calendarMonthlyByDay",
                                )
                            ),
                            "data"       => array(
                                "target" => ".calendarMonthlyByMonth",
                            ),
                            "className"  => "calendarMonthlyByDay",
                            "id"         => $fieldName . "calendarMonthlyByDay",
                        )
                    ); ?>


                    <?php echo $form->dropdown(
                        array(
                            "name"      => $monthPrefix . "[bydayinterval]",
                            "options"   => array(
                                "1"  => lang("calendar_first"),
                                "2"  => lang("calendar_second"),
                                "3"  => lang("calendar_third"),
                                "4"  => lang("calendar_fourth"),
                                "-1" => lang("calendar_last"),
                            ),
                            "values"    => isset($monthlyRecurrence->bydayinterval) ? $monthlyRecurrence->bydayinterval : null,
                            "className" => "calendarDropdown calendarByDayInterval",
                        )
                    ); ?>
                </label>
            </div>

            <div class="calendarMonthlyOption calendarMonthlyOptionFirst">
                <?php echo $form->month_day_buttons(
                    array(
                        "name"   => $monthPrefix . "[bymonthday][]",
                        "values" => $event->getMonthly() && isset($event->getMonthly(
                            )->bymonthday) ? $event->getMonthly()->bymonthday : null,
                        "id"     => $fieldName . "byMonthDay",
                    )
                ); ?>
            </div>
            <div class="calendarMonthlyOption calendarMonthlyByMonth" style="display: none;">
                <?php echo $form->checkbox_list(
                    array(
                        "items"     => $days,
                        "name"      => $monthPrefix . "[byday][]",
                        "values"    => isset($monthlyRecurrence->byday) ? $monthlyRecurrence->byday : null,
                        "className" => "calendarByDay",
                        "id"        => $monthPrefix . "calendarByDay",
                    )
                ); ?>
            </div>
        </li>

        <li class="calendarYearly">
            <?php
            $yearPrefix       = $fieldName . "[yearly]";
            $yearlyRecurrence = $event->getYearly();
            ?>
            <div class="calendarYearlyByDay">
                <div class="clearfix choice-list">
                    <?php $isYearlyByDayChecked = isset($yearlyRecurrence->byday) ? count(
                            $yearlyRecurrence->byday
                        ) > 0 : false; ?>
                    <label class="">
                        <?php echo $form->input(
                            array(
                                "type"      => "checkbox",
                                "name"      => $yearPrefix . "[isbyday]",
                                "checked"   => $isYearlyByDayChecked,
                                "value"     => 1,
                                "label"     => lang("calendar_on_the"),
                                "className" => "calendarYearlyIsByDay",
                                "data"      => array(
                                    'target' => '.calendarYearlyDaySelector, .calendarYearlyMonthSelector',
                                ),
                                "id"        => $fieldName . "calendarYearlyIsByDay",
                            )
                        ); ?>

                        <?php echo $form->dropdown(
                            array(
                                "name"      => $yearPrefix . "[bydayinterval]",
                                "options"   => array(
                                    "1"  => lang("calendar_first"),
                                    "2"  => lang("calendar_second"),
                                    "3"  => lang("calendar_third"),
                                    "4"  => lang("calendar_fourth"),
                                    "-1" => lang("calendar_last"),
                                ),
                                "values"    => isset($yearlyRecurrence->bydayinterval) ? $yearlyRecurrence->bydayinterval : null,
                                "className" => "calendarDropdown calendarByDayInterval",
                            )
                        ); ?>
                    </label>
                </div>

                <div class="clearfix calendarYearlyDaySelector" style="display: none;">

                    <?php echo $form->checkbox_list(
                        array(
                            "items"     => $days,
                            "name"      => $yearPrefix . "[byday][]",
                            "values"    => isset($yearlyRecurrence->byday) ? $yearlyRecurrence->byday : null,
                            "className" => "calendarByDay",
                            "id"        => $yearPrefix . "calendarByDay",
                        )
                    ); ?>
                </div>

                <div class="calendarYearlyMonthSelector" style="display: none;">
                    <?php echo $form->month_name_buttons(
                        array(
                            'name'      => $yearPrefix . '[bymonth][]',
                            'values'    => isset($yearlyRecurrence->bymonth) ? $yearlyRecurrence->bymonth : null,
                            'className' => 'calendarByMonth',
                            'id'        => $fieldName . 'calendarYearly',
                        )
                    ); ?>
                </div>
            </div>
        </li>

        <li class="calendarDates">
            <div class="calendarDatesSection calendarRecurrenceContainer calendarSection borderless">
                <?php echo $form->label(
                    array(
                        "text"      => lang("calendar_select_dates"),
                        "className" => "calendarLabel",
                    )
                ); ?>

                <div class="calendarContainer">
                    <?php echo $form->input(
                        array(
                            "className"   => "selectDatesCalendar calendarDates calendarDate",
                            "placeholder" => $preference->getHumanReadableDateFormat(),
                            "data"        => array(
                                "dateformat" => $preference->getHumanReadableDateFormat(),
                                "startday"   => $startDay,
                            ),
                        )
                    ); ?>

                    <ul class="dates-list">
                        <?php if ($dates): ?>
                            <?php foreach ($dates as $date) : ?>
                                <li>
                                    <?php echo $form->input(
                                        array(
                                            "type"  => 'hidden',
                                            "name"  => $fieldName . '[dates][]',
                                            "value" => $date->date,
                                        )
                                    ); ?>
                                    <a class="close-button"></a>
                                    <?php echo $date->getDescriptiveDate() ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </li>
    </ul>
</div>
<div class="calendarUntilSection calendarRecurrenceContainer calendarSection">
    <?php echo $form->label(
        array(
            "text"      => lang("calendar_until"),
            "className" => "calendarLabel",
        )
    ); ?>

    <div class="calendarContainer">
        <?php
        if ($event->isRecurring() && !$event->isSelectDates()) {
            $until = $event->getUntil()->format($dateFormat);
        } else {
            $until = null;
        }
        ?>
        <?php echo $form->input(
            array(
                "name"        => $fieldName . "[until]",
                "value"       => $until,
                "className"   => "calendarUntil calendarDate",
                "placeholder" => $preference->getHumanReadableDateFormat(),
                "data"        => array(
                    "dateformat" => $preference->getHumanReadableDateFormat(),
                    "startday"   => $startDay,
                ),
            )
        ); ?>
    </div>
</div>

<div class="calendarExceptSection calendarRecurrenceContainer calendarSection borderless">
    <?php echo $form->label(
        array(
            "text"      => lang("calendar_except"),
            "className" => "calendarLabel",
        )
    ); ?>

    <div class="calendarContainer">
        <?php echo $form->input(
            array(
                "className"   => "calendarExclude calendarDate",
                "placeholder" => $preference->getHumanReadableDateFormat(),
                "data"        => array(
                    "dateformat" => $preference->getHumanReadableDateFormat(),
                    "startday"   => $startDay,
                ),
            )
        ); ?>

        <ul class="exclusion-list">
            <?php if ($exclusions): ?>
                <?php foreach ($exclusions as $exclusion) : ?>
                    <li>
                        <?php echo $form->input(
                            array(
                                "type"  => 'hidden',
                                "name"  => $fieldName . '[exclude][]',
                                "value" => $exclusion->date,
                            )
                        ); ?>
                        <a class="close-button"></a>
                        <?php echo $exclusion->getDescriptiveDate() ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>
