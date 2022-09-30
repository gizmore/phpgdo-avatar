<?php
namespace GDO\Avatar;

use GDO\Core\GDO_Module;
use GDO\User\GDO_User;
use GDO\Core\GDT_Checkbox;
use GDO\File\GDT_ImageFile;
use GDO\File\GDO_File;
use GDO\UI\GDT_Page;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Bar;

/**
 * Avatar module.
 * Features default avatars.
 * Avatars have a colored border depending on the two real genders: mandalorian and apache helicopter.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 * @see GDT_ImageFile
 */
final class Module_Avatar extends GDO_Module
{
	##############
	### Module ###
	##############
	public function getClasses() : array { return ['GDO\Avatar\GDO_Avatar','GDO\Avatar\GDO_UserAvatar']; }
	public function getDependencies() : array { return ['File']; }
	public function getFriendencies() : array { return ['Account']; }
	public function onLoadLanguage() : void { $this->loadLanguage('lang/avatar'); }
	public function onIncludeScripts() : void { $this->addCSS('css/gdo-avatar.css'); }

	##############
	### Config ###
	##############
	public function getConfig() : array
	{
		return [
		    GDT_Checkbox::make('avatar_guests')->initial('1'),
		    GDT_Checkbox::make('hook_sidebar')->initial('1'),
		    GDT_ImageFile::make('avatar_image_guest')->previewHREF(href('Avatar', 'Image', '&file={id}'))->scaledVersion('icon', 96, 96)->scaledVersion('thumb', 375, 375),
		    GDT_ImageFile::make('avatar_image_member')->previewHREF(href('Avatar', 'Image', '&file={id}'))->scaledVersion('icon', 96, 96)->scaledVersion('thumb', 375, 375),
		    GDT_ImageFile::make('avatar_image_male')->previewHREF(href('Avatar', 'Image', '&file={id}'))->scaledVersion('icon', 96, 96)->scaledVersion('thumb', 375, 375),
		    GDT_ImageFile::make('avatar_image_female')->previewHREF(href('Avatar', 'Image', '&file={id}'))->scaledVersion('icon', 96, 96)->scaledVersion('thumb', 375, 375),
		];
	}
	public function cfgGuestAvatars() { return $this->getConfigValue('avatar_guests'); }
	public function cfgSidebar() { return $this->getConfigValue('hook_sidebar'); }
	public function cfgColAvatarGuest() : GDT_ImageFile { return $this->getConfigColumn('avatar_image_guest'); }
	
	############
	### Init ###
	############
	public function onInitSidebar() : void
	{
	    if ($this->cfgSidebar())
	    {
	        GDT_Page::$INSTANCE->leftBar()->addField(
	            GDT_Link::make()->text('mt_avatar_gallery')->href(
	                href('Avatar', 'Gallery')));
	    }
	}
	
	###############
	### Install ###
	###############
	public function onInstall() : void
	{
		if (!($image = $this->getConfigValue('avatar_image_guest')))
		{
			$path = $this->filePath('tpl/img/default.jpeg');
			$image = GDO_File::fromPath('default.jpeg', $path)->insert()->copy();
			$column = $this->cfgColAvatarGuest();
			$column->createScaledVersions($image);
			$this->saveConfigVar('avatar_image_guest', $image->getID());
		}
	}
	
	############
	### Hook ###
	############
	public function hookAccountChanged(GDO_User $user) : void
	{
		$user->tempUnset('gdo_avatar');
	}

	public function hookAccountBar(GDT_Bar $bar) : void
	{
		$bar->addField(GDT_Link::make('btn_avatar')->href(href('Avatar', 'Set')));
	}

// 	public function hookCreateCardUserProfile(GDT_Card $card)
// 	{
// 		$user = $card->gdo->getUser();
// 		$avatar = GDT_Avatar::make()->user($user);
// 		$card->addFieldFirst($avatar);
// 	}

}
