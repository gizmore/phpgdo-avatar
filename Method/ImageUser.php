<?php
namespace GDO\Avatar\Method;

use GDO\Core\Method;
use GDO\File\Method\GetFile;
use GDO\Util\Common;
use GDO\Avatar\GDO_Avatar;
use GDO\User\GDO_User;

/**
 * Get the avatar for a user.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.5.0
 * @deprecated You should better use \GDO\Avatar\Method\Image which has no caching problems.
 * @see Image
 */
final class ImageUser extends Method
{
	public function isSavingLastUrl() : bool { return false; }
	
	public function getMethodTitle() : string
	{
		return t('mt_avatar_image');
	}
	
	public function execute()
	{
		$userId = Common::getRequestString('id');
		if (!($user = GDO_User::getById($userId)))
		{
			# Ignore invalid users by returning guest/ghost.
			$user = GDO_User::ghost()->setVar('user_id', $userId);
		}

		$avatar = GDO_Avatar::forUser($user);
		
		$_REQUEST['file'] = $avatar->getFileID();
		
		return GetFile::make()->execute();
	}
}
