<?php
namespace GDO\Avatar\tpl;
use GDO\Avatar\GDO_Avatar;
use GDO\Avatar\GDT_Avatar;
/** @var $field GDT_Avatar **/
$az = round($field->imageWidth, 1);
$px = "{$az}px";
$gender = $field->user->getGender();
$field->css('width', $px);
$field->css('height', $px);
$field->addClass("gdo-avatar $gender");
?>
<span<?=$field->htmlAttributes()?>
><img alt="<?= t('avatar_of', [$field->user->renderUserName()]); ?>"
  src="<?=href('Avatar', 'Image', '&_ajax=1&file=' . GDO_Avatar::forUser($field->user)->getFileID()); ?>"
style="padding:<?=round($az/24,1)?>px;width:<?=$px?>;height:<?=$px?>;" /></span>
