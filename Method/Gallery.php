<?php
namespace GDO\Avatar\Method;

use GDO\Table\MethodList;
use GDO\Avatar\GDO_Avatar;

final class Gallery extends MethodList
{
    public function gdoTable()
    {
        return GDO_Avatar::table();
    }

}
