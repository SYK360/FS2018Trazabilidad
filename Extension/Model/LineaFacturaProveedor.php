<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Model;


use FacturaScripts\Core\Model\Stock;
use FacturaScripts\Core\Base\ToolBox;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Dinamic\Model\Producto;

class LineaFacturaProveedor
{
    public function saveBefore()
    {
        return function() {
            $where = [
                new DataBaseWhere('referencia', $this->referencia),
                new DataBaseWhere('descripcion', $this->descripcion)
            ];
            if ((new Producto())->loadFromCode('', $where)) {
                $product = (new Producto())->all($where)[0];
                if($product->trazabilidadseries)
                {
                    $where = [
                        new DataBaseWhere('referencia', $this->referencia),
                        new DataBaseWhere('numserie', $this->numserie)
                    ];
                    if(!(new Stock())->loadFromCode('', $where))
                    {
                        $stock = new Stock();
                        $stock->idproducto = $product->idproducto;
                        $stock->referencia = $product->referencia;
                        $stock->disponible = 1;
                        $stock->cantidad = 1;
                        $stock->numserie = $this->numserie;
                        $stock->codalmacen = $_POST['codalmacen'];
                        if ($stock->save()) {
                            ToolBox::log()->notice("Se creo un stock para el producto: $product->referencia con el numero de serie: $this->numserie");
                        }else{
                           ToolBox::log()->warning("Ocurrio un problema con el stock al guardar la linea");
                        }

                    }
                }else if($product->trazabilidadlotes)
                {
                    $where = [
                        new DataBaseWhere('referencia', $this->referencia),
                        new DataBaseWhere('lote', $this->numserie)
                    ];
                    if(!(new Stock())->loadFromCode('', $where))
                    {
                        $stock = new Stock();
                        $stock->idproducto = $product->idproducto;
                        $stock->referencia = $product->referencia;
                        $stock->disponible = $this->cantidad;
                        $stock->cantidad = $this->cantidad;
                        $stock->numserie = $this->numserie;
                        $stock->codalmacen = $_POST['codalmacen'];
                        if ($stock->save()) {
                            ToolBox::log()->notice("Se creo un stock para el producto: $product->referencia con el numero de lote: $this->numserie");
                        }else{
                            ToolBox::log()->warning("Ocurrio un problema con el stock al guardar la linea");
                        }

                    }
                }
            }
            return true;
        };
    }
}