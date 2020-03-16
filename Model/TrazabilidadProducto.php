<?php namespace FacturaScripts\Plugins\Trazabilidad\Model;

use FacturaScripts\Core\Model\Variante;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

class TrazabilidadProducto extends Variante
{
    public static function primaryColumn()
    {
        return 'referencia';
    }
    public function get($code)
    {
        $where = [new DataBaseWhere('referencia', $code)];
        if (!empty($code) && $this->loadFromCode('', $where)) {
            return $this->getProducto();
        }
        return null;
    }
}