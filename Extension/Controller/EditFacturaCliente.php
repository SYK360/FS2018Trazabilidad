<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Controller;

use FacturaScripts\Plugins\Trazabilidad\Model\TrazabilidadProducto;

class EditFacturaCliente
{
    public function execPreviousAction()
    {
        return function ($action)
        {
           if ($action == 'get-trazabilidad')
           {
              $this->setTemplate(false);
              $dataProduct = $this->request->request->get('product');
              if ($product = (new TrazabilidadProducto())->get($dataProduct['referencia']))
              {
                  $data = [];
                  if ($product->trazabilidadseries){
                      $data['autosave'] = false;
                      $data['trazabilidad'] = 'series';
                  }
                  $this->response->setContent(json_encode($data));
              }
           }
        };
    }
}