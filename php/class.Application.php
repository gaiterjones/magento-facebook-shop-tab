<?php
/**
 *  Facebook Magento Storefront Tab Application
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
 
 	usage
 	?storeid= - select store id
 	?fbtab=true - use for fbtab install link
 	?collection= - collection type
 	?p= - page number
 	?category= - category id
 	
 	0.91 - modified user configuration code to allow user config in ini file.
 	0.92 - changed fancybox settings 10.11.2012
 	0.95 - translation additions, html header changes
 	0.97 - css changes, bug fixes 12.11.2012
 	1.00 - first publie release
 	1.01 - css bug blocked buy button
	
 *
 *  @category   PAJ
 *  @package    
 *  @license    http://www.gnu.org/licenses/ GNU General Public License
 * 	
 */


/**
 * Application class.
 */
class Application
{
	
	protected $__; // class variables protected array
	protected $__t; // protected translation class object
	protected $__config; // application config protected array
	
	
	/**
	 * __construct function.
	 * -- application constructor
	 * @access public
	 * @return void
	 */
	public function __construct() {
		
		try
		{
			if (!isset($_SESSION)) session_start();
			
			$this->set('errorMessage','');
			$this->loadConfig();
			$this->getExternalVariables();
			$this->__t=new Translator($this->get('languagecode'));
			$this->getProductCollection($this->get('collectiontype'));
			$this->getProducts();
			$this->createProductHTML();
			$this->createPage();
			
			session_write_close();
			

		}
		catch (Exception $e)
	    {
	    	$_error=$e->getMessage();
	    	$_reportEmail='apps@gaiterjones.com';
	    	$_userConfigXMLFile=$this->__config->get('userConfigurationFile');
	    	
	    	if (empty($_error)) {$_error='Undefined see trace > ';}
	    	
	    	$_userConfigSettings=parse_ini_file($_userConfigXMLFile,'application_settings');
	    	$_userConfigSettings=$_userConfigSettings['application_settings'];
	    	$_userConfigHTML='<ol>';
	    	foreach ($_userConfigSettings as $_userConfigSetting => $_value)
	    	{ 
	    		$_userConfigHTML=$_userConfigHTML. '<li>'.htmlspecialchars($_userConfigSetting). ' - '.  htmlspecialchars($_value).'</li>';
	    	}
	    	$_userConfigHTML=$_userConfigHTML. '</ol>';
	    	
	    	$this->set('errorMessage', 'An error has occurred : '. $_error. ' <a class="fancybox" href="#errorReport">!</a>
	    	<script src="js/ajax.erroremail.js"></script>
	    	<div id="formResponse"><img id="ajaxLoader" width="32" height="32" class="hidden" src="css/images/ajax-loader.gif" ></div>
	    	<form id="errorReportForm" style="margin: 0 auto; width: 300px;" method="post">
		    	<input name="name" type="hidden" value="Facebook Magento Tab Store App <noreply@gaiterjones.com>" />
		    	<input name="md5" type="hidden" value="'. md5($_reportEmail). '" />
		    	<input name="successmessage" type="hidden" value="Your error submission report has been received." />
		    	<input name="email" type="hidden" value="'. $_reportEmail. '" />
		    	<input name="message" type="hidden" value="<h1>Error Report</h1><ol><li>URL - '.
		    	 $this->curPageURL(). '</li><li>Error - '. $e->getMessage(). '</li><li>Trace:<pre>'. $e->getTraceAsString(). '</pre></li><li>User Configuration - '. $_userConfigHTML. '</li></ol>" />
			</form>
			<div id="submit">Click <a id="submitForm" href="#">here</a> to report this error.</div>
			<div id="errorReport" style="display: none;">Error trace (if available) - <pre>'. $e->getTraceAsString(). '</pre></div>
			');
	    	$this->createPage();
	    	exit;
	    }
	}
	
	public function __destruct()
	{
			// cleanup
			unset($this->__config);
			unset($this->__t);
	}
	
	/**
	 * loadConfig function.
	 * -- load application configuration
	 * @access private
	 * @return void
	 */
	private function loadConfig()
	{
		$this->__config= new config();
		
		
		$_version='BETA v1.0.0-12.11.2012';
		$_versionNumber=explode('-',$_version);
		$_versionNumber=$_versionNumber[0];
		
		
		// class variables
		$this->set('productlimit',$this->__config->get('productLimit'));
		$this->set('useshortdescription',$this->__config->get('useShortDescription'));
		$this->set('uselongdescription',$this->__config->get('useLongDescription'));
		$this->set('googleutmtag',$this->__config->get('googleUTMTag'));
		$this->set('defaultcollectiontype',$this->__config->get('defaultCollectionType'));
		$this->set('showmenu',$this->__config->get('showMenu'));
		$this->set('showabout',$this->__config->get('showAbout'));
		$this->set('showcontact',$this->__config->get('showContact'));
		$this->set('version',$_version);
		$this->set('versionNumber',$_versionNumber);
		$this->set('pagelikerequired',$this->__config->get('pageLikeRequired'));
		$this->set('pageTitle',$this->__config->get('pageTitle'));
		$this->set('pageDescription',$this->__config->get('pageDescription'));
		$this->set('appIcon',$this->__config->get('appIcon'));
		
		// determine like status
		if($_SERVER['REQUEST_URI']===$this->__config->get('URIToApp') && $this->get('pagelikerequired'))
		{
			$this->getLikeStatus();
			$_SESSION['likestatus']=$this->get('likeStatus');
		}

	}
	
	
	/**
	 * getExternalVariables function.
	 * -- get external variables and set class variables
	 * @access private
	 * @return void
	 */
	private function getExternalVariables()
	{
		
		// initialise variables from GET	
		//
		
		// collection type
		if(isset($_GET['collection'])){ $_collectionType = $_GET['collection'];} else { $_collectionType=$this->get('defaultcollectiontype');}
		
		// collection page
		if(isset($_GET['p'])){ $_collectionPage = $_GET['p'];} else { $_collectionPage="1"; }
		
		// category
		if(isset($_GET['category'])){ $_categoryID = $_GET['category'];} else { $_categoryID='0'; }
		
		// store id from browser language
		if(isset($_GET['storeid']))
		{
			// set store manually
			$_storeID = $_GET['storeid'];
			$this->getLanguageFromStoreID($_storeID);
			
		} else {
			// set store based on browser language
			$_storeID=$this->getStoreLanguage($this->getBrowserLanguages());
		}
		
		// use to provide tab installation link ?fbtab=true
		if(isset($_GET['fbtab']))
			{ 
			$_serverPath=explode("?",$this->__config->_serverPath);
			$_fbTabLink='http://www.facebook.com/dialog/pagetab?app_id='. $this->__config->get('fbAppID'). '&next='.
			$this->__config->_serverURL. $_serverPath[0];
			 
			echo '<a href="'. $_fbTabLink. '">Click here to setup the facebook tab application.</a>';
			exit;
		}
		
		
		// store class variables
		$this->set('collectiontype',$_collectionType);
		$this->set('collectionpage',$_collectionPage);
		$this->set('categoryid',$_categoryID);
		$this->set('storeid',$_storeID);
				
	}
	
		/**
		 * getProductCollection function.
		 * -- gets Magento product collection
		 * @access private
		 * @param mixed $_collectionType
		 * @return void
		 */
		private function getProductCollection($_collectionType)
		{
			// -- Load Magento --
			$_obj=new MagentoCollection();
			$_storeID=$this->get('storeid');
			$_collectionPage=$this->get('collectionpage');
			$_categoryID=$this->get('categoryid');
			$_collectionLimit=$this->get('productlimit');
			
			switch ($_collectionType)
			{			
				case 'newfromdate':
				$_obj->getNewProducts($_storeID,(int)$_collectionPage,(int)$_collectionLimit);
				$this->set('selectedcollectionname',$this->__t->__('New Products'));
				break;
				
				case 'allproducts':
				$_obj->getAllProducts($_storeID,(int)$_collectionPage,(int)$_collectionLimit);
				$this->set('selectedcollectionname',$this->__t->__('All Products'));
				break;
				
				case 'categoryproducts':
				$_obj->getCategoryProducts($_storeID,(int)$_categoryID,(int)$_collectionPage,(int)$_collectionLimit);
				$this->set('selectedcollectionname',$this->__t->__('Category Products'));
				break;
				
				case 'bestselling':
				$_obj->getBestsellingProducts($_storeID);
				$this->set('selectedcollectionname',$this->__t->__('Bestselling Products'));
				break;	
				
				default:
				throw new Exception('Invalid collection type.');
			}
			
			// -- load Magento Product Collection
			$_collection=$_obj->get('collection');
			
			$_imageBaseURL=$_obj->get('baseurlmedia'). 'catalog/product';
	
			$this->set('imagebaseurl',$_imageBaseURL);
			$this->set('collection',$_collection);
			
			// load categories and category product count used for menu
			if ($this->get('showmenu'))
			{
				$this->set('categories',$_obj->get('categories'));
				$this->set('categoriesproductcount',$_obj->get('categoriesproductcount'));
			}
			
			// -- Unload Magento
			unset($_collection);
			unset($_obj);
		}
		
		/**
		 * getProducts function.
		 * -- extracts products from collection
		 * @access private
		 * @param mixed $_currentDate
		 * @return void
		 */
		private function getProducts()
		{
			// -- init function variables
			$_newProductCount=0;
			$_collection=$this->get('collection');
			$_collectionType=$this->get('collectiontype');
			$_filteredProduct=array();
			$_collectionSize=$_collection->getSize();
			
			// -- iterate magento product collection
			foreach ($_collection as $product) {
			
				// -- product variables
				$_sku = $product->getSku();
				$_name = $this->clean_up($product->getName());
				$_descriptionText=$product->getDescription();
				$_descriptionShortText=$product->getShortDescription();
				$_unitPrice = round($product->getPrice(),2);
				$_url=explode('?',$product->getProductUrl());
				$_url=$_url[0];
				$_id=$product->getId();
				$_imageURL=$this->get('imagebaseurl'). $product->getImage();
				$_productIsSaleable= $product->isSaleable();
								  
				if ($_productIsSaleable) {
	
				  		$_filteredProducts[$_id] = array 
						  (
						  "sku" => $_sku,
						  "name" => $_name,
						  "url" => $_url,
						  "unitPrice" => $_unitPrice,
						  "imageURL" => $_imageURL,
						  "description" => $_descriptionText,
						  "descriptionShort" => $_descriptionShortText
						  );
						  
				  	$_newProductCount++;
				}
	
			}
			
			// -- set class variables
			$this->set('filteredcollection',$_filteredProducts);
			$this->set('newproductcount',$_newProductCount);
			$this->set('collectionsize',$_collectionSize);
	
		}		

	
	/**
	 * createProductHTML function.
	 * -- renders main product and navigation html from product collection array
	 * @access private
	 * @return void
	 */
	private function createProductHTML()
	{
		$x=0;
		$_productCount=0;
		$_productLimit=$this->get('productlimit');
		$_collection=$this->get('filteredcollection');
		$_collectionItems = count($_collection);
		$_useLongDescription=$this->get('uselongdescription');
		$_useShortDescription=$this->get('useshortdescription');
		$_collectionPage=$this->get('collectionpage');
		$_collectionType=$this->get('collectiontype');
		
		$_categoryIDSelected=$this->get('categoryid');
		$_selectedCollection=$this->get('selectedcollectionname');

		$_productHTML='
		<!-- START Product Container -->
		<div id="productContainer">';
			    
		// iterate collection
		foreach ($_collection as $id=>$product) {
		
			$x++;
			$_productCount++;
			$_id=$id;
			$_sku = $product['sku'];
			$_name = $product['name'];
			$_longDescription = $product['description'];
			$_shortDescription = $product['descriptionShort'];
			$_url = $product['url'];
			$_imageURL = $product['imageURL'];
			
			if ($_productCount===1){$_productHTML=$_productHTML. '<div class="products">';}
		
		    $_productHTML=$_productHTML. '
		    <!-- START Product '. $_productCount. ' -->
		    <div class="product'. $x. '">
		    <!-- START Shadow Product '. $_productCount. ' -->
		     <div class="productboxshadow"> 
		     <!-- START Product Box -->
		      <div class="productbox">
		      		<!-- START Product Info -->
		      		<div class="productinfo">
				    	<p class="productname center">
				    		<a rel="productGroup1" class="fancybox" href="#'. $_id. '">'.
				    		$_name .'
				    	</a>
				    	</p>
				    	<p class="productimage center">
				    		<a rel="productGroup2" class="fancybox" href="#'. $_id. '">
				    			<img '.
				    			 (strlen($this->getFirstSentence($_longDescription)) > 10 ? 'class="poshytip" title="'. $this->getFirstSentence($_longDescription). '"' : '').
				    			  ' alt="'.$_name.'" height="128" width="128" src="'. $_imageURL. '">
				    		</a>
				    	</p>
				    <!-- END Product Info -->
			    	</div>
			    	<div class="fb-like-bottom">
			    	  <div class="fb-like-btn">
			    		<div class="fb-like" data-href="'. $_url. '" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
			    	  </div>
			    	</div>
			   <!-- END Product Box -->
			   </div>
		     <!-- END Shadow Product '. $_productCount. ' -->
		     </div>	
		    <!-- END Product '. $_productCount. ' -->
		    </div>
		    ';

		    $_productDescriptionHTML=$_productDescriptionHTML.'
		    <!-- START Product '. $_id. ' Detail -->
			    <div id="'. $_id. '" style="display: none;">
			    	<div id="productdetail">
				    	<fieldset>
				    	<legend>'. $_name. '</legend>
					    	<div class="descriptionbox">
					    		<div class="descriptiontext">
						    	';
						    	if ($_useShortDescription) {$_productDescriptionHTML=$_productDescriptionHTML. '<p>'. $_shortDescription. '</p>';}
						    	if ($_useLongDescription) {$_productDescriptionHTML=$_productDescriptionHTML. '<p>'. $_longDescription. '</p>';}
						    	$_productDescriptionHTML=$_productDescriptionHTML.'
						    	</div>
						    	<div class="imageboxshadow">
							    	<div class="imagebox">
							    		<img alt="'.$_name.'" title="'.$_name.'" height="400" width="400" src="'. $_imageURL. '">
							    	</div>
							    </div>
							    <div class="descriptionfooter">
							    	<div class="buttonContainer">
						    			<a target="parent" href="'. $_url. htmlspecialchars($this->get('googleutmtag')). '" class="button">'. $this->__t->__('Buy Now'). '</a>
						    		</div>
						    	</div>
						    </div>
						</fieldset>
					</div>
		      <!-- END Product Detail -->
		      </div>';
		      
		      
			if ($_productCount >= $_productLimit) {
				$_productHTML=$_productHTML. '</div>';
				 break;}
					
			// new row not last item next row
			if ($x===3 && $_productCount < $_collectionItems){
			
				$_productHTML=$_productHTML. '
				</div>
				<div class="products">';
				$x=0;
			}
			
			// new row last item no more row
			if ($_productCount === $_collectionItems){
			
				$_productHTML=$_productHTML. '
				</div>';
			}
			
			
		}
		
		$_productHTML=$_productHTML. '
		<!-- END Product Container -->
		</div>';
		
		
			// contact form html
		    if ($this->get('showcontact')){	$_contactHTML=new HTMLContactForm(array(
		    											'languagecode'=>$this->get('languagecode'))); } else { $_contactHTML=null;}
			// about box html
		    if ($this->get('showabout')){ $_aboutHTML=new HTMLAboutBox(array(
		    											'version'=>$this->get('version'),
		    											'languagecode'=>$this->get('languagecode'),
		    											'pageTitle'=>$this->__t->__($this->__['pageTitle']),
		    											'pageDescription'=>$this->__t->__($this->__['pageDescription'])
		    											)); } else { $_aboutHTML=null;}
			
			// pagination for the nation
		    $_paginationHTML='
		    <!-- START Pagination -->
		    <div class="productpagination">';
		    $pages = ceil($this->get('collectionsize') / $_productLimit);
		    
		    if ($pages>1)
		    {
		    	if ($_collectionPage==1)
		    	{
			    	$_previousHTML='<li class="previous-off">« '. $this->__t->__('Previous'). '</li>';
			    	$_nextHTML='<li class="next"><a href="?collection='. $this->get('collectiontype'). '&amp;p='. ($_collectionPage+1).
			    	($_collectionType=='categoryproducts' ? '&amp;category='.$_categoryIDSelected : '').
			    	 '">'. $this->__t->__('Next'). ' »</a></li>';
		    	} else if ($_collectionPage >1 && $_collectionPage < $pages) {
			    	$_previousHTML='<li class="previous"><a href="?collection='. $this->get('collectiontype'). '&amp;p='. ($_collectionPage-1).
			    	($_collectionType=='categoryproducts' ? '&amp;category='.$_categoryIDSelected : '').
			    	 '">« '. $this->__t->__('Previous'). '</a></li>';	
			    	$_nextHTML='<li class="next"><a href="?collection='. $this->get('collectiontype'). '&amp;p='. ($_collectionPage+1).
			    	($_collectionType=='categoryproducts' ? '&amp;category='.$_categoryIDSelected : '').
			    	 '">'. $this->__t->__('Next'). ' »</a></li>';			    	
		    	
		    	} else if ($_collectionPage >1 && $_collectionPage == $pages) {
			    	$_previousHTML='<li class="previous"><a href="?collection='. $this->get('collectiontype'). '&amp;p='. ($_collectionPage-1).
			    	($_collectionType=='categoryproducts' ? '&amp;category='.$_categoryIDSelected : '').
			    	 '">« '. $this->__t->__('Previous'). '</a></li>';	
			    	$_nextHTML='';	    	
		    	}
		    	
		    	
		    	$_paginationHTML=$_paginationHTML.'<ul id="pagination-flickr">'.$_previousHTML;
		    				    
			    for ($i = 1; $i <= $pages; $i++) {
			    
			    	if ($i == $_collectionPage){
					   	$_paginationHTML=$_paginationHTML.'
					   		<li class="active">'. $i. '</li>
					   	';
					
					} else { 
					   	$_paginationHTML=$_paginationHTML.'
					   		<li><a href="?collection='. $this->get('collectiontype'). '&amp;p='. $i. 
					   		($_collectionType=='categoryproducts' ? '&amp;category='.$_categoryIDSelected : '').
					   		'">'. $i. '</a></li>
					   	';						
					}
				}
				$_paginationHTML=$_paginationHTML. $_nextHTML.'
				</ul>';
			}
			
		    $_paginationHTML=$_paginationHTML.'
		    <!-- END Pagination -->
		    </div>';
		    
		    // Menu / Categories HTML		
	    	if ($this->get('showmenu')){
		    	
		    	$_categories=$this->get('categories');
		    	$_categoriesProductCount=$this->get('categoriesproductcount');
		    	
		    	$_excludedProductCategoriesArray=explode(',',$this->__config->get('excludedProductCategories'));
		    	array_unshift($_excludedProductCategoriesArray,'dummy');
			    		    
		    	$_menuHTML='
		    	<div class="menu">
			    	<div id="navigation">
						<ul class="clear" id="nav">
							<li class="first"><a href="#">'. $this->__t->__('Products'). '</a>
								<div class="dropDown">
									<div class="top"></div>
									<div class="items">
										<ul>
										<li class="first"><a href="?collection=allproducts">'. $this->__t->__('All Products'). '</a></li>';
											foreach($_categories as $_category) 
											{
												// check excluded categories
												if (array_search($_category->getId(),$_excludedProductCategoriesArray)) { continue; }
												//
												if ($_categoryIDSelected===$_category->getId()) { $_selectedCollection=$_category->getName(); }
												// exclude default or roor
												if(preg_match('/Root/', $_category->getName())) { continue; }
												if(preg_match('/Default/', $_category->getName())) { continue; }
												if(!$_category->getName()) { continue; }
												
												
												//if($_category->hasChildren()) {$_menuHTML=$_menuHTML.'<li><a href="?collection=categoryproducts&amp;category='. $_category->getId(). '">'. $_category->getName(). '('. $_categoriesProductCount[$_category->getId()]. ')</a></li>';}
												if($_categoriesProductCount[$_category->getId()] > 0) {$_menuHTML=$_menuHTML.'<li><a href="?collection=categoryproducts&amp;category='. $_category->getId(). '">'. $_category->getName(). ' ('. $_categoriesProductCount[$_category->getId()]. ')</a></li>';}
											}
										$_menuHTML=$_menuHTML.'
										<li class="last"><a href="?collection=newfromdate">'. $this->__t->__('New Products'). '</a></li>
										</ul>
									</div>
								</div>					
							</li>'.						
							($this->get('showcontact') ? '<li class="last"><a class="fancybox" href="#contactformhtml">'. $this->__t->__('Contact'). '</a></li>' : ''). 
							($this->get('showabout') ? '<li class="last"><a class="fancybox" href="#about">'. $this->__t->__('About'). '</a></li>' : ''). '
						</ul>
					</div>
				</div>';
			} else {
				// no menu
				$_menuHTML=null;
			}
			
		// product item count summary html
		//	
		if ($_productCount > 0){
			$_productSummaryHTML= '
		    <div class="productsummary">
		    	<p>'. $_selectedCollection. ' ['. $this->get('languagecode'). '] - '. $this->__t->__('items'). ' '. (($_collectionPage-1)*$_productLimit + 1). ' '. $this->__t->__('to'). ' '. (($_collectionPage-1)*$_productLimit+$_productCount).' '. $this->__t->__('of'). ' '. $this->get('collectionsize').' '. $this->__t->__('total'). '.</p>
		    </div>
		    ';
	    } else {
		    $_productSummaryHTML. '
		    <div class="productsummary">
		    	<p>'. $this->__t->__('No products found!'). '</p>
		    </div>
		    ';		    
	    }
	    
	    // set class variable
		$this->set('producthtml',
			$_menuHTML. $_paginationHTML.$_productSummaryHTML. $_productHTML. $_productDescriptionHTML. $_contactHTML.$_aboutHTML);
			
		
	}
	
	
		private function createPage()
		{

					// create variable array for class
					$HTML = new HTMLMainPage(array(
					  "producthtml"  			=> 	$this->__['producthtml'],
					  "pageTitle"  				=> 	$this->__t->__($this->__['pageTitle']),
					  "pageDescription" 		=> 	$this->__t->__($this->__['pageDescription']),
					  "appIcon"  				=> 	$this->__['appIcon'],
					  "pagelikerequired"		=>	$this->__['pagelikerequired'],
					  "likestatus"				=>	$_SESSION['likestatus'],
					  "version"					=>	$this->__['versionNumber'],
					  "languagecode"			=>	$this->__['languagecode'],
					  "errorMessage" 			=>	$this->__['errorMessage']
					));			
					
					header("content-type: text/html; charset=utf-8");
					echo $HTML;
					
					unset($HTML);
		}
		
		public function set($key,$value)
		{
			$this->__[$key] = $value;
		}
			
	  	public function get($variable)
		{
			
			if (isset($this->__[$variable]))
			{
				return $this->__[$variable];
			} else {
				
				// for debugging
				throw new Exception('Class Variable \''. $variable. '\' has not been defined.');
				//return null;
			}

		}
		
		
		/**
		 * clean_up function.
		 * 
		 * @access private
		 * @param mixed $text
		 * @return void
		 */
		private function clean_up ($text)
		{
			$cleanText=$this->replaceHtmlBreaks($text," ");
			$cleanText=$this->strip_html_tags($cleanText);
			$cleanText=preg_replace("/&#?[a-z0-9]+;/i"," ",$cleanText);
			$cleanText=htmlspecialchars($cleanText);
			
			return $cleanText;
		}
		
		/**
		 * strip_html_tags function.
		 * 
		 * @access private
		 * @param mixed $text
		 * @return void
		 */
		private function strip_html_tags( $text )
		{
		    $text = preg_replace(
		        array(
		          // Remove invisible content
		            '@<head[^>]*?>.*?</head>@siu',
		            '@<style[^>]*?>.*?</style>@siu',
		            '@<script[^>]*?.*?</script>@siu',
		            '@<object[^>]*?.*?</object>@siu',
		            '@<embed[^>]*?.*?</embed>@siu',
		            '@<applet[^>]*?.*?</applet>@siu',
		            '@<noframes[^>]*?.*?</noframes>@siu',
		            '@<noscript[^>]*?.*?</noscript>@siu',
		            '@<noembed[^>]*?.*?</noembed>@siu',
		          // Add line breaks before and after blocks
		            '@</?((address)|(blockquote)|(center)|(del))@iu',
		            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
		            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
		            '@</?((table)|(th)|(td)|(caption))@iu',
		            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
		            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
		            '@</?((frameset)|(frame)|(iframe))@iu',
		        ),
		        array(
		            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
		            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
		            "\n\$0", "\n\$0",
		        ),
		        $text );
		    return strip_tags( $text );
		}
		
		/**
		 * replaceHtmlBreaks function.
		 * 
		 * @access private
		 * @param mixed $str
		 * @param mixed $replace
		 * @param mixed $multiIstance (default: FALSE)
		 * @return void
		 */
		private function replaceHtmlBreaks($str, $replace, $multiIstance = FALSE)
		{
		  
		    $base = '<[bB][rR][\s]*[/]*[\s]*>';
		    
		    $pattern = '|' . $base . '|';
		    
		    if ($multiIstance === TRUE) {
		        //The pipe (|) delimiter can be changed, if necessary.
		        
		        $pattern = '|([\s]*' . $base . '[\s]*)+|';
		    }
		    
		    return preg_replace($pattern, $replace, $str);
		}
		
		/**
		 * getBrowserLanguages function.
		 * -- return a language code array from available browser languages
		 * @access private
		 * @return void
		 */
		private function getBrowserLanguages()
		{
		
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				foreach (explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) as $accept) {
					if (preg_match("!([a-z-]+)(;q=([0-9.]+))?!", trim($accept), $found)) {
						$langs[] = $found[1];
						$quality[] = (isset($found[3]) ? (float) $found[3] : 1.0);
					}
				}
				// Order the language codes
				array_multisort($quality, SORT_NUMERIC, SORT_DESC, $langs);
		
				return $langs;
				
			} else {
			
				return "notdetected";
			}
		}
		
		/**
		 * getStoreLanguage function.
		 * -- parse browser language array and match to configured store languages
		 * @access private
		 * @param mixed $langs
		 * @return storeid
		 */
		private function getStoreLanguage($langs)
		{
				
				// default language code for store
				$_storeDefaultLanguage=$this->__config->get('storeDefaultLanguage');
				$_defaultStoreID=1;
				
				// get supported codes and configured store id from config
				$_storesLangArray=explode(',',$this->__config->get('storesLanguage'));
				$_storesConfiguredArray=explode(',',$this->__config->get('storesConfigured'));
				
				// determing default store id based on default language code
				$_defaultStoreID=$_storesConfiguredArray[array_search($_storeDefaultLanguage, $_storesLangArray)];
				
				
				// iterate through languages found in the accept-language header
				// return default if no browser language detected
				if ($langs==="notdetected") {return $_defaultStoreID;}
				
				foreach ($langs as $key=>$lang) {
		
					$languageCode=strtolower($lang);
			
					$pos = strpos($lang, "-");
					if ($pos !== false) {
						$languageCodeArray=explode('-',$languageCode);
						$languageCode=$languageCodeArray[0];
					}
				
						if (in_array($languageCode, $_storesLangArray))
						{
							$store=$_storesConfiguredArray[array_search($languageCode, $_storesLangArray)];
							break;
							
						} else {
						
							$store= $_defaultStoreID;
						}
				}
				
				$this->set('languagecode',$languageCode);
				return $store;
		}
		
	private function getLanguageFromStoreID($_storeID)
	{
		// get supported codes and configured store id from config
		$_languageCode=null;
		$_storesLangArray=explode(',',$this->__config->get('storesLanguage'));
		$_storesConfiguredArray=explode(',',$this->__config->get('storesConfigured'));
		
		$_languageCode=$_storesLangArray[array_search($_storeID,$_storesConfiguredArray)];
	
		if (!empty($_languageCode))
		{
			$this->set('languagecode',$_languageCode);
		} else {
			throw new Exception('Invalid store ID - '. $_storeID. '.');
		}
		
	}
	
	private function getFirstSentence($content) {
	
	    $content = str_ireplace('<br />', ' - ', $content);
	    $content = html_entity_decode(strip_tags($content));
	    $pos = strpos($content, '.');
	       
	    if($pos === false) {
	        return htmlspecialchars($content);
	    }
	    else {
	        return htmlspecialchars(substr($content, 0, $pos+1));
	    }
   
	}

	private function curPageURL() {
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
	}
	
	private function getLikeStatus()
	{

			$likeStatus=new DecodeSignedLikeRequest($this->__config->get('fbAppSecret'));
				$this->set('likeStatus',$likeStatus->get('likeStatus'));
					unset($likeStatus);
	}
		
	}
