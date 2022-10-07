<?php
namespace GDO\Avatar\Method;

use GDO\Avatar\Module_Avatar;
use GDO\Core\Method;
use GDO\File\Method\GetFile;
use GDO\File\GDT_File;

/**
 * Get an avatar image.
 * Avatar images do not have any permission checking.
 * 
 * TODO: better error handling for non existing images.
 * 
 * @version 7.0.1
 * @since 6.9.0
 * @author gizmore
 * @see GetFile
 */
final class Image extends Method
{
	public function isTrivial() : bool { return false; }

	public function isSavingLastUrl() : bool { return false; }
	
	public function isUserRequired() : bool { return false; }
	
	public function gdoParameters() : array
	{
		return [
			GDT_File::make('file'),
		];
	}
	
	public function getFileID() : ?string
	{
		return $this->gdoParameterVar('file');
	}
	
	public function execute()
	{
		if (!($this->getFileID()))
		{
			hdr('Content-Type: image/jpeg');
			die(Module_Avatar::instance()->templateFile('img/default.jpeg'));
		}
		return GetFile::make()->executeWithInputs($this->inputs, false);
	}
	
}
