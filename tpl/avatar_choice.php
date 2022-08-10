<?php
namespace GDO\Avatar\tpl\choice;
/** @var $field \GDO\Avatar\GDT_Avatar **/ ?>
<span class="gdo-avatar">
  <img src="<?= href('Avatar', 'Image', '&_ajax=1&file=' . $field->gdo->gdoVar('avatar_file_id')); ?>" />
  <?=$field->gdo->gdoDisplay('file_name')?>
</span>
