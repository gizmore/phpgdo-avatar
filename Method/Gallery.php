<?php
namespace GDO\Avatar\Method;

use GDO\Table\MethodList;
use GDO\Avatar\GDO_Avatar;
use GDO\Core\GDO;

final class Gallery extends MethodList
{
    public function gdoTable() : GDO
    {
        return GDO_Avatar::table();
    }

}
