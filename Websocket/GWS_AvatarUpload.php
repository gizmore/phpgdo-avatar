<?php
namespace GDO\Avatar\Websocket;

use GDO\Websocket\Server\GWS_CommandForm;
use GDO\Avatar\Method\Upload;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Global;
use GDO\Websocket\Server\GWS_Message;
use GDO\Avatar\GDO_Avatar;

/**
 * Websocket method wrapper for avatar upload.
 * Sends an event to all connected users with "userToRefresh".
 * 
 * @author gizmore@wechall.net
 * @license MIT
 * @version 6.10.4
 * @since 6.7.0
 **/
class GWS_AvatarUpload extends GWS_CommandForm
{
	public function getMethod() { return Upload::make(); }
	
	public function afterReplySuccess(GWS_Message $msg)
	{
		$user = $msg->user();
		$user->tempUnset('gdo_avatar');
		$avatarid = GDO_Avatar::forUser($user)->getFileID();
		
		$this->sendNotifications($msg, $user->getID(), $avatarid);
	}
	
	protected function sendNotifications(GWS_Message $msg, $userid, $avatarid)
	{
		$payload = $msg->wrCmd(0x0402).$msg->wr32($userid).$msg->wr32($avatarid);
		GWS_Global::broadcastBinary($payload);
	}
}

// Register command
GWS_Commands::register(0x0402, new GWS_AvatarUpload());
