<?php namespace FacturaScripts\Plugins\Trazabilidad\Model;

use FacturaScripts\Dinamic\Model\Stock;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

class LineaFacturaCliente extends \FacturaScripts\Core\Model\LineaFacturaCliente
{
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