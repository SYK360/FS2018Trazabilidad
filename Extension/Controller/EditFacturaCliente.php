<?php namespace FacturaScripts\Plugins\Trazabilidad\Extension\Controller;

use FacturaScripts\Dinamic\Model\Producto;
use FacturaScripts\Core\Base\DataBase\DataBaseWhere;

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
              $where = [
                  new DataBaseWhere('referencia', $dataProduct['referencia']),
                  new DataBaseWhere('descripcion', $dataProduct['descripcion'])
              ];

              $product = (new Producto())->all($where);
              if (isset($product[0]))
              {
                  $data = [];
                  $product = $product[0];
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