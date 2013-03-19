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
 * HTMLMainPage class.
 * -- generate the main page html
 * @extends HTML
 */
class HTMLMainPage extends HTML {


	public function __construct($_variables) {
	
		parent::__construct($_variables);

		// class variables
		$_constants=array(
		  	"fbURL" => $this->__config->get('fbURL'),
		  	"bannerimage" => $this->__config->get('bannerImage'),
		  	"appIcon" => $this->__config->get('appIcon'),
		  	"demo" => $this->__config->get('demo'),
		  	"serverURL" => $this->__config->_serverURL. $this->__config->_serverPath,
		  	"allowStandAlone" => $this->__config->get('allowStandAlone')
		);
		
		// load class variables
		if(is_array($_constants)) {
			foreach ($_constants as $key => $value)
			{
				$this->set($key,$value);
			}
		}	
		
		// handle error state
		$_errorMessage=$this->get('errorMessage');
		
		if (!empty($_errorMessage)) {
			$this->set('pagetitle',$this->__t->__('An error has been detected'));
			$this->set('pagedescription',$this->__config->get('siteTitle'));
		}
		
		
		$this->createHTML();
		
		$_HTMLArray=$this->get('html');
		
		
		/* render html from html array */
		foreach ($_HTMLArray as $_obj)
		{
			$_usePageHtml=false;
			
			foreach ($_obj as $_key=>$_value)
			{
				
				if ($_key === 'page')
				{				
					$_array=$_value;
					foreach ($_array as $_key=>$_page)
					{
						// render default html
						if ($_page == '*')	{$_usePageHtml=true;}
					
						
						if (empty($_errorMessage))
						{
							// no errors
							
							
							if (!$this->get('allowStandAlone'))
							{
								if ($_page == 'redirector')	{$_usePageHtml=true; }
							}
							
													
							// check like status
							if ($this->get('pagelikerequired') && !$this->get('likestatus'))
							{
								if ($_page == 'htmllikeprompt') {$_usePageHtml=true; }
							} else {
							// display product html
								if ($_page == 'htmlproduct') {$_usePageHtml=true; }
							}							
														
						
						} else {
						
							// display error html
							
							if ($_page == 'error')	{$_usePageHtml=true; }
							
						}
						

					
					
					}

				}
				
				if ($_key === 'html')
				{
					if ($_usePageHtml)
					{
						$_pageHtml=$_pageHtml.$_value;
					}
				}

			}

		}
		
		$this->set('pageHtml',$_pageHtml);

	}

