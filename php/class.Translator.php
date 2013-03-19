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
 
class Translator {

    protected $__;
    private $lang = array();

    public function __construct($_languageCode) {
    
    	$this->set('languageCode',$_languageCode);
    
    }

    
    private function findString($str,$lang) {
        if (array_key_exists($str, $this->lang[$lang])) {
            return $this->lang[$lang][$str];
        }
        return $str;
    }
    
    private function splitStrings($str) {
        return explode('=',trim($str));
    }
    
    public function __($str) {
    
    	try {
	    	$lang=$this->get('languageCode');
	    	
	    	$_localeFile= dirname(__FILE__). '/locale/'. $lang.'.txt';
	    	
	        if (!array_key_exists($lang, $this->lang)) {
	            if (file_exists($_localeFile)) {
	                $strings = array_map(array($this,'splitStrings'),file($_localeFile));
	                foreach ($strings as $k => $v) {
	                    $this->lang[$lang][$v[0]] = $v[1];
	                }
	                return $this->findString($str, $lang);
	            }
	            else {
	                return $str;
	            }
	        }
	        else {
	            return $this->findString($str, $lang);
	        }
	        
	    } catch (Exception $e) {

		    // catch translation errors quietly, just
		    // return the original string and pretend
		    // nothing happened...
		    return $str;
		}
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