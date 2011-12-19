<?php
/**
 * @package     PSProductReviews
 * @author      Axel Etcheverry <axel@etcheverry.biz>
 * @copyright   Copyright (c) 2011 Axel Etcheverry (http://www.axel-etcheverry.com)
 * Displays     <a href="http://creativecommons.org/licenses/MIT/deed.fr">MIT</a>
 * @license     http://creativecommons.org/licenses/MIT/deed.fr    MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}


class PSProductReviews extends Module
{
	private $_html = '';
	private $_baseUrl;
	
	public function __construct()
	{
	    require_once(dirname(__FILE__) . '/ProductReview.php');
	    
		$this->name             = 'psproductreviews';
		$this->tab              = 'front_office_features';
		$this->version          = ProductReview::VERSION;
		$this->author           = 'Axel Etcheverry';
		$this->need_instance    = 0;

		parent::__construct();

		$this->displayName      = $this->l('Product Reviews');
		$this->description      = $this->l('Display audiofanzine reviews about a product.');
	}

    public function install()
	{
	    if (parent::install() == false 
	        OR $this->registerHook('productTab') == false 
	        OR $this->registerHook('productTabContent') == false
	        OR $this->registerHook('extraRight') == false
	        OR $this->registerHook('header') == false
		) {
			return false;
		}
		return true;
    }

    public function uninstall()
	{
		if (!parent::uninstall() OR !Configuration::deleteByName('PRODUCT_REVIEWS_API_ID') OR !Configuration::deleteByName('PRODUCT_REVIEWS_API_KEY')) {
			return false;
		}
		return true;
	}

    public function getContent()
	{
		$this->_setBaseUrl();		
		$this->_html = '<h2>' . $this->displayName . '</h2>';
		
		$this->_postProcess();
		
		$this->_displayForm();
		
		return $this->_html;
	}
	
	private function _setBaseUrl()
	{
		$this->_baseUrl = 'index.php?' . http_build_query($_GET);
	}
	
	private function _postProcess()
	{
	    if (Tools::isSubmit('submitReviewsConfig')) {
		    Configuration::updateValue('PRODUCT_REVIEWS_API_ID', (int)Tools::getValue('application_id'));
		    Configuration::updateValue('PRODUCT_REVIEWS_API_KEY', Tools::getValue('key'));
		    
		    $this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('Confirmation') . '" />' . $this->l('Settings updated') . '</div>';
	    }
	}
	
	private function _displayForm()
	{
        $this->_html .= '<fieldset class="width2">' . PHP_EOL;
	    $this->_html .= '<legend><img alt="' . $this->l('Configuration') . '" src="../img/admin/cog.gif" />' . $this->l('Configuration') . '</legend>' . PHP_EOL;
	    $this->_html .= '<form method="post" action="' . $this->_baseUrl . '">' . PHP_EOL;
	    $this->_html .= '<label style="padding-top: 0;">' . $this->l('Application_id') . '</label>' . PHP_EOL;
	    $this->_html .= '<div class="margin-form">' . PHP_EOL; 
	    $this->_html .= '<input name="application_id" type="text" class="text" value="' . Configuration::get('PRODUCT_REVIEWS_API_ID') . '" style="width: 20px; text-align: right;" />' . PHP_EOL;
	    $this->_html .= '</div>' . PHP_EOL;
	    $this->_html .= '<div class="clear"></div>' . PHP_EOL;
	    $this->_html .= '<label style="padding-top: 0;">' . $this->l('Application key') . '</label>' . PHP_EOL;
	    $this->_html .= '<div class="margin-form">' . PHP_EOL; 
	    $this->_html .= '<input name="key" type="text" class="text" value="' . Configuration::get('PRODUCT_REVIEWS_API_KEY') . '" style="width: 200px; text-align: right;" />' . PHP_EOL;
	    $this->_html .= '</div>' . PHP_EOL;
	    $this->_html .= '<div class="clear"></div>' . PHP_EOL;
	    $this->_html .= '<div class="margin-form clear">' . PHP_EOL;
	    $this->_html .= '<input type="submit" name="submitReviewsConfig" value="' . $this->l('Save') . '" class="button" />' . PHP_EOL;
	    $this->_html .= '</div>' . PHP_EOL;
	    $this->_html .= '</form>' . PHP_EOL;
	    $this->_html .= '</fieldset>' . PHP_EOL;
	}
	
	public function hookHeader()
	{
	    Tools::addCSS('/modules/psproductreviews/css/reviews.css');
	}
	
	public function hookProductTab($params)
    {
		global $smarty, $cookie;
		
		$product = new Product($_GET['id_product'], true, $cookie->id_lang);
		
		$productReview = new ProductReview();
		$reviews = $productReview->getByProduct($product->manufacturer_name, $product->name);
		
		if(isset($reviews['reviews'])) {
		    $nbReviews = count($reviews['reviews']);
		} else {
		    $nbReviews = 0;
		}
		
		$smarty->assign(array(
		    'nbReviews' => $nbReviews
		));

		return ($this->display(__FILE__, '/tab.tpl'));
	}
	
	public function hookProductTabContent($params)
    {
		global $smarty, $cookie;

        $product = new Product($_GET['id_product'], true, $cookie->id_lang);

    	$productReview = new ProductReview();
    	$reviews = $productReview->getByProduct($product->manufacturer_name, $product->name);

        if(isset($reviews['message']) && $reviews['code'] != 404) {
            $smarty->assign(array(
    		    'error' => $reviews['message']
    		));
        } else {
            $smarty->assign(array(
    		    'reviews' => $reviews['reviews']
    		));
        }
		
		return ($this->display(__FILE__, '/psproductreviews.tpl'));
	}
	
	public function hookExtraRight($params)
	{
	    global $smarty, $cookie;

        $product = new Product($_GET['id_product'], true, $cookie->id_lang);

    	$productReview = new ProductReview();
    	$reviews = $productReview->getByProduct($product->manufacturer_name, $product->name);
    	
        if(isset($reviews['product']['product_avgMark'])) {
            $smarty->assign(array(
    		    'avgMark' => $reviews['product']['product_avgMark']
    		));
	    }
        
	    return ($this->display(__FILE__, '/productmark.tpl'));
	}
}
