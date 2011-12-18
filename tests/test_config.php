<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_Nmap Parse Test
 *
 * PHP version 5
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330,Boston,MA 02111-1307 USA
 *
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2008 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://www.ortro.net
 */

//The values used in tests for the $option array 
//define('OUTPUT_FILE', 'C:\testNmapSaveOutputFile1.xml');
//define('NMAP_BINARY', 'C:\Programmi\Nmap\nmap.EXE');
$nmap = exec('which nmap');

if ($nmap) {
    define('NMAP_BINARY', $nmap);
}

define('OUTPUT_FILE', sys_get_temp_dir() . '/testNmapSaveOutputFile1.xml');

$nmap_options = array('os_detection' => true,
                      'service_info' => true,
                      'port_ranges' => 'U:53,111,137,T:21-25,80,139,8080',
                      );
