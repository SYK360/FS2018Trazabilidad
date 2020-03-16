<?php namespace FacturaScripts\Plugins\Trazabilidad\Model;

use FacturaScripts\Dinamic\Model\Stock;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

class LineaFacturaProveedor extends \FacturaScripts\Core\Model\LineaFacturaProveedor{

    protected function updateStock()
    {
        if ($producto = (new TrazabilidadProducto())->get($this->referencia))
        {
            if (!$producto->trazabilidadseries)
                return parent::updateStock();
            return true;
        }
    }
}