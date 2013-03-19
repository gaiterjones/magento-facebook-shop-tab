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
 * HTMLContactForm class.
 * -- generates html for a contact form
 * @extends HTML
 */
class HTMLContactForm extends HTML {
	
	public function __construct($_variables) {
	
	parent::__construct($_variables);

		$this->createContactFormHTML();
		
	}
	
	public function __toString()
	{
		$html=$this->get('html');
				return $html;
	}
	
	protected function createContactFormHTML()
	{
	$_html='
	<div id="contactformhtml" style="display: none;">
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
	<script src="js/contactform/jquery.jigowatt.js"></script>
	<!-- AJAX Form Submit -->	
	<section id="contact">

		<header>

			<h1>'.$this->__t->__('Contact Form'). '</h1>
			<p></p>

		</header>

		<mark id="message"></mark>

		<form method="post" action="php/contactform/contact.php" name="contactform" id="contactform" autocomplete="on">

			<fieldset>

				<legend>Contact Details</legend>

				<div>
					<label for="name" accesskey="U">'.$this->__t->__('Your Name'). ' </label>
					<input name="name" type="text" id="name" placeholder="'.$this->__t->__('Enter your name'). '" required="required" />
				</div>
				<div>
					<label for="email" accesskey="E">'.$this->__t->__('Email'). ' </label>
					<input name="email" type="email" id="email" placeholder="'.$this->__t->__('Enter your email address'). '" pattern="^[A-Za-z0-9](([_\.\-]?[a-zA-Z0-9]+)*)@([A-Za-z0-9]+)(([\.\-]?[a-zA-Z0-9]+)*)\.([A-Za-z]{2,})$" required="required" />
				</div>

				<div>
					<label for="phone" accesskey="P">'.$this->__t->__('Phone'). ' <small>(optional)</small></label>
					<input name="phone" type="tel" id="phone" size="30" placeholder="'.$this->__t->__('Enter your phone number'). '" />
				</div>

				<div>
					<label for="website" accesskey="W">'.$this->__t->__('Website'). ' <small>(optional)</small></label>
					<input name="website" type="url" id="website" placeholder="'.$this->__t->__('Enter your website address'). '" />
				</div>

			</fieldset>

			<fieldset>

				<legend>Your Comments</legend>

				<div>
					<label for="subject" accesskey="S">Subject</label>
					<select name="subject" id="subject" required="required">
						<option value=""></option>
						<option value="Support">'.$this->__t->__('Support'). '</option>
						<option value="A Sale">'.$this->__t->__('Sales'). '</option>
						<option value="A Bug fix">'.$this->__t->__('Report a bug'). '</option>
					</select>
				</div>

				<div>
					<label for="comments" accesskey="C">'.$this->__t->__('Comments'). '</label>
					<textarea name="comments" cols="40" rows="3" id="comments" placeholder="'.$this->__t->__('Enter your comments'). 's" spellcheck="true" required="required"></textarea>
				</div>

			</fieldset>

			<fieldset>

				<legend>'.$this->__t->__('Are you human?'). '</legend>

				<label for="verify" accesskey="V" class="verify"><img src="php/contactform/image.php" alt="Verification code" /></label>
				<input name="verify" type="text" id="verify" size="6" required="required" style="width: 50px;" title="This confirms you are a human user and not a spam-bot." />

			</fieldset>

			<input type="submit" class="submit" id="submit" value="'.$this->__t->__('Submit'). '" />

		</form>

	</section>
	</div>
	';	
	
	$this->set('html',$_html);
	
	}
	
	
}