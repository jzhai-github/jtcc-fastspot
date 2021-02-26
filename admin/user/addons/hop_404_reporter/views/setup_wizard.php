<div class="box">
    <?php echo ee('CP/Alert')->getAllInlines(); ?>

    <div style="padding: 10px;">
        <?php
            if ($edit_404) {
                echo $edit_404;
            } elseif ($create_404) {
                echo $create_404;
            } elseif ($all_good) {
               echo $all_good;
            }

            if ($create_notification) {
                echo $create_notification;
            }
            echo $body;
        ?>
    </div>
</div>