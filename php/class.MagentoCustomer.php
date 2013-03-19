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

class MagentoCustomer extends Magento {

	public function __construct() {
		parent::__construct();

	}
	
	
	public function createCustomerAccount($_firstName,$_lastName,$_email,$_password,$_storeID=1)
	{


	//$_firstName = "John";
	//$_lastName = "Smith";
	//$_email = "johnsmith@localhost.local";
	//$_password = "myverysecretpassword";
	
	// Website and Store details
	$websiteId = Mage::app()->getWebsite()->getId();
	$store = Mage::app()->getStore();
	
	$customer = Mage::getModel("customer/customer");
	$customer->website_id = $websiteId;
	$customer->setStore($store);
	
		try {
			// If new, save customer information
			$customer->firstname = $_firstName;
			$customer->lastname = $_lastName;
			$customer->email = $_email;
			$customer->password_hash = md5($_password);
			if($customer->save()){
				echo $customer->firstname." ".$customer->lastname." information is saved!";
			}else{
				echo "An error occured while saving customer";
			}
		}catch(Exception $e){
			// If customer already exists, initiate login
			if(preg_match('/Customer email already exists/', $e)){
				$customer->loadByEmail($_email);
		
				$session = Mage::getSingleton('customer/session');
				$session->login($_email, $_password);
		
				//echo $session->isLoggedIn() ? $session->getCustomer()->getName().' is online!' : 'not logged in';
			}
		}
	}
}