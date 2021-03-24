<div id="calendar" class="calendar">
    <div class="nav">
        <h1><?php echo $title ?></h1>

        <ul class="controls">
            <li>
                <a href="<?php echo $urls['prevLink'] ?>" class="previous"><?php echo $urls['prevLinkTitle'] ?></a>
            </li>
            <li>
                <a href="<?php echo $urls['today'] ?>" class="calendarButton todayButton"><?php echo lang('calendar_today') ?></a>
            </li>
            <li>
                <a href="<?php echo $urls['nextLink'] ?>" class="next"><?php echo $urls['nextLinkTitle'] ?></a>
            </li>
        </ul>
    </div>

    <?php echo $sub_view ?>
</div>
