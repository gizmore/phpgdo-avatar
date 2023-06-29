<?php
namespace GDO\Avatar;

use GDO\Core\GDT_ObjectSelect;
use GDO\Core\WithGDO;
use GDO\File\GDO_File;
use GDO\UI\WithImageSize;
use GDO\User\GDO_User;

/**
 * An avatar.
 *
 * @version 7.0.1
 * @since 6.4.0
 * @author gizmore
 */
final class GDT_Avatar extends GDT_ObjectSelect
{

	use WithGDO;
	use WithImageSize;

	public GDO_User $user;
	public bool $withLink = true;

	protected function __construct()
	{
		parent::__construct();
		$this->icon = 'image';
		$this->emptyInitial('choice_no_avatar');
		$this->table(GDO_Avatar::table());
	}

	public function isTestable(): bool
	{
		return false;
	}

	# ###########
	# ## User ###
	# ###########

	public function gdtDefaultName(): ?string
	{
		return 'avatar';
	}

	public function gdtDefaultLabel(): ?string
	{
		return 'avatar';
	}

	protected function getChoices(): array
	{
		$choices = [];
		if (isset($this->user))
		{
			$query = GDO_Avatar::table()->select();
			$result = $query->select('avatar_file_id_t.*')
				->where("avatar_public OR avatar_created_by={$this->user->getID()}")
				->joinObject('avatar_file_id')
//				->fetchTable(GDO_File::table())
				->exec();
			while ($gdo = $result->fetchObject())
			{
				$choices[$gdo->getID()] = $gdo;
			}
		}
		return $choices;
	}

	public function renderHTML(): string
	{
		return Module_Avatar::instance()->php('avatar_html.php', [
			'field' => $this,
		]);
	}

	# ##############
	# ## Choices ###
	# ##############

	public function currentUser()
	{
		return $this->user(GDO_User::current());
	}

	################
	### WithLink ###
	################

	public function user(GDO_User $user)
	{
		$this->user = $user;
		$this->gdo = GDO_Avatar::forUser($user);
		$this->initial($this->gdo->getID());
		return $this;
	}

	public function hrefUser()
	{
		return $this->user->hrefProfile();
	}

	# #############
	# ## Render ###
	# #############

	public function withProfileLink(bool $withLink = true): self
	{
		$this->withLink = $withLink;
		return $this;
	}

}
