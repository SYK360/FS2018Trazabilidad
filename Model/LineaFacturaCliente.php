<?php

namespace FacturaScripts\Plugins\Trazabilidad\Model;


use FacturaScripts\Dinamic\Model\Producto;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

class LineaFacturaCliente extends \FacturaScripts\Core\Model\LineaFacturaCliente{

    protected function updateStock()
    {
        $where = [
            new DataBaseWhere('referencia', $this->referencia),
            new DataBaseWhere('descripcion', $this->descripcion)
        ];
        if ((new Producto())->loadFromCode('', $where))
        {
            $product = (new Producto())->all($where)[0];
            $this->toolbox()->log()->info("Se actualizara un registro de stock para el producto: $product->referencia con el numero de serie: $this->numserie");

            if ($product->trazabilidadseries) {
                return true;
            }else{
                return parent::updateStock();
            }
        }
        return true;
    }
}