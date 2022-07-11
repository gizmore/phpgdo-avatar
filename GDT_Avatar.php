<?php
namespace GDO\Avatar;

use GDO\User\GDO_User;
use GDO\Core\GDT_ObjectSelect;
use GDO\UI\WithImageSize;

/**
 * An avatar.
 * @author gizmore
 * @version 6.10.4
 * @since 6.4.0
 */
final class GDT_Avatar extends GDT_ObjectSelect
{
    use WithImageSize;
    
    public function defaultName() { return 'avatar'; }
    public function defaultLabel() : self { return $this->label('avatar'); }
    
	protected function __construct()
	{
	    parent::__construct();
	    $this->icon = 'image';
		$this->emptyLabel = 'choice_no_avatar';
		$this->table(GDO_Avatar::table());
	}
	
	/**
	 * @var GDO_User
	 */
	public $user;
	public function currentUser()
	{
		return $this->user(GDO_User::current());
	}
	
	public function user(GDO_User $user)
	{
		$this->user = $user;
		$this->gdo = GDO_Avatar::forUser($user);
		$this->var = $this->gdo->getID();
		return $this;
	}
	
	public function initChoices()
	{
		if (!$this->choices)
		{
			$this->choices($this->avatarChoices());
		}
	}
	public function avatarChoices()
	{
		$query = GDO_Avatar::table()->select();
		$result = $query->select('avatar_file_id_t.*, gdo_file.*')->
		  where("avatar_public OR avatar_created_by={$this->user->getID()}")->exec();
		$choices = array();
		while ($gwfAvatar = $result->fetchObject())
		{
			$choices[$gwfAvatar->getID()] = $gwfAvatar;
		}
		return $choices;
	}
	
	public function renderChoice($avatar)
	{
		$gdo = $this->gdo;
		$var = $this->var;
		$html = Module_Avatar::instance()->php('choice/avatar.php', ['field'=>$this->gdo($avatar)]);
		$this->gdo = $gdo;
		$this->var = $var;
		return $html;
	}

	public function renderCell() : string
	{
		return Module_Avatar::instance()->php('cell/avatar.php', ['field'=>$this]);
	}
}
