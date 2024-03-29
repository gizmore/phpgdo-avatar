<?php
namespace GDO\Avatar;

use GDO\Core\Debug;
use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\File\GDO_File;
use GDO\File\GDT_ImageFile;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;

/**
 * Avatar module.
 * Features default avatars.
 * Avatars have a colored border depending on the two real genders: mandalorian and apache helicopter.
 * This module requires the php gd library.
 *
 * @version 7.0.2
 * @since 6.2.0
 * @author gizmore
 * @see GDT_ImageFile
 */
final class Module_Avatar extends GDO_Module
{

	# #############
	# ## Module ###
	# #############
	public function getClasses(): array
	{
		return [
			GDO_Avatar::class,
			GDO_UserAvatar::class,
		];
	}

	public function getDependencies(): array
	{
		return [
			'File',
		];
	}

	public function getFriendencies(): array
	{
		return [
			'Account',
		];
	}

	public function onLoadLanguage(): void
	{
		$this->loadLanguage('lang/avatar');
	}

	public function onIncludeScripts(): void
	{
		$this->addCSS('css/gdo-avatar.css');
	}

	# #############
	# ## Config ###
	# #############
	public function getConfig(): array
	{
		return [
			GDT_Checkbox::make('avatar_guests')->initial('1'),
			GDT_Checkbox::make('hook_sidebar')->initial('1'),
			GDT_ImageFile::make('avatar_image_guest')->previewHREF(href('Avatar', 'Image', '&file={id}'))
				->scaledVersion('icon', 96, 96)
				->scaledVersion('thumb', 375, 375),
			GDT_ImageFile::make('avatar_image_member')->previewHREF(href('Avatar', 'Image', '&file={id}'))
				->scaledVersion('icon', 96, 96)
				->scaledVersion('thumb', 375, 375),
			GDT_ImageFile::make('avatar_image_male')->previewHREF(href('Avatar', 'Image', '&file={id}'))
				->scaledVersion('icon', 96, 96)
				->scaledVersion('thumb', 375, 375),
			GDT_ImageFile::make('avatar_image_female')->previewHREF(href('Avatar', 'Image', '&file={id}'))
				->scaledVersion('icon', 96, 96)
				->scaledVersion('thumb', 375, 375),
		];
	}

	public function onInitSidebar(): void
	{
		if ($this->cfgSidebar())
		{
			GDT_Page::$INSTANCE->leftBar()->addField(
				GDT_Link::make()->text('mt_avatar_gallery')
					->icon('image')
					->href(href('Avatar', 'Gallery')));
		}
	}

	public function cfgSidebar()
	{
		return $this->getConfigValue('hook_sidebar');
	}

	public function checkSystemDependencies(): bool
	{
		if (!function_exists('imagecreatefromjpeg'))
		{
			return $this->errorSystemDependency('err_php_extension', [
				'gd2',
			]);
		}
		return true;
	}

	# ###########
	# ## Init ###
	# ###########

	public function onInstall(): void
	{
		if (!($image = $this->getConfigValue('avatar_image_guest')))
		{
			$path = $this->filePath('tpl/img/default.jpeg');
			$image = GDO_File::fromPath('default.jpeg', $path)->insert();
			$column = $this->cfgColAvatarGuest();
			$column->createScaledVersions($image);
			$this->saveConfigVar('avatar_image_guest', $image->getID());
		}
	}

	# ##############
	# ## Install ###
	# ##############

	public function cfgColAvatarGuest(): GDT_ImageFile
	{
		return $this->getConfigColumn('avatar_image_guest');
	}

	public function cfgGuestAvatars(): string
	{
		return $this->getConfigVar('avatar_guests');
	}

	# ###########
	# ## Hook ###
	# ###########

	public function hookAccountChanged(GDO_User $user): void
	{
		$user->tempUnset('gdo_avatar');
	}

	public function hookAccountBar(GDT_Bar $bar): void
	{
		$bar->addField(GDT_Link::make('btn_avatar')->href(href('Avatar', 'Set')));
	}

}
