<?php
namespace FacturaScripts\Plugins\Trazabilidad\Model;


use FacturaScripts\Core\Model\Stock;

class EmptyStock extends Stock
{
    public function delete()
    {
        if (\FacturaScripts\Core\Model\Base\ModelClass::delete()) {
            return true;
        }

        return false;
    }
    public function save()
    {
        if (\FacturaScripts\Core\Model\Base\ModelClass::save()) {
            return true;
        }
        return false;
    }
}