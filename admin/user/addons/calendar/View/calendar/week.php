<div id="calendar" class="calendar form-standard">
    <?php include PATH_THIRD . '/calendar/View/_layouts/calendar_controls.php'; ?>

    <table class="calendar week" cellspacing="0" cellpadding="0">
        <thead>
        <tr>
            <th class="hours"></th>
            <?php foreach ($schedule as $dayObject): ?>
                <?php $dateString = $dayObject->getDateTime()->toDateString(); ?>
                <th>
                    <?php echo $dayObject->getDateTime()->format('D j') ?>
                </th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr class="all-day">
            <td><?php echo lang('calendar_all_day') ?></td>
            <?php foreach ($schedule as $dayObject): ?>
                <?php
                $day = $dayObject->getDateTime();
                $dateString = $dayObject->getDateTime()->toDateString();
                $class      = '';

                if ($dayObject->getDateTime()->toDateString() == $now->toDateString()) {
                    $class = 'today';
                }
                ?>
                <td class="<?php echo $class ?>">
                    <?php foreach ($events->getEvents($dateString) as $event): ?>
                        <?php if ($event->showAsAllDay($day)): ?>
                            <?php include PATH_THIRD . '/calendar/View/event/module.php'; ?>
                        <?php endif ?>
                    <?php endforeach; ?>
                </td>
            <?php endforeach; ?>
        </tr>
        <?php foreach ($hours as $hour => $formattedHour): ?>
            <tr>
                <td><?php echo $formattedHour ?></td>
                <?php foreach ($schedule as $dayObject): ?>
                    <?php
                    $day = $dayObject->getDateTime();
                    $dateString = $dayObject->getDateTime()->toDateString();
                    $class      = '';

                    if ($dayObject->getDateTime()->toDateString() == $now->toDateString()) {
                        $class = 'today';
                    }
                    ?>

                    <td class="<?php echo $class ?>">
                        <?php foreach ($events->getEvents($dateString) as $event): ?>
                            <?php if ($event->inHour($hour) && !$event->showAsAllDay($day)): ?>
                                <?php include PATH_THIRD . '/calendar/View/event/module.php'; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
