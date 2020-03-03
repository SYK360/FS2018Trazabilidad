<?php
/**
 * This file is part of SamplePlugin for FacturaScripts
 * Copyright (C) 2019 Carlos Garcia Gomez <carlos@facturascripts.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace FacturaScripts\Plugins\SamplePlugin\Extension\Controller;

use FacturaScripts\Dinamic\Model\Producto;

/**
 * Description of ListProducto
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class ListProducto
{

    public function createViews()
    {
        return function() {
            $this->createViewLogs();

            /// Test new methods in Producto model
            $product = new Producto();
            $product->referencia = '1234';
            $this->toolBox()->log()->notice('Test Producto->prefix() = ' . $product->prefix());
            $this->toolBox()->log()->notice('Test Producto->suffix() = ' . $product->suffix());
        };
    }

    public function createViewLogs()
    {
        return function($viewName = 'ListLogMessage') {
            $this->addView($viewName, 'LogMessage', 'log');
        };
    }
}
