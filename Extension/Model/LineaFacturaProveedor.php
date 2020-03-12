<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Model;

use FacturaScripts\Core\Model\Stock;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;
use FacturaScripts\Dinamic\Model\Producto;
use FacturaScripts\Plugins\Trazabilidad\Model\EmptyStock;

class LineaFacturaProveedor
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
                $this->toolbox()->log()->info("Se actualizara un registro de stock para el producto: $product->referencia con el numero de serie: $this->numserie");

                if ($product->trazabilidadseries)
                {
                    $where = [
                        new DataBaseWhere('referencia', $this->referencia),
                        new DataBaseWhere('numserie', $this->numserie)
                    ];
                    if(!(new Stock())->loadFromCode('', $where) && !empty($this->numserie))
                    {
                        $stock = new EmptyStock();
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
    public function delete(){
        return function (){
            $where = [
                new DataBaseWhere('referencia', $this->referencia),
                new DataBaseWhere('numserie', $this->numserie)
            ];
            $stock = (new EmptyStock())->all($where);
            if(isset($stock[0])){
                $this->toolbox()->log()->info("Se eliminara el stock con la serie: " . $stock[0]->numserie);
                $stock[0]->delete();
            }
        };
    }

}