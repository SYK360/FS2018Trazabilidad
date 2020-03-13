<?php namespace FacturaScripts\Plugins\Trazabilidad\Model;


class LineaFacturaCliente extends \FacturaScripts\Core\Model\LineaFacturaCliente{

    protected function updateStock()
    {
        if ($product = (new TrazabilidadProducto())->get($this->referencia))
        {
            if (!$product->trazabilidadseries)
            {
                return parent::updateStock();
            }
            return true;
        }
        return true;
    }
}