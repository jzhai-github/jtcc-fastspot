<?php if (empty($migratableEvents)) : ?>
    <h5 style="margin-top: 20px;"><?php echo lang('calendar_low_no_events') ?></h5>
<?php else: ?>

    <style type="text/css" rel="stylesheet">
        li.no-calendar {
            color: #CCCCCC;
        }

        li.no-calendar h5 {
            color: #bf6968;
        }

        ul.migratable-events > li > ul > li {
            padding-left: 10px;
        }
    </style>

    <ul class="listing migratable-events">
        <?php foreach ($migratableEvents as $data): ?>
            <?php
            $channel       = $data['channel'];
            $events        = $data['events'];
            $calendar      = $data['calendar'];
            $calendarField = $data['calendar_field'];

            $createFieldUrl = ee('CP/URL', 'channels/fields/create/' . $channel->field_group);
            $setCalendarUrl = ee('CP/URL', 'channels/fields/edit/' . ($calendarField ? $calendarField->field_id : ''));
            ?>
            <li class="<?php echo !$calendar ? 'no-calendar' : '' ?>">
                <h3><?php echo $data['channel']->channel_title ?></h3>
                <h5 style="<?php echo $calendar ? 'color: ' . $calendar->color : '' ?>">
                    <?php if ($calendarField) : ?>
                        <?php if ($calendar) : ?>
                            <?php echo $calendar->name ?>
                        <?php else: ?>
                            <?php echo str_replace('%link%', $setCalendarUrl, lang('calendar_low_no_default_calendar')) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php echo str_replace('%link%', $createFieldUrl, lang('calendar_low_no_calendar_field_present')) ?>
                    <?php endif; ?>
                </h5>

                <ul class="listing">
                    <?php foreach ($events as $event): ?>
                        <li>
                            <?php echo $event ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
    <input type="hidden" name="migrate" value="1" />

<?php endif; ?>
