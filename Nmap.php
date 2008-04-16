<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Simple wrapper interface for the Nmap utility.
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
 * @category  Net
 * @package   Net_Nmap
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2008 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_Nmap
 */

require_once 'System.php';
require_once 'Nmap/Parser.php';
require_once 'Nmap/Exception.php';

/**
 * Simple wrapper interface for the Nmap utility.
 *
 * @category  Net
 * @package   Net_Nmap
 * @author    Luca Corbo <lucor@ortro.net>
 * @copyright 2008 Luca Corbo
 * @license   GNU/LGPL v2.1
 * @link      http://pear.php.net/packages/Net_Nmap
 */
class Net_Nmap
{
    
    /**
     * Location of the nmap binary
     *
     * @var string  $nmap_path
     * @see Net_Namp::__construct()
     * @access private
     */
    private $_nmap_binary;
    
    /**
     * Absolute path to store the Nmap XML output file.
     *
     * @var string
     * @see Net_Namp::__construct()
     * @access private
     */
    private $_output_file = null;

    /**
     * Force the OS detection
     *
     * @var boolean
     * @see Net_Namp::__construct()
     * @access private
     */
    private $_os_detection = false;
    
    /**
     * Delete Nmap output file after parsing
     *
     * @var    string
     * @access private
     */
    private $_delete_output_file = true;
    
    /**
     * The hostname/IP failed to resolve 
     *
     * @var    array
     * @access private
     */
    private $_failed_to_resolve = array();
    
    /**
     * Creates a new Nmap object
     *
     * Available options are:
     * 
     * - string  nmap_binary:  The location of the Nmap binary. 
     *                         If not specified, defaults to '/usr/bin/nmap'.
     *
     * - string  output_file:  Path to store the Nmap XML output file.
     *                         If not specified, a temporary file is created.
     * 
     * - boolean os_detection: Force the OS detection, requires root privileges. 
     *                         If not specified, defaults to false.
     *
     * @param array $options optional. An array of options used to create the
     *                       Nmap object. All options must be optional and are
     *                       represented as key-value pairs.
     * 
     * @access public
     */
    public function __construct(array $options = array())
    {
        if (array_key_exists('nmap_binary', $options)) {
            $this->_nmap_binary = (string)$options['nmap_binary'];
        } else {
            $this->_nmap_binary = System::which('nmap');
        }

        if (array_key_exists('output_file', $options)) {
            $this->_output_file = (string)$options['output_file'];
        }
        
        if (array_key_exists('os_detection', $options)) {
            $this->_os_detection = (boolean)$options['os_detection'];
        }
    }
    
    /**
     * Prepare the command to execute 
     *
     * @param array $targets contains hostnames, IP addresses, networks to scan
     * 
     * @return string 
     * @access private
     */
    private function _createCommandLine($targets)
    {
        $default_option = ' -A ';

        if ($this->_output_file === null) {
             $this->_output_file = tempnam(System::tmpdir(), __CLASS__);
        } else {
            $this->_delete_output_file = false;
        }
        
        $cmd  = escapeshellarg($this->_nmap_binary);
        $cmd .= $default_option;
        if ($this->_os_detection) {
            $cmd .= ' -O ';
        }
        $cmd .= ' -oX ' . escapeshellarg($this->_output_file) . ' ';
        foreach ($targets as $target) {
            $cmd .= escapeshellarg($target) . ' ';
        }
        $cmd .= '2>&1';
        if (OS_WINDOWS) {
            $cmd = '"' . $cmd . '"';
        }
        return $cmd;
    }
    
    /**
     * Scan the specified target 
     *
     * @param array $targets Array contains hostnames, IP addresses, 
     *                       networks to scan
     * 
     * @return true | PEAR_Error
     * @throws Net_Nmap_Exception If Nmap binary does not exist or 
     *                            the command failed to execute.
     * @access public
     */
    public function scan($targets)
    {        
        exec($this->_createCommandLine($targets), $out, $ret_var);

        if ($ret_var > 0) {
            throw new Net_Nmap_Exception(implode(' ', $out));
        } else {
            foreach ($out as $row) {
                preg_match('@^Failed to resolve given hostname/IP:\s+(.+)\.\s+Note@',
                           $row,
                           $matches);
                if (count($matches) > 0) {
                    $this->_failed_to_resolve[] = $matches[1];
                }
            }
            return true;
        }
    }
    
    /**
     * Get all the discovered hosts
     * 
     * @param string $output_file Absolute path of the file to parse (optional)
     *
     * @return ArrayIterator      Returns Hosts Object on success. 
     * @throws Net_Nmap_Exception If a parsing error occurred. 
     * @access public
     */
    public function parseXMLOutput($output_file = null)
    {
        if ($output_file === null) {
            $output_file = $this->_output_file;
        } else {
            $this->_delete_output_file = false;
        }
        $parse = new Net_Nmap_Parser();
        $parse->setInputFile($output_file);
        $parse->folding = false;
        
        $res = $parse->parse();
        if (PEAR::isError($res)) {
            throw new Net_Nmap_Exception($res);
        }
        if ($this->_delete_output_file) {
            unlink($this->_output_file);
        }
        return $parse->getHosts();
    }
    
    /**
     * Get all the hostnames/IPs failed to resolve during scanning operation
     * 
     * @return Array    Returns array
     * @access public
     */
    public function getFailedToResolveHosts()
    {
        return $this->_failed_to_resolve;
    }
}
?>
