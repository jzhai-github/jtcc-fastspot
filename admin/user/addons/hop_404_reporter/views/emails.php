<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="box">
	<div class="tbl-ctrls">
		<fieldset class="tbl-search right">
			<a class="btn tn action" href="<?= ee('CP/URL')->make('addons/settings/hop_404_reporter/add_email')?>"><?=lang('add_new_notification') ?></a>
		</fieldset>
		<h1><?=lang('email_notifications')?></h1>
		
		<?= ee('CP/Alert')->getAllInlines() ?>
		
		<?=lang('email_notifications_description')?>

		<?=form_open($search_url, array('name' => 'search', 'id' => 'search'))?>
			<fieldset class="tbl-search right input-group">
				<input type="text" class="form-control" name="search" value="<?=$search_keywords?>" style="display: inline-block; margin-right: 5px;" />
				<input class="btn submit" value="<?=lang('search')?>" type="submit">
			</fieldset>
		<?=form_close()?>

		<?= $filters ?>

		<?=form_open($action_url, array('name' => 'target', 'id' => 'target'))?>
		<?php
			$this->embed('ee:_shared/table', $table);
			print_r($pagination);
		?>

		<fieldset class="tbl-bulk-act hidden">
			<select name="bulk_action">
				<option><?=lang('--with_selected--')?></option>
				<option value="reset"><?=lang('email_reset_selected')?></option>
				<option value="delete" data-confirm-trigger="selected" rel="modal-confirm-remove"><?=lang('delete_selected')?></option>
			</select>
			<input class="btn submit" data-conditional-modal="confirm-trigger" type="submit" value="<?=lang('submit')?>">
		</fieldset>
		<?=form_close()?>
	</div>
</div>
<?php
	ee()->cp->add_js_script(array(
		'file' => array('cp/confirm_remove'),
	));
	ee('CP/Modal')->addModal('remove',
		$this->make('ee:_shared/modal_confirm_remove')->render(array(
			'name'     => 'modal-confirm-remove',
			'form_url' => $action_url,
			'hidden'  => array(
				'bulk_action' => 'delete'
			)
		))
	);
?>