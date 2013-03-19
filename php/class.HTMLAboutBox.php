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
 * HTMLAboutBox class
 * -- generates html for an about box
 * @extends HTML
 */
class HTMLAboutBox extends HTML {
	
	public function __construct($_variables) {
	
	parent::__construct($_variables);
		
		$this->createAboutBoxHTML();
		
	}
	
	protected function createAboutBoxHTML()
	{
	$_html='
	    <div id="about" style="display: none;">
	    <script>
			function closeAboutBox() {
			    $.fancybox.close(); 
			}
		</script>
	    <form>
		    <fieldset>
		    <legend>'. $this->get('pageTitle'). '</legend>
		    	<div class="aboutimage">
		    		<img alt="" title="Icon" width="120" height="120" src="images/pjlogo.png">
		    	</div>
		    	<div>
		    		<p>'.
		    			$this->get('pageDescription').
		    		'</p>
				    <p>
				    	Version '. $this->get('version').'
				    </p>
				    <p>
				    	 '. (file_exists('./images/flagIcons/'. $this->get('languagecode'). '.png') ?
				    	  '<img title="Detected language - '. $this->get('languagecode'). '" style="padding-right: 5px;" src="images/flagIcons/'. $this->get('languagecode'). '.png" >' : '').'
				    	 &copy;'. date('Y'). ' PAJ</a>
				    </p>
				    <p style="clear:both; width:100%;" class="center">
				    	<input type="button" onclick="closeAboutBox()" value="OK" />
				    </p>
			    </div>
		    </fieldset>
	    </form>
	    </div>
	';	
	
	$this->set('html',$_html);
	
	}
	
	public function __toString()
	{
		$html=$this->get('html');
				return $html;
	}
	
	
}