<?php namespace FacturaScripts\Plugins\Trazabilidad\Model;

use FacturaScripts\Core\Model\Producto;

class TrazabilidadProducto extends Producto
{
    public static function primaryColumn()
    {
        return 'referencia';
    }
}