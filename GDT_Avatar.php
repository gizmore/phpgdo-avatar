<?php
namespace GDO\Avatar;

use GDO\User\GDO_User;
use GDO\Core\GDT_ObjectSelect;
use GDO\UI\WithImageSize;
use GDO\Core\WithGDO;

/**
 * An avatar.
 *
 * @author gizmore
 * @version 7.0.1
 * @since 6.4.0
 */
final class GDT_Avatar extends GDT_ObjectSelect
{
	use WithGDO;
	use WithImageSize;
	
	public function isTestable(): bool
	{
		return false;
	}

	public function getDefaultName(): ?string
	{
		return 'avatar';
	}

	public function defaultLabel(): static
	{
		return $this->label('avatar');
	}

	protected function __construct()
	{
		parent::__construct();
		$this->icon = 'image';
		$this->emptyInitial('choice_no_avatar');
		$this->table(GDO_Avatar::table());
	}

	# ###########
	# ## User ###
	# ###########
	public GDO_User $user;

	public function user(GDO_User $user)
	{
		$this->user = $user;
		$this->gdo = GDO_Avatar::forUser($user);
		$this->initial($this->gdo->getID());
		return $this;
	}

	public function currentUser()
	{
		return $this->user(GDO_User::current());
	}
	
	public function hrefUser()
	{
		return $this->user->hrefProfile();
	}

	# ##############
	# ## Choices ###
	# ##############
	public function getChoices(): array
	{
		$choices = [];
		if (isset($this->user))
		{
			$query = GDO_Avatar::table()->select();
			$result = $query->select('avatar_file_id_t.*')
				->where("avatar_public OR avatar_created_by={$this->user->getID()}")
				->joinObject("avatar_file_id")
				->exec();
			while ($gdo = $result->fetchObject())
			{
				$choices[$gdo->getID()] = $gdo;
			}
		}
		return $choices;
	}

	################
	### WithLink ###
	################
	public bool $withLink = true;
	public function withProfileLink(bool $withLink=true): static
	{
		$this->withLink = $withLink;
		return $this;
	}
	
	# #############
	# ## Render ###
	# #############
	public function renderHTML(): string
	{
		return Module_Avatar::instance()->php('avatar_html.php', [
			'field' => $this
		]);
	}

}
