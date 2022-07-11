<?php
use GDO\Avatar\GDO_Avatar;
use GDO\Avatar\GDT_Avatar;
/** @var $field GDT_Avatar **/
$az = $field->imageWidth;
?>
<span
 class="gdo-avatar <?=$field->user->getGender()?>"
 style="width: <?=$az?>px; height: <?=$az?>px;"
 <?=$field->htmlAttributes()?>
  ><img alt="<?= t('avatar_of', [$field->user->renderUserName()]); ?>"
   src="<?= href('Avatar', 'Image', '&_ajax=1&file=' . GDO_Avatar::forUser($field->user)->getFileID()); ?>"
   style="padding: <?=round($az/24,1)?>px; width: <?=$az?>px; height: <?=$az?>px;" /></span>
