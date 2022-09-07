<?php
namespace GDO\Avatar;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_Checkbox;
use GDO\User\GDO_User;
use GDO\File\GDT_ImageFile;
use GDO\Core\GDT_Template;
use GDO\Table\GDT_ListItem;
use GDO\Core\GDT_CreatedAt;

/**
 * An avatar image file.
 * 
 * @author gizmore
 * @version 7.0.1
 * @since 6.2.0
 */
class GDO_Avatar extends GDO
{
	###########
	### GDO ###
	###########
	public function gdoCached() : bool { return false; }
	public function gdoColumns() : array
	{
		return [
			GDT_AutoInc::make('avatar_id'),
		    GDT_ImageFile::make('avatar_file_id')->notNull()->
		      previewHREF(href('Avatar', 'Image', '&file={id}'))->
		      scaledVersion('icon', 96, 96)->
		      scaledVersion('thumb', 375, 375),
			GDT_Checkbox::make('avatar_public')->initial('0'),
			GDT_CreatedBy::make('avatar_created_by')->notNull(),
			GDT_CreatedAt::make('avatar_created_at')->notNull(),
		];
	}
	
	public function getID() : ?string { return $this->gdoVar('avatar_id'); }
	public function getFileID() : ?string { return $this->gdoVar('avatar_file_id'); }
	public function getUser() : GDO_User { return $this->gdoValue('avatar_created_by'); }
	public function getUserID() : string { return $this->gdoVar('avatar_created_by'); }
	
	######################
	### Default Avatar ###
	######################
	public static function defaultAvatar(GDO_User $user) : self
	{
		return self::table()->blank([
			'avatar_id'=>'0',
			'avatar_file_id' => self::getBestDefaultAvatar($user),
		]);
	}
	
	/**
	 * Get the best matching default avatar for a user.
	 * Depends on gender and usertype.
	 */
	public static function getBestDefaultAvatar(GDO_User $user) : ?string
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
		
		return null;
	}
	
	###################
	### User Avatar ###
	###################
	public static function forUser(GDO_User $user) : self
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
	
	public function getGDOAvatar(GDO_User $user) : GDT_Avatar
	{
		static $gdt;
		if (!$gdt) $gdt = GDT_Avatar::make();
		return $gdt->user($user)->gdo($this);
	}
	
	##############
	### Render ###
	##############
	public static function renderAvatar(GDO_User $user, float $size=32) : string
	{
		return self::forUser($user)->getGDOAvatar($user)->imageSize($size)->render();
	}
	
	public function renderOption() : string
	{
		$field = GDT_Avatar::make()->gdo($this);
		return GDT_Template::php('Avatar', 'avatar_choice.php', ['field' => $field]);
	}
	
	public function renderList() : string
	{
		$li = GDT_ListItem::make()->gdo($this);
		$li->creatorHeader();
		$
		$views = $this->
		$li->title('li_avatar', [$views]);
		return $li->render();
	}

}
