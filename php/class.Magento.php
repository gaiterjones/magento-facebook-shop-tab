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

class Magento
{

	protected $__config;
	protected $__;
	
	public function __construct() {
		
			$this->loadConfig();
			$this->loadMagento();

	}
	
	
	// get app config
	private function loadConfig()
	{
		$this->__config= new config();
	}


	// connect to Magento
	private function loadMagento()
	{
		require_once $this->__config->get('PATH_TO_MAGENTO_INSTALLATION'). 'app/Mage.php';
		umask(0);
		Mage::app()->loadArea(Mage_Core_Model_App_Area::AREA_FRONTEND);
		$baseUrlMedia = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);
		$this->set('baseurlmedia',$baseUrlMedia);
	}
	
	
	public function set($key,$value)
	{
		$this->__[$key] = $value;
	}

	public function get($variable)
	{
		return $this->__[$variable];
	}

}  
?>