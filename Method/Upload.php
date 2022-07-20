<?php
namespace GDO\Avatar\Method;

use GDO\Account\Module_Account;
use GDO\Account\Method\Settings;
use GDO\Avatar\GDO_Avatar;
use GDO\Avatar\GDO_UserAvatar;
use GDO\Core\Website;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Button;
use GDO\User\GDO_User;
use GDO\Avatar\Module_Avatar;
use GDO\UI\GDT_Redirect;

/**
 * Upload an avatar image.
 * 
 * @author gizmore
 * @version 6.10.4
 * @since 6.2.0
 */
final class Upload extends MethodForm
{
	public function isUserRequired() : bool { return true; }
	public function isGuestAllowed() : bool { return Module_Avatar::instance()->cfgGuestAvatars(); }
	
// 	public function beforeExecute() : void
// 	{
// 	    Module_Account::instance()->renderAccountTabs();
// 	    Settings::make()->navLinks();
// 	}
	
	public function createForm(GDT_Form $form) : void
	{
		$form->addField(GDO_Avatar::forUser(GDO_User::current())->gdoColumn('avatar_file_id')->action($this->href()));
		$form->actions()->addField(GDT_Submit::make()->label('btn_upload'));
		$form->addField(GDT_AntiCSRF::make());
		$form->actions()->addField(GDT_Button::make('btn_set_avatar')->href(href('Avatar', 'Set')));
	}
	
	public function formValidated(GDT_Form $form)
	{
		$user = GDO_User::current();
		$avatar = GDO_Avatar::blank(['avatar_file_id'=>$form->getFormVar('avatar_file_id')])->insert();
		GDO_UserAvatar::updateAvatar($user, $avatar->getID());
		$user->recache();
		$this->resetForm();
		return GDT_Redirect::make()->redirectMessage('msg_avatar_uploaded')->back();
		Website::redirectMessage('msg_avatar_uploaded', null, href('Avatar', 'Set'));
	}
	
}
