<?php
namespace GDO\Avatar\Method;

use GDO\Account\Module_Account;
use GDO\Avatar\GDO_Avatar;
use GDO\Avatar\GDO_UserAvatar;
use GDO\Avatar\Module_Avatar;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Button;
use GDO\UI\GDT_Redirect;
use GDO\User\GDO_User;

/**
 * Upload an avatar image.
 *
 * @version 7.0.1
 * @since 6.2.0
 * @author gizmore
 */
final class Upload extends MethodForm
{

	public function isUserRequired(): bool { return true; }

	public function isGuestAllowed(): bool { return Module_Avatar::instance()->cfgGuestAvatars(); }

	public function onRenderTabs(): void
	{
		if (module_enabled('Account'))
		{
			Module_Account::instance()->renderAccountBar();
		}
	}

	public function createForm(GDT_Form $form): void
	{
		$form->addField(GDO_Avatar::forUser(GDO_User::current())->gdoColumn('avatar_file_id')->action($this->href()));
		$form->actions()->addField(GDT_Submit::make()->label('btn_upload'));
		$form->addField(GDT_AntiCSRF::make());
		$form->actions()->addField(GDT_Button::make('btn_set_avatar')->href(href('Avatar', 'Set')));
	}

	public function formValidated(GDT_Form $form)
	{
		$user = GDO_User::current();
		$avatar = GDO_Avatar::blank(['avatar_file_id' => $form->getFormVar('avatar_file_id')])->insert();
		GDO_UserAvatar::updateAvatar($user, $avatar->getID());
		$user->recache();
		$this->resetForm();
		return GDT_Redirect::make()->redirectMessage('msg_avatar_uploaded')->href(href('Avatar', 'Set'));
	}

}
