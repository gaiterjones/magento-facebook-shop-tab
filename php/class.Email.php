<?php
/**
 *  
 *  Copyright (C) 2011 paj@gaiterjones.com
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

	$_obj=new Email(array(
	  'to'  => TO,
	  'from' => FROM,
	  'subject' => SUBJECT,
	  'body' => BODY txt/html,
	  'cc' => CC,
	  'bcc' => BCC,
	));
	
*/

/**
 * Email class.
 -- a class to send MyMedazzaland email
 */
class Email {


	protected $__;
	protected $_headers;
		
	public function __construct($_email) {
	
		$this->loadEmail($_email);
		$this->sendEmail();
	}
	
	protected function loadEmail($_variables)
	{
		foreach ($_variables as $key => $value)
		{
			$this->set($key,$value);
		}
	}


    protected function sendEmail()
    {
		
		$_cc=$this->get('cc');
		$_bcc=$this->get('bcc');
		
		$_fqdnHostname= $_SERVER['SERVER_NAME'];
		$_messageID="<" . sha1(microtime()) . "@" . $_fqdnHostname . ">";
		$this->addHeader('From: '.$this->get('from')."\r\n");
		if (!empty($_cc)) {$this->addHeader('CC: '.$this->get('cc')."\r\n");}
		if (!empty($_bcc)) {$this->addHeader('BCC: '.$this->get('bcc')."\r\n");}
        $this->addHeader('Reply-To: '.$this->get('from')."\r\n");
	    $this->addHeader("Content-Type:text/html; charset=\"iso-8859-1\"\r\n");		
        $this->addHeader('Return-Path: '.$this->get('from')."\r\n");
		$this->addHeader("Message-ID: " .$_messageID. "\r\n");
		$this->addHeader('X-mailer: MyMedazzaland 1.0'."\r\n");
		
		if (mail($this->get('to'),$this->get('subject'),$this->get('body'),$this->_headers)) 
		{
			$this->set('emailsuccess',true);
		} else {
			$this->set('emailsuccess',false);
		}
        
    }

    protected function addHeader($header){
        $this->_headers .= $header;
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