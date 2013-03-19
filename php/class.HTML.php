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

class HTML{
	
	protected $__;
	protected $__t; // protected translation class object
	protected $__config;
	
		public function __construct($_variables) {
		
			// load class variables
			foreach ($_variables as $key => $value)
			{
				$this->set($key,$value);
			}
			

			// load app variables
			$this->__config= new config();
			
			// load app translator
			
			$_languageCode=$this->get('languagecode');
			
			if (empty($_languageCode)) { $_languageCode='en';}
			
			$this->__t=new Translator($_languageCode);
	
	
		}
		
	   function __destruct() {
	       
	       unset($this->__config);
	       unset($this->__);
	       unset($this->__t);
	       
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