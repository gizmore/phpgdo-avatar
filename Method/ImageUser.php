<?php
namespace GDO\Avatar\Method;

use GDO\Avatar\GDO_Avatar;
use GDO\Core\Method;
use GDO\File\Method\GetFile;
use GDO\User\GDT_User;

/**
 * Get the avatar for a user.
 *
 * @version 7.0.1
 * @since 6.5.0
 * @author gizmore
 * @deprecated You should better use \GDO\Avatar\Method\Image which has no caching problems.
 * @see Image
 */
final class ImageUser extends Method
{

	public function isSavingLastUrl(): bool { return false; }

	public function getMethodTitle(): string
	{
		return t('mt_avatar_image');
	}

	public function gdoParameters(): array
	{
		return [
			GDT_User::make('id')->ghost()->notNull(),
		];
	}

	public function execute()
	{
		$user = $this->gdoParameterValue('id');
		$avatar = GDO_Avatar::forUser($user);
		$inputs = ['file' => $avatar->getFileID()];
		return GetFile::make()->executeWithInputs($inputs);
	}

}
