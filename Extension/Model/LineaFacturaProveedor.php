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
            $this->toolBox()->i18nLog()->error('record-not-found');
            $where = [
                new DataBaseWhere('referencia', $this->referencia),
                new DataBaseWhere('descripcion', $this->descripcion)
            ];
            if ((new Producto())->loadFromCode('', $where)) {
                $product = (new Producto())->all($where)[0];
                $stock = new Stock();
                $stock->idproducto = $product->idproducto;
                $stock->referencia = $product->referencia;
                $stock->disponible = 1;
                $stock->cantidad = 1;
                $stock->codalmacen = $_POST['codalmacen'];
                $where = [new DataBaseWhere('referencia', $this->referencia)];
                if($product->trazabilidadseries)
                {
                    $where[] = new DataBaseWhere('numserie', $this->numserie);
                    if(!(new Stock())->loadFromCode('', $where))
                    {
                        $stock->numserie = $this->numserie;
                        if ($stock->save()) {
                            ToolBox::log()->notice("Se creo un stock para el producto: $product->referencia con el numero de serie: $this->numserie");
                        }

                    }
                }else if($product->trazabilidadlotes)
                {
                    $where[] = new DataBaseWhere('lote', $this->lote);
                    if(!(new Stock())->loadFromCode('', $where))
                    {
                        $stock->cantidad = $this->cantidad;
                        $stock->lote = $this->lote;
                        if ($stock->save()) {
                            ToolBox::log()->notice("Se creo un stock para el producto: $product->referencia con el numero de lote: $this->lote");
                        }
                    }
                }
            }
            return true;
        };
    }
}