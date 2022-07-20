<?php
namespace GDO\Avatar\Method;

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
use GDO\Core\GDT_Tuple;

/**
 * Set an avatar picture out of possible choices.
 * @author gizmore
 */
final class Set extends MethodForm
{
	public function isUserRequired() : bool { return true; }
	public function isGuestAllowed() : bool { return Module_Avatar::instance()->cfgGuestAvatars(); }
	
// 	public function beforeExecute() : void
// 	{
// 	    Module_Account::instance()->renderAccountTabs();
// 	    Settings::make()->navLinks();
// 	}

	public function renderPage() : GDT
	{
		$avatar = GDT_Avatar::make()->currentUser()->imageSize(128)->css('margin', '16px');
		return GDT_Tuple::make()->addField($avatar)->addField(parent::renderPage());
	}
	
	public function createForm(GDT_Form $form) : void
	{
// 	    $form->addField(
// 	        GDT_HTML::make()->addField(
// 	            GDT_Avatar::make()->currentUser()->
// 	                imageSize(128)->css('margin', '16px')));
		$form->addField(GDT_Avatar::make('avt_avatar_id')->currentUser());
		$form->actions()->addField(GDT_Submit::make()->label('btn_set'));
		$form->actions()->addField(GDT_Button::make('btn_upload')->href(href('Avatar', 'Upload'))->icon('upload'));
		$form->addField(GDT_AntiCSRF::make());
// 		$form->withGDOValuesFrom(GDO_User::current());
	}

	public function formValidated(GDT_Form $form)
	{
		$user = GDO_User::current();
		GDO_UserAvatar::updateAvatar($user, $form->getFormVar('avt_avatar_id'));
// 		$user->recache();
// 		$this->resetForm();
		return $this->message('msg_avatar_set')->addField($this->renderPage());
	}
	
	public function afterExecute() : void
	{
		GDT_Hook::callWithIPC('AvatarSet', GDO_User::current());
	}

}
