<?php
/**
 *  
 *  Copyright (C) 2012 paj@gaiterjones.com
 *
 *	This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  @category   PAJ
 *  @package    
 *  @license    http://www.gnu.org/licenses/ GNU General Public License
 * 	
 *
 */


	
/**
 * config class.
 -- application config
 */
class config
{

	// specify the full path to your configuration file here
		
	const userConfigurationFile='/root/Dropbox/paj/www/dev/facebook/magentotab/config/devConfig.ini';
	
	//







	const demo = false;
	const appIcon = 'magentologoicon.png';
	const demoText ='';
	const encryptionKey = 'myEncRYPti0nKeZ2012';
	const showAbout=true;
	
	public $_serverURL;
	public $_serverPath;
	
	public function __construct()
	{

		$this->_serverURL=$this->serverURL();
		$this->_serverPath=$this->serverPath();
		$this->loadUserConfiguration();
	}
	
	protected function loadUserConfiguration()
	{
		
		$_userConfigFile=self::userConfigurationFile;
		
		if (file_exists($_userConfigFile))
		{
		   $_settings=parse_ini_file($_userConfigFile,'application_settings');
		} else {
		    die('The requested user configuration file - '. $_userConfigFile. ' does not exist. Please check your configuration file and settings.');
		}
		
		$_settings=$_settings['application_settings'];
		
		foreach ($_settings as $_setting => $_value)
		{
			if ($_value=='true')
			{
				$this->set($_setting,true);
			} else if ($_value=='false') {
				$this->set($_setting,false);
			} else {
				$this->set($_setting,$_value);
			}
		}

	}
	
    public function get($variable) {
	
	    $constant = 'self::'. $variable;
	    
	    // get constant if defines
	    if(defined($constant)) {
	    
	        return constant($constant);
	    
	    } else {
	    
	    	// get array variable
	    	if(isset($this->__[$variable])) {
	        	return $this->__[$variable];
	        } else {
		        return false;
	        }
	        
	    }
	}
	
	public function set($key,$value)
	{
	    $this->__[$key] = $value;
	}

	/**
	 * serverURL function.
	 * 
	 * @access public
	 * @return string
	 */
	public function serverURL() {
	 $_serverURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$_serverURL .= "s";}
	 $_serverURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $_serverURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	 } else {
	  $_serverURL .= $_SERVER["SERVER_NAME"];
	 }
	 return $_serverURL;
	}
	
	private function serverPath() {
	 $_serverPath=$_SERVER["REQUEST_URI"];
	 //$_serverPath=explode('?',$_serverPath);
	 //$_serverPath=$_serverPath[0];
	 
	 return $_serverPath;
	}
	
}

//function autoloader($class) {
//	require_once 'php/class.' . $class . '.php';
//}

//spl_autoload_register('autoloader');
?>