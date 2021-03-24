<script>
    var calendarData = <?= json_encode($js_calendar_data) ?>;
</script>
<div class="calendarFieldType<?php echo $editingDisabled ? ' disabled' : '' ?><?php echo isset($isFrontend) && $isFrontend ? ' calendarFieldTypeFront' : '' ?>" id="<?php echo $fieldName ?>">
    <?php if ($editingDisabled) : ?>
        <h5 class="error-message" "><?php echo lang('calendar_no_permissions') ?></h5>
    <?php endif; ?>

    <?php
    include PATH_THIRD . '/calendar/View/event/form.php';
    include PATH_THIRD . '/calendar/View/event/recurrence.php';
    ?>

    <div class="calendarErrors">
        <?php echo $errors ?>
    </div>
</div>
