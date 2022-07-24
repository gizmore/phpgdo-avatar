<?php
namespace GDO\Avatar\Method;

use GDO\Avatar\Module_Avatar;
use GDO\Core\Method;
use GDO\Util\Common;
use GDO\File\Method\GetFile;

/**
 * Get an avatar image.
 * Avatar images do not have any permission checking.
 * 
 * TODO: better error handling for non existing images.
 * 
 * @version 6.09
 * @author gizmore
 * @see GetFile
 */
final class Image extends Method
{
	public function saveLastUrl() : bool { return false; }
	
	public function execute()
	{
		if (Common::getRequestInt('file') == 0)
		{
			header('Content-Type: image/jpeg');
			die(Module_Avatar::instance()->templateFile('img/default.jpeg'));
		}
		return GetFile::make()->execute();
	}
}
