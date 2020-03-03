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
namespace FacturaScripts\Plugins\SamplePlugin\Extension\Model;

/**
 * Description of Producto
 *
 * @author Carlos Garcia Gomez <carlos@facturascripts.com>
 */
class Producto
{

    /**
     * Adds prefix() method to the model.
     */
    public function prefix()
    {
        return function($prefix = 'PRE-') {
            $this->referencia = $prefix . $this->referencia;
            return $this->referencia;
        };
    }

    public function save()
    {
        return function() {
            $this->toolBox()->log()->notice('This code is executed after save() method is called.');
        };
    }

    public function saveBefore()
    {
        return function() {
            $this->toolBox()->log()->notice('This code is executed before save() method is called.'
                . ' And return false can stop save().');
        };
    }

    /**
     * Adds suffix() method to the model.
     */
    public function suffix()
    {
        return function($suffix = '-SUF') {
            return $this->referencia . $suffix;
        };
    }
}
