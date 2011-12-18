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

class NetNmapParseTest extends PHPUnit_Framework_TestCase
{

    private $_hosts;
    private $_host;
    private $_services;
    
    public function setUp()
    {
        $nmap = new Net_Nmap();
        $this->_hosts = $nmap->parseXMLOutput(dirname(__FILE__) . '/NetNmapParseTest.xml');
        $this->_host   = $this->_hosts[0];
        $this->_services = $this->_host->getServices();
    }

    public function tearDown()
    {
        unset($this->_host);
        unset($this->_services);
        unset($this->_hosts);
    }

    public function testHostsCount()
    {
    	$this->assertEquals(1, count($this->_hosts));
    }
    
    public function testStatus()
    {
        $this->assertEquals('up', $this->_host->getStatus());
    }
    
    public function testAddess()
    {
        $this->assertEquals('127.0.0.1', $this->_host->getAddress());
    }
    
    public function testHostname()
    {
        $this->assertEquals('localhost', $this->_host->getHostname());
    }
    
    public function testAllOS()
    {
        $this->assertEquals(2, count($this->_host->getAllOS()));
    }
    
    public function testOS()
    {
        $this->assertEquals('Linux 2.6.22 - 2.6.23', $this->_host->getOS());
    }
    
    public function testServicesCount()
    {
        $this->assertEquals(3, count($this->_services));
    }
    
    public function testServiceInfo()
    {
        $service = $this->_services[0];

        $this->assertEquals('OpenSSH', $service->product);
        $this->assertEquals('tcp', $service->protocol);
        $this->assertEquals('22', $service->port);
        $this->assertEquals('ssh', $service->name);
        $this->assertEquals('4.6p1 Debian 5ubuntu0.1', $service->version);
        $this->assertEquals('protocol 2.0', $service->extrainfo);
    }
}
