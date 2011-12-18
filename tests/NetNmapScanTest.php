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
require_once 'test_config.php';

class NetNmapScanTest extends PHPUnit_Framework_TestCase
{

    private $_target;
    
    public function setUp()
    {
        if (!defined('NMAP_BINARY')) {
            $this->markTestSkipped("NMAP_BINARY is not defined - check your test_config");
        }
        $this->_target = array('127.0.0.1');
    }

    public function tearDown()
    {
        unset($this->_target);
    }

    public function testNmapBinaryInPath()
    {
        $nmap = new Net_Nmap();
        $nmap->enableOptions($GLOBALS['nmap_options']);
        $res = $nmap->scan($this->_target);
        $this->assertEquals(true, $res);
    }
    
    public function testNmapBinaryCustomPath()
    {
        $options = array('nmap_binary' => NMAP_BINARY);
        $nmap = new Net_Nmap($options);
        $res = $nmap->scan($this->_target);
        $this->assertEquals(true, $res);
    }
    
    public function testNmapSaveOutputFile()
    {
        $options = array('output_file' => OUTPUT_FILE);
        $nmap = new Net_Nmap($options);
        $res = $nmap->scan($this->_target);
        $this->assertEquals(true, $res);
        $this->assertEquals(true, is_file(OUTPUT_FILE));
        //unlink(OUTPUT_FILE);
    }
}
