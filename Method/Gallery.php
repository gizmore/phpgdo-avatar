<?php
namespace GDO\Avatar\Method;

use GDO\Avatar\GDO_Avatar;
use GDO\Core\GDO;
use GDO\Table\MethodQueryList;

/**
 * A list of user avatars to view in a gallery.
 *
 * @version 7.0.1
 * @since 6.9.0
 * @author gizmore
 */
final class Gallery extends MethodQueryList
{

	public function gdoTable(): GDO
	{
		return GDO_Avatar::table();
	}

}
