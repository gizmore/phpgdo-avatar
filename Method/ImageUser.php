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
 * @version 6.09
 * @since 6.05
 * @deprecated You should better use \GDO\Avatar\Method\Image which has no caching problems.
 * @see Image
 */
final class ImageUser extends Method
{
	public function saveLastUrl() { return false; }
	
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
