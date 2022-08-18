<?php
namespace GDO\Avatar\Method;

use GDO\Account\Module_Account;
use GDO\Avatar\GDT_Avatar;
use GDO\Avatar\GDO_UserAvatar;
use GDO\Core\GDT_Hook;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\Avatar\Module_Avatar;
use GDO\Core\GDT;
use GDO\UI\GDT_Container;

/**
 * Set an avatar picture out of possible choices.
 * 
 * @author gizmore
 */
final class Set extends MethodForm
{
	public function isUserRequired() : bool { return true; }
	public function isGuestAllowed() : bool { return Module_Avatar::instance()->cfgGuestAvatars(); }
	
	public function beforeExecute() : void
	{
		if (module_enabled('Account'))
		{
			Module_Account::instance()->renderAccountBar();
		}
	}
	
	public function renderPage() : GDT
	{
		$form = $this->getForm();
		$avatar = GDT_Avatar::make()->currentUser()->imageSize(128)->css('margin', '16px')->addClass('fl');
		return GDT_Container::make()->addFields($avatar, $form);
	}
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addClass('fl');
		$form->addField(GDT_Avatar::make('avt_avatar_id')->currentUser());
		$form->actions()->addField(GDT_Submit::make()->label('btn_set'));
		$form->actions()->addField(GDT_Button::make('btn_upload')->href(href('Avatar', 'Upload'))->icon('upload'));
		$form->addField(GDT_AntiCSRF::make());
	}

	public function formValidated(GDT_Form $form)
	{
		$user = GDO_User::current();
		GDO_UserAvatar::updateAvatar($user, $form->getFormVar('avt_avatar_id'));
		$this->resetForm(true);
		return $this->message('msg_avatar_set')->addField($this->renderPage());
	}
	
	public function afterExecute() : void
	{
		GDT_Hook::callWithIPC('AvatarSet', GDO_User::current());
	}

}
