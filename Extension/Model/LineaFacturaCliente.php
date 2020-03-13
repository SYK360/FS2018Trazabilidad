<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Model;

use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Plugins\Trazabilidad\Model\TrazabilidadStock;
use FacturaScripts\Plugins\Trazabilidad\Model\TrazabilidadProducto;

class LineaFacturaCliente
{
    public function saveBefore()
    {
        return function()
        {
            if ($product = (new TrazabilidadProducto())->get($this->referencia))
            {
                if ($product->trazabilidadseries)
                {
                    $where = [
                        new DataBaseWhere('referencia', $this->referencia),
                        new DataBaseWhere('numserie', $this->numserie)
                    ];
                    $stock = (new TrazabilidadStock())->getStock($where);
                    if($stock && !empty($this->numserie))
                    {
                        if($this->cantidad != 1)
                        {
                            $this->toolbox()->log()->error("Solo posee un producto con la serie $this->numserie, cambie la cantidad.");
                            return false;
                        }
                        if ($stock->cantidad == 1)
                        {
                            $stock->disponible = 0;
                            $stock->cantidad = 0;
                            $stock->save();
                            return true;
                        }
                        else {
                            $this->toolbox()->log()->error("El producto con serie $stock->numserie tiene conflictos, no tiene stock o esta registrado en otra factura.");
                        }
                    } else {
                        $this->toolbox()->log()->warning("El producto tiene trazabilidad, seleccione la serie.");
                    }
                    return false;
                }
            }
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
                $stock->cantidad = 1;
                $stock->disponible = 1;
                if ($stock->save())
                {
                    $this->toolbox()->log()->info("El stock con serie $stock->numserie se ha restaurado.");
                }
            }
        };
    }
}