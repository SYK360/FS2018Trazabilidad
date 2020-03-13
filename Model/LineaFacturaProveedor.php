<?php

namespace FacturaScripts\Plugins\Trazabilidad\Model;


use FacturaScripts\Dinamic\Model\Producto;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

class LineaFacturaProveedor extends \FacturaScripts\Core\Model\LineaFacturaProveedor{

    protected function updateStock()
    {
        $where = [
            new DataBaseWhere('referencia', $this->referencia),
            new DataBaseWhere('descripcion', $this->descripcion)
        ];
        if ((new Producto())->loadFromCode('', $where))
        {
            $product = (new Producto())->all($where)[0];
            $this->toolbox()->log()->info("Se actualizará un registro de stock para el producto: $product->referencia con el número de serie: $this->numserie");

            if (!$product->trazabilidadseries)
            {
                return parent::updateStock();
            }
            return true;
        }
        return true;
    }
}