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
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Net/Nmap.php';

class Bug16336Test extends PHPUnit_Framework_TestCase
{

    private $_hosts;
    private $_host;
    
    public function setUp()
    {
        $nmap = new Net_Nmap();
        $this->_hosts = $nmap->parseXMLOutput(dirname(__FILE__) . '/bug16336.xml');
        $this->_host   = $this->_hosts[0];
    }

    public function tearDown()
    {
        unset($this->_host);
        unset($this->_hosts);
    }

    public function testOS()
    {
        $this->assertEquals('Too many fingerprints match this host to give specific OS details', $this->_host->getOS());
    }
}
?>
