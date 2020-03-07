<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Model;


use FacturaScripts\Core\Model\Stock;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Dinamic\Model\Producto;

class LineaFacturaProveedor
{
    public function saveBefore()
    {
        return function() {
            $stock = new Stock();
            $where = [
                new DataBaseWhere('referencia', $this->referencia),
                new DataBaseWhere('descripcion', $this->descripcion)
            ];
            $product = (new Producto())->all($where)[0];
            if($product->trazabilidadseries)
            {
                $where = [
                    new DataBaseWhere('referencia', $this->referencia),
                    new DataBaseWhere('numserie', $this->numserie)
                ];
                $stock = (new Stock())->all($where);
                if(empty($stock)){
                    $stock = new Stock();
                    $stock->referencia = $this->referencia;
                    //Aqui se guardara el stock para series
                }
            }else if($product->trazabilidadlotes){
                //Aqui se guardara para lotes
            }
            return true;
        };
    }
}