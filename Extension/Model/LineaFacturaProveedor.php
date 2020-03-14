<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Model;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Plugins\Trazabilidad\Model\TrazabilidadStock;
use FacturaScripts\Plugins\Trazabilidad\Model\TrazabilidadProducto;

class LineaFacturaProveedor
{
    public function saveBefore()
    {
        return function() {
            if ($product = (new TrazabilidadProducto())->get($this->referencia))
            {
                if ($product->trazabilidadseries)
                {
                    $where = [
                        new DataBaseWhere('referencia', $this->referencia),
                        new DataBaseWhere('numserie', $this->numserie)
                    ];
                    if(!(new TrazabilidadStock())->getStock($where) && !empty($this->numserie))
                    {
                        $stock = new TrazabilidadStock();
                        $stock->idproducto = $product->idproducto;
                        $stock->referencia = $product->referencia;
                        $stock->disponible = 1;
                        $stock->cantidad = 1;
                        $stock->codalmacen = $_POST['codalmacen'];
                        $stock->numserie = $this->numserie;
                        $stock->save();

                    }
                }
            }
            return true;
        };
    }
    public function delete()
    {
        return function ()
        {
            $where = [
                new DataBaseWhere('referencia', $this->referencia),
                new DataBaseWhere('numserie', $this->numserie)
            ];
            $stock = (new TrazabilidadStock())->getStock($where);
            if ($stock)
            {
                $stock->delete();
            }
        };
    }

}