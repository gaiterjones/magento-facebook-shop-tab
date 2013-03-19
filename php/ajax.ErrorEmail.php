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

if(!$_POST) exit();
//response array with status code and message
$_responseArray = array();

//validate the post data
$_successMessage='Thankyou your message has been received.';
$_errorMessage='';

// get POST variables
//$_postValues = array();
//foreach ( $_POST as $name => $value ) {
//	$_postValues[$name] = trim( $value );
//}

//extract( $postValues );

if(!empty($_POST['successmessage'])){$_successMessage=$_POST['successmessage'];}

//check the from field
if(empty($_POST['name'])){$_errorMessage='No name provided.';}

//check the message body
if(empty($_POST['message'])){$_errorMessage='No message body provided.';}

//check the email address
if(empty($_POST['email'])){$_errorMessage='No email address provided.';}
if(!isEmail($_POST['email'])){$_errorMessage='Email address is invalid.';}

// validate the request
if(md5($_POST['email']) != $_POST['md5']){$_errorMessage='Invalid request.';}

if (empty($_errorMessage))
{

	//send the email
	include 'class.Email.php';
	
	$_obj=new Email(array(
	  'to'  => $_POST['email'],
	  'from' => $_POST['name'],
	  'subject' => 'Error Report',
	  'body' => $_POST['message'],
	  'cc' => '',
	  'bcc' => '',
	));

	if ($_obj->get('emailsuccess'))
	{	
	//set the response
	$_responseArray['status'] = 'success';
	$_responseArray['message'] = $_successMessage;
	} else {
	$_responseArray['status'] = 'error';
	$_responseArray['message'] = 'An error occurred sending the email.';		
	}
	unset($_obj);

} else {
	$_responseArray['status'] = 'error';
	$_responseArray['message'] = $_errorMessage;
}

//send the response back
echo json_encode($_responseArray);
exit;

function isEmail($email) { // Email address verification, do not edit.

	return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$email));

}

?>