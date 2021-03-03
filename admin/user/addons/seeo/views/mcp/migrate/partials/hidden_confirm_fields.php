<input type="hidden" name="<?=$csrf_token_name?>" value="<?=$csrf_token_value?>" />

<?php foreach ($migration->migration_data as $name => $val): ?>
    <input type="hidden" name="migrate[<?=$name?>]" value='<?=serialize($val)?>' />
<?php endforeach; ?>

<input type="hidden" name="context" value="<?=$migration->shortname?>"/>