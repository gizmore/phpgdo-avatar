<?php
namespace GDO\Avatar;

use GDO\Core\GDO;
use GDO\Core\GDT_Object;
use GDO\File\GDO_File;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\Core\GDT_CreatedAt;

/**
 * Avatar entity.
 * @author gizmore
 * @version 6.10.1
 */
final class GDO_UserAvatar extends GDO
{
	public function gdoCached() : bool { return false; }
	
	public function gdoColumns() : array
	{
		return [
			GDT_User::make('avt_user_id')->primary(),
			GDT_Object::make('avt_avatar_id')->table(GDO_Avatar::table())->notNull(),
			GDT_CreatedAt::make('avt_created_at'),
		];
	}
	
	public static function updateAvatar(GDO_User $user, $avatarId)
	{
		$user->tempUnset('gdo_avatar');
		if ($avatarId > 0)
		{
			GDO_UserAvatar::blank(['avt_user_id'=>$user->getID(), 'avt_avatar_id'=>$avatarId])->replace();
		}
		else
		{
			GDO_UserAvatar::table()->deleteWhere('avt_user_id='.$user->getID());
		}
		$user->recache();
		return true;
	}
	
	public static function createAvatarFromString(GDO_User $user, $filename, $contents)
	{
		$file = GDO_File::fromString($filename, $contents)->insert()->copy();
		$avatar = GDO_Avatar::blank(['avatar_file_id' => $file->getID()])->insert();
		return self::updateAvatar($user, $avatar->getID());
	}
	
}
