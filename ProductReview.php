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

class ProductReview
{
    const VERSION = '0.1';
    
    protected $_provider = "http://fr.audiofanzineapi.com/1/reviews/playlist.json";
    protected $_params = array();
    protected $_cacheLife = 604800; // 1 week
    protected $_userAgent;
    
    public function __construct()
    {
        $this->_userAgent = sprintf(
            "Mozilla/5.0 (compatible; PSProductReviews/%s; +http://wiki.audiofanzineapi.com/)", 
            self::VERSION
        );
        
        $this->_params['application_id'] = Configuration::get('PRODUCT_REVIEWS_API_ID');
        $this->_params['key'] = Configuration::get('PRODUCT_REVIEWS_API_KEY');
    }
    
    public function getByProduct($manufacturer, $product)
    {
        $this->_params['manufacturer_name'] = $manufacturer;
        $this->_params['product_name'] = $product;
        
        $url = $this->_provider . '?' . http_build_query($this->_params);

        if(_PS_CACHE_ENABLED_) {
            if (!$result = Cache::getInstance()->get(md5($url))) {
				
				$result = $this->_call($url);
				
				Cache::getInstance()->set(md5($url), $result, $this->_cacheLife);
			}
			
			return $result;
        } else {
            return $this->_call($url);
        }
    }
    
    
    protected function _call($url)
    {
        if(extension_loaded('curl')) {
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); 
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); 
            curl_setopt($curl, CURLOPT_TIMEOUT, 10); 
            curl_setopt($curl, CURLOPT_MAXREDIRS, 0); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false); 
            curl_setopt($curl, CURLOPT_USERAGENT, $this->_userAgent);
            curl_setopt($curl, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
            $content = curl_exec($curl);
            curl_close($curl);
            unset($curl);
            
        } elseif((bool)ini_get('allow_url_fopen')) {
            
            $content = file_get_contents($url);
            
        } else {
            
            return false;
            
        }
        
        return json_decode($content, true);
    }
}