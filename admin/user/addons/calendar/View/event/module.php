<?php
$calendar = $calendars[$event->calendar_id];

// Do not show this event if the entry no longer exists
if (!isset($channel_entries[$event->entry_id])) {
    return;
}

$channelEntry = $channel_entries[$event->entry_id];
?>
<div class="event <?php echo $calendars[$event->calendar_id]->url_title ?>Calendar"
     data-date="<?php echo $day->toDateString() ?>">

    <span class="calendar-color" style="background-color: <?php echo $calendars[$event->calendar_id]->color ?>;"></span>
    <span class="title">
        <?php echo $channelEntry->title ?>
    </span>
    <span class="truncated-title">
        <?php echo \Solspace\Addons\Calendar\Library\Helpers::truncateString($channelEntry->title, 11) ?>
    </span>
    <span class="time">
        <?php if ($event->all_day != true): ?>
            (<?php echo $event->getStartDate()->format($timeFormat) ?>
            &ndash;
            <?php echo $event->getEndDate()->format($timeFormat) ?>)
        <?php endif; ?>
    </span>


    <div class="qtip-data">
        <a href="<?php echo ee('CP/URL', 'publish/edit/entry/' . $event->entry_id) ?>" class="edit"></a>
        <h3><?php echo $channelEntry->title ?></h3>
        <div class="calendar">
            <span class="calendar-color" style="background-color: <?php echo $calendar->color ?>;"></span>
            <span style="color: <?php echo $calendar->color ?>;"><?php echo $calendars[$event->calendar_id]->name ?></span>
        </div>
        <hr>
        <div class="starts">
            <b><?php echo lang('calendar_starts') ?></b> <?php echo $event->getFormattedStartDate($day) ?>
        </div>
        <div class="ends">
            <b><?php echo lang('calendar_ends') ?></b> <?php echo $event->getFormattedEndDate($day) ?>
        </div>
        <?php if ($event->isRecurring()) : ?>
            <hr>
            <div class="repeats">
                <b><?php echo lang('calendar_is_repeating') ?></b>
            </div>
        <?php endif; ?>
    </div>
</div>
