<?php

namespace FacturaScripts\Plugins\Trazabilidad;

use FacturaScripts\Core\Base\ToolBox;
use FacturaScripts\Core\Base\InitClass;

class Init extends InitClass
{

    public function init()
    {
        $this->loadExtension(new Extension\Controller\EditFacturaProveedor());
        $this->loadExtension(new Extension\Controller\EditFacturaCliente());
        $this->loadExtension(new Extension\Model\LineaFacturaProveedor());
        $this->loadExtension(new Extension\Model\LineaFacturaCliente());

    }

    public function update()
    {
        $value = ini_get('max_input_vars');
        if(intval($value) < 3000){
            ToolBox::log()->warning("Porfavor cambie el valor max_input_vars de su php.ini a un mínimo de 3000 para poder enviar mas de 100 líneas en productos que tengan trazabilidad por serie. ");
        }
    }
}
