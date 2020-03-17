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
                if ($product->trazabilidadseries && !empty($this->numserie))
                {
                    $where = [
                        new DataBaseWhere('referencia', $this->referencia),
                        new DataBaseWhere('numserie', $this->numserie)
                    ];

                    if(!(new TrazabilidadStock())->getStock($where))
                    {
                        $stock = new TrazabilidadStock();
                        $stock->cantidad = 1;
                        $stock->disponible = 1;
                        $stock->numserie = $this->numserie;
                        $stock->referencia = $this->referencia;
                        $stock->idproducto = $product->idproducto;
                        $stock->codalmacen = $this->getDocument()->codalmacen;
                        $stock->save();
                        return true;
                    } else {
                        $linea = $this->all($where);
                        if(count($linea) == 1 && $this->idfactura && $this->idfactura == $linea[0]->idfactura && $linea[0]->idlinea == $this->idlinea)
                            return  true;

                        $this->toolbox()->log()->error("El producto con serie $this->numserie ya esta regisrado.");
                        return false;
                    }
                }
                $this->toolbox()->log()->error("El producto $this->referencia tiene trazabilidad, ingrese la serie.");
                return false;
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