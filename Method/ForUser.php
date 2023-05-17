<?php
namespace GDO\Avatar\Method;

use GDO\Avatar\GDO_Avatar;
use GDO\Avatar\Module_Avatar;
use GDO\Core\GDO_ArgError;
use GDO\Core\GDT;
use GDO\Core\Method;
use GDO\File\GDT_File;
use GDO\File\Method\GetFile;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * Get an avatar image for a user.
 * Avatar images do not have any permission checking.
 *
 * @version 7.0.3
 * @since 6.9.0
 * @author gizmore
 * @see GetFile
 */
final class ForUser extends Method
{

	public function isTrivial(): bool { return false; }

	public function isSavingLastUrl(): bool { return false; }

	public function isUserRequired(): bool { return false; }

	public function gdoParameters(): array
	{
		return [
			GDT_User::make('id'),
		];
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function getUser(): GDO_User
	{
		return $this->gdoParameterValue('id');
	}

	/**
	 * @throws GDO_ArgError
	 */
	public function execute(): GDT
	{
		$user = $this->getUser();
		$file = GDO_Avatar::forUser($user)->getFileID();
		return Image::make()->executeWithInputs(['file' => $file]);
	}

}
