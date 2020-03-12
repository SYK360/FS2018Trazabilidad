<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Model;

use FacturaScripts\Core\Model\Stock;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Dinamic\Model\Producto;
use FacturaScripts\Plugins\Trazabilidad\Model\TrazabilidadStock;

class LineaFacturaCliente
{
    public function saveBefore()
    {
        return function() {
            $where = [
                new DataBaseWhere('referencia', $this->referencia),
                new DataBaseWhere('descripcion', $this->descripcion)
            ];
            if ((new Producto())->loadFromCode('', $where))
            {
                $product = (new Producto())->all($where)[0];
                if ($product->trazabilidadseries)
                {
                    $where = [
                        new DataBaseWhere('referencia', $this->referencia),
                        new DataBaseWhere('numserie', $this->numserie)
                    ];
                    $stock = (new TrazabilidadStock())->getStock($where);
                    if($stock && !empty($this->numserie))
                    {
                        if($stock->cantidad == 1){
                            $stock->disponible = 0;
                            $stock->cantidad = 0;
                            $stock->save();
                        }else{
                            $this->toolbox()->log()->error("El stock con serie: $stock->numserie tiene conflictos, no tiene stock o esta registrado en una factura.");
                            return false;
                        }
                    }
                }
            }
            return true;
        };
    }

}