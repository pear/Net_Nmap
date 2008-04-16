<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_Nmap Example
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

/**
 * Parse a Nmap XML Output file to retrieve Hosts Object
 */

require_once 'Net/Nmap.php';

try {
    $nmap = new Net_Nmap();

    //Parse XML Output to retrieve Hosts Object
    $hosts = $nmap->parseXMLOutput('example2.xml');

    //Print results
    foreach ($hosts as $key => $host) {
        echo 'Hostname: ' . $host->getHostname() . "\n";
        echo 'Address: ' . $host->getAddress() . "\n";
        echo 'OS: ' . $host->getOS() . "\n";
        echo 'Status: ' . $host->getStatus . "\n";
        $services = $host->getServices();
        echo 'Number of discovered services: ' . count($services) . "\n";
        foreach ($services as $key => $service) {
            echo "\n";
            echo 'Service Name: ' . $service->name . "\n";
            echo 'Port: ' . $service->port . "\n";
            echo 'Protocol: ' . $service->protocol . "\n";
            echo 'Product information: ' . $service->product . "\n";
            echo 'Product version: ' . $service->version . "\n";
            echo 'Product additional info: ' . $service->extrainfo . "\n";
        }
    }
} catch (Net_Nmap_Exception $ne) {
    echo $ne->getMessage();
}
?>