	// return page
	public function __toString()
	  {
		$html=$this->get('pageHtml');
				return $html;
	  }

protected function createHTML()
{
$_HTML[] = array
	(
    'page' => array
    	(
	    	'*',
	    ),
    'html' => '
<!DOCTYPE html>
<!-- This document was successfully checked as HTML5! -->
<html>
<head>
<title>'. $this->get('pageTitle'). '</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta property="og:site_name" content="'. $this->get('pageTitle'). '"/> 
<meta property="og:type" content="website"/> 
<meta property="og:url" content="'. htmlspecialchars($this->get('appURL')). '"/> 
<meta property="og:description" content="'. htmlspecialchars($this->get('pageDescription')). '"/> 
<meta property="og:title" content="'. htmlspecialchars($this->get('pageTitle')). '"/> 
<meta property="og:image" content="'. $this->get('serverURL'). 'images/'. $this->get('appIcon'). '"/> 
<meta property="fb:app_id" content="'. $this->__config->get('fbAppID') .'" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="js/poshytip/src/tip-twitter/tip-twitter.css" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
<script src="js/fancybox/jquery.fancybox.js?v=2.0.6"></script>
<script src="js/poshytip/src/jquery.poshytip.js"></script>
<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox.css?v=2.0.6" media="screen" />
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {

$(\'.fancybox\').fancybox({
	    \'fitToView\'     : false,
	    \'centerOnScroll\' : true,
	    \'transitionIn\' : \'elastic\',
	    \'transitionOut\' : \'elastic\',
        \'fixed\'         : true,
        helpers:  {
        overlay : null,
        }
});
	
$(".poshytip").poshytip({
	className: \'tip-twitter\',
	showTimeout: 1,
	alignTo: \'target\',
	alignX: \'center\',
	offsetY: 5,
	allowTipHover: false,
	fade: false,
	slide: false
});

$(\'#nav li\').hover(function () {
	$(this).find(\'.dropDown\').slideToggle(200);
	$(this).toggleClass(\'active\');
});

window.fbAsyncInit = function() {
FB.Canvas.setAutoGrow();
}

function sizeChangeCallback() {
FB.Canvas.setSize({ width: 810, height: 1400 });
}
'
);


// redirect to facebook tab when standalone disabled
$_HTML[] = array
	(
    'page' => array
    	(
	    	'redirector',
	    ),
    'html' => '
  function NotInFacebookFrame() {
    return top === self;
  }
  function ReferrerIsFacebookApp() {
    if(document.referrer) {
      return document.referrer.indexOf("apps.facebook.com") != -1;
    }
    return false;
  }
  if (NotInFacebookFrame() || ReferrerIsFacebookApp()) {
    top.location.replace("'. $this->get('fbURL'). '");
  }'
);


$_HTML[] = array
	(
    'page' => array
    	(
	    	'*',
	    ),
    'html' => '
});
//]]>
</script>
<!--[if IE]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body>
<!-- START Wrapper -->
<div id="wrapper">
	<!-- START Header -->
	<div id="header" style="background-image:url(\'css/images/'. $this->get('bannerimage'). '\');">
	<!-- END Header -->
	</div>
	<!-- START Content 1 -->
	<div id="contentContainer1">
'
);
		

// display product html
$_HTML[] = array
	(
    'page' => array
    	(
	    	'htmlproduct',
	    ),
    'html' => $this->get('producthtml')
    );
				

// html to display like prompt
$_HTML[] = array
	(
    'page' => array
    	(
	    	'htmllikeprompt',
	    ),
    'html' => '
	<div class="fberrorbox" style="width: 758px;">
		<div class="fbbody textNorm center">'.
		$this->__t->__('Please like this page to view the Shop!').
		'</div>
	</div>'
);

								
								
// html to create app connector
$_HTML[] = array
	(
    'page' => array
    	(
	    	'htmlappconnect',
	    ),
	'html' => '
	<div class="fberrorbox" style="width: 758px;">
	Please login...
	<script> top.location.href=\'' . $this->get('facebookApploginUrl'). '\'</script>
	</div>
	<br />
	'
	);
	

// html to create error
$_HTML[] = array
	(
    'page' => array
    	(
	    	'error',
	    ),
	'html' => '
	<div class="fberrorbox center" style="width: 758px;">
	'. $this->get('errorMessage'). '
	</div>
	<br />
	'
	);	


// footer
$_HTML[] = array
	(
    'page' => array
    	(
	    	'*',
	    ),
	'html' => '
	<!-- END Content Container1 -->
	</div>
	<!-- START Footer -->
			<div id="footer">'
);


// footer
$_HTML[] = array
	(
    'page' => array
    	(
	    	'footerdemo',
	    ),
	'html' => '
						<div class="fbcontentdivider"></div>				
						<div class="fbbody fbinfobox" style="width: 758px;">'.
							$this->__['demoText'].
						'</div>
						<div class="fbcontentdivider"></div>
					'
			);

				
				

	

// footer
$_HTML[] = array
	(
    'page' => array
    	(
	    	'*',
	    ),
	'html' => '			
	<!-- END Footer -->
		</div>
<!-- END Wrapper-->
</div>
<div id="fb-root"></div>
<script src="https://connect.facebook.net/en_US/all.js"></script>
<script>
FB.init({
appId  : \''. $this->__config->get('fbAppID') .'\',
status : true,
cookie : true,
xfbml : true
});

window.fbAsyncInit = function() {
FB.Canvas.setAutoGrow();
}
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId='. $this->__config->get('fbAppID') .'";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));
</script>
</body>
</html>
');


$this->set('html',$_HTML);
	
}


}