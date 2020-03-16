<?php namespace FacturaScripts\Plugins\Trazabilidad\Model;


class LineaFacturaProveedor extends \FacturaScripts\Core\Model\LineaFacturaProveedor{

    protected function updateStock()
    {
        if ($product = (new TrazabilidadProducto())->get($this->referencia))
        {
            if (!$product->trazabilidadseries)
            {
                return parent::updateStock();
            }
            $this->toolbox()->log()->info("Se actualizará un registro de stock para el producto $product->referencia con el número de serie $this->numserie");
            return true;
        }
    }
}