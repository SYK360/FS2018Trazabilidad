<?php
namespace FacturaScripts\Plugins\Trazabilidad\Model;


use FacturaScripts\Core\Model\Stock;

class TrazabilidadStock extends Stock
{
    public function delete()
    {
        if (\FacturaScripts\Core\Model\Base\ModelClass::delete())
        {
            return true;
        }

        return false;
    }

    public function save()
    {
        if (\FacturaScripts\Core\Model\Base\ModelClass::save())
        {
            return true;
        }
        return false;
    }

    public function primaryDescriptionColumn()
    {
        return 'referencia';
    }

    public function getStock($where)
    {
        if($this->loadFromCode('', $where))
        {
            return $this->all($where)[0];
        }
        return  false;
    }
}