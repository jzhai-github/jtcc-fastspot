<div class="form-standard">
    <?php echo $form_open; ?>

     <div class="form-btns form-btns-top">
        <h1 class="panel-heading"><?=lang('seeo_template_page_settings')?></h1>
        <input class="btn" type="submit" value="<?=lang('save')?>">
    </div>

<?php
    include 'partials/template_page_details.php';
    include 'partials/standard_meta.php';
    include 'partials/open_graph.php';
    include 'partials/twitter_cards.php';
    include 'partials/sitemap.php';
?>

    <div class="form-btns">
        <input class="btn" type="submit" value="<?=lang('save')?>">
    </div>
    </form>
</div>
