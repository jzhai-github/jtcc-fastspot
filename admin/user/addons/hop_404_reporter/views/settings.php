<?php if (version_compare(APP_VER, '4', '<')) { ?>
<div class="box">
<?php } ?>
	<?php $this->embed('ee:_shared/form')?>
<?php if (version_compare(APP_VER, '4', '<')) { ?>
</div>
<?php } ?>