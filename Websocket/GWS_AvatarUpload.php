<?php
namespace GDO\Avatar\Websocket;

use GDO\Avatar\GDO_Avatar;
use GDO\Avatar\GDO_UserAvatar;
use GDO\File\GDO_File;
use GDO\Websocket\Server\GWS_Command;
use GDO\Websocket\Server\GWS_Commands;
use GDO\Websocket\Server\GWS_Global;
use GDO\Websocket\Server\GWS_Message;

/**
 * Websocket method wrapper for avatar upload.
 * Sends an event to all connected users with "userToRefresh".
 *
 * @version 6.10.4
 * @since 6.7.0
 **@author gizmore@wechall.net
 * @license MIT
 */
class GWS_AvatarUpload extends GWS_Command
{
	public function execute(GWS_Message $msg)
	{
		$identifier = preg_replace('/[^A-Za-z0-9_-]/', '', $msg->readString());
		if ((!$identifier) || !($file = $this->fileForIdentifier($identifier)))
		{
			$msg->replyErrorMessage($msg->cmd(), t('err_upload_failed'));
			return;
		}

		$user = $msg->user();
		$avatar = GDO_Avatar::blank(['avatar_file_id' => $file->getID()])->insert();
		GDO_UserAvatar::updateAvatar($user, $avatar->getID());

		$this->sendNotifications($msg, $user->getID(), $file->getID());
		$msg->replyBinary($msg->cmd(), '');
	}

	/**
	 * HTTP Flow uploads and WebSocket commands do not necessarily share a PHP
	 * session. Resolve the completed Flow upload by its client-side identifier.
	 */
	private function fileForIdentifier(string $identifier): ?GDO_File
	{
		foreach (glob(GDO_TEMP_PATH . 'flow/*/avatar_file_id/' . $identifier, GLOB_ONLYDIR) ?: [] as $dir)
		{
			if (is_file($dir . '/id') && ($id = trim((string)file_get_contents($dir . '/id'))) && ($file = GDO_File::getById($id)))
			{
				return $file;
			}
			if (is_file($dir . '/0') && is_file($dir . '/name'))
			{
				$file = GDO_File::fromPath(trim((string)file_get_contents($dir . '/name')), $dir . '/0');
				$file->insert();
				file_put_contents($dir . '/id', $file->getID());
				return $file;
			}
		}
		return null;
	}

	protected function sendNotifications(GWS_Message $msg, $userid, $avatarid)
	{
		$payload = $msg->wrCmd(0x0402) . $msg->wr32($userid) . $msg->wr32($avatarid);
		GWS_Global::broadcastBinary($payload);
	}

}

// Register command
GWS_Commands::register(0x0402, new GWS_AvatarUpload());
