<?php /** @var $field \GDO\Avatar\GDT_Avatar **/ ?>
<div class="gdo-avatar">
  <img src="<?= href('Avatar', 'Image', '&_ajax=1&file=' . $field->gdo->getVar('avatar_file_id')); ?>" />
</div>
<?=$field->gdo->display('file_name')?>
