<?php
$dateTime   = $day->toDateTimeString();
$dateString = $day->toDateString();
$timestamp  = $day->timestamp;
?>


<div id="calendar" class="calendar form-standard">
    <?php include PATH_THIRD . '/calendar/View/_layouts/calendar_controls.php'; ?>

    <table class="calendar day" cellspacing="0" cellpadding="0">
        <thead>
        <th></th>
        <th><?php echo $day->format('l') ?></th>
        </thead>
        <tbody>
        <tr class="all-day">
            <td class="hour"><?php echo lang('calendar_all_day') ?></td>
            <td>
                <?php foreach ($events->getEvents($dateString) as $event): ?>
                    <?php if ($event->showAsAllDay($day)): ?>
                        <?php include PATH_THIRD . '/calendar/View/event/module.php'; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </td>
        </tr>
        <?php foreach ($hours as $hour => $formattedHour): ?>
            <tr>
                <td class="hour"><?php echo $formattedHour ?></td>

                <?php $class = $day->eq($now) ? 'today' : ''; ?>
                <td class="<?php echo $class ?>">
                    <?php foreach ($events->getEvents($dateString) as $event): ?>
                        <?php if ($event->inHour($hour) && !$event->showAsAllDay($day)): ?>
                            <?php include PATH_THIRD . '/calendar/View/event/module.php'; ?>
                        <?php endif ?>
                    <?php endforeach; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
