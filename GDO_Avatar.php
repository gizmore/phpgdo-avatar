<?php
namespace GDO\Avatar;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Checkbox;
use GDO\User\GDO_User;
use GDO\File\GDT_ImageFile;
/**
 * An avatar image file.
 * @author gizmore
 */
class GDO_Avatar extends GDO
{
	public function gdoCached() : bool { return false; }
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('avatar_id'),
		    GDT_ImageFile::make('avatar_file_id')->notNull()->
		      previewHREF(href('Avatar', 'Image', '&file={id}'))->
		      scaledVersion('icon', 96, 96)->
		      scaledVersion('thumb', 375, 375),
			GDT_CreatedBy::make('avatar_created_by')->notNull(),
			GDT_Checkbox::make('avatar_public')->initial('0'),
		];
	}
	
	public function getID() : ?string { return $this->gdoVar('avatar_id'); }
	public function getFileID() { return $this->gdoVar('avatar_file_id'); }
	
	public static function defaultAvatar(GDO_User $user)
	{
		return self::table()->blank([
			'avatar_id'=>'0',
			'avatar_file_id' => self::getBestDefaultAvatar($user),
		]);
	}
	
	public static function getBestDefaultAvatar(GDO_User $user)
	{
		$keys = ['avatar_image_guest']; # Last resort
		if ($user->isMember())
		{
			$keys[] = 'avatar_image_member';
			switch ($user->getGender())
			{
				case 'male': $keys[] = 'avatar_image_male'; break;
				case 'female': $keys[] = 'avatar_image_female'; break;
			}
		}
		
		if (module_enabled('Avatar'))
		{
    		$module = Module_Avatar::instance();
    		foreach (array_reverse($keys) as $key)
    		{
    			if ($file = $module->getConfigValue($key))
    			{
    				return $file->getID();
    			}
    		}
		}
		return '0';
	}
	
	/**
	 * @param GDO_User $user
	 * @return self
	 */
	public static function forUser(GDO_User $user)
	{
		if (!$user->isPersisted())
		{
			return self::defaultAvatar($user);
		}
		
		if (null === ($avatar = $user->tempGet('gdo_avatar')))
		{
			$avatarTable = self::table();
			
			$query = GDO_UserAvatar::table()->select('*, gdo_file.*');
			$query->joinObject('avt_avatar_id');
			$query->join('JOIN gdo_file ON file_id = avatar_file_id');
			$query->where('avt_user_id='.$user->getID())->first();
			if (!($avatar = $query->exec()->fetchAs($avatarTable)))
			{
				$avatar = self::defaultAvatar($user);
			}
			$user->tempSet('gdo_avatar', $avatar);
			$user->recache();
		}
		return $avatar;
	}
	
	/**
	 * @param GDO_User $user
	 * @return GDT_Avatar
	 */
	public function getGDOAvatar(GDO_User $user)
	{
		static $gdt;
		if (!$gdt) $gdt = GDT_Avatar::make();
		return $gdt->user($user)->gdo($this);
	}
	
	public static function renderAvatar(GDO_User $user, $size=32)
	{
		return self::forUser($user)->getGDOAvatar($user)->imageSize($size)->renderCell();
	}
	
}
