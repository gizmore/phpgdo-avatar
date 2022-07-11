<?php
namespace GDO\Avatar\Websocket;

use GDO\Avatar\GDO_Avatar;
use GDO\Avatar\Method\Set;
use GDO\Websocket\Server\GWS_CommandForm;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Global;
use GDO\Websocket\Server\GWS_Message;
use GDO\User\GDO_User;

final class GWS_AvatarSet extends GWS_CommandForm
{
	public function getMethod() { return Set::make(); }
	
	public function hookAvatarSet($userId)
	{
		$user = GDO_User::findById($userId);
		$user->tempUnset('gdo_avatar');
		$payload = GWS_Message::payload(0x0401);
		$payload .= GWS_Message::wr32($user->getID());
		$payload .= GWS_Message::wr32(GDO_Avatar::forUser($user)->getFileID());
		GWS_Global::broadcastBinary($payload);
	}
}

GWS_Commands::register(0x0401, new GWS_AvatarSet());
