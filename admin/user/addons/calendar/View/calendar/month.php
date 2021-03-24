<div id="calendar" class="calendar form-standard">
    <?php include PATH_THIRD . '/calendar/View/_layouts/calendar_controls.php'; ?>

    <table class="calendar" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <?php foreach ($days as $name): ?>
                <th><?php echo $name ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($schedule as $weeks): ?>
            <?php foreach ($weeks as $week): ?>
                <tr>
                <?php foreach ($week as $dayObject): ?>
                    <?php
                    $class = '';

                    $date = $dayObject->getDateTime();

                    $monthInt         = $date->month;
                    $isDayBeforeMonth = $monthInt < $schedule->key();
                    $isDayAfterMonth  = $monthInt > $schedule->key();

                    $yearInt  = $date->year;
                    $monthInt = $date->month;
                    $dayInt   = $date->day;

                    $isDayOutsideMonth = $isDayBeforeMonth || $isDayAfterMonth;
                    if ($date->toDateString() === $now->toDateString()) {
                        $class = 'today';
                    } else if ($isDayOutsideMonth) {
                        $class = 'inactive';
                    }

                    $dayUrl = ee('CP/URL', "addons/settings/calendar/day/{$yearInt}/{$monthInt}/{$dayInt}");
                    ?>

                    <td class="<?php echo $class ?>">
                        <a href="<?php echo $dayUrl ?>"><?php echo $date->format('j'); ?></a>
                        <?php
                        if (is_object($events)) {
                            $toDateString = $date->toDateString();

                            $eventList  = $events->getEvents($toDateString);
                            $eventCount = count($eventList);

                            $count = 0;
                            foreach ($eventList as $event) {
                                $day = $dayObject->getDateTime();
                                include PATH_THIRD . '/calendar/View/event/module.php';

                                if (++$count >= 4) {
                                    break;
                                }
                            }

                            if ($eventCount > $count) {
                                $eventRemainder = $eventCount - $count;
                                $translation    = lang('calendar_more_event_count');
                                $translation    = str_replace('%count%', $eventRemainder, $translation);

                                echo '<a href="' . $dayUrl . '" class="more-events">' . $translation . '</a>';
                            }
                        }
                        ?>
                    </td>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
