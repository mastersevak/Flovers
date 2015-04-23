<?php

Yii::setPathOfAlias('TheTwelve', realpath(dirname(__FILE__) . '/lib/TheTwelve'));

use TheTwelve\Foursquare\ApiGatewayFactory;
use TheTwelve\Foursquare\HttpClient\CurlHttpClient;
use TheTwelve\Foursquare\Redirector\HeaderRedirector;
use TheTwelve\Foursquare\Cache\CacheAdapter;

class FoursquareComponent extends CApplicationComponent
{
    public $clientId;
    
    public $clientSecret;
    
    public $endpointUri = 'https://api.foursquare.com';
    
    public $versionApi = 2;
    
    public $defaultLocale = 'en';
    
    public $cacheEnable = false;
    
    public $cacheID;
    
    private $_factory;
    private $_httpClient;
    private $_headerRedirector;
    private $_cacheAdapter;
    
    public function getFactory()
    {
        if($this->_factory === null) {
            $this->_factory = new ApiGatewayFactory($this->getHttpClient(), $this->getHeaderRedirector(), $this->getCacheAdapter());
            $this->_factory->setClientCredentials($this->clientId, $this->clientSecret);
            $this->_factory->setEndpointUri($this->endpointUri);
            $this->_factory->useVersion($this->versionApi);
        }
        
        return $this->_factory;
    }
    
    public function getHttpClient()
    {
        if($this->_httpClient === null) {
            $this->_httpClient = new CurlHttpClient();
            $this->_httpClient->setVerifyPeer(false);
            $this->_httpClient->setLocale($this->defaultLocale);
        }
        
        return $this->_httpClient;
    }
    
    public function getHeaderRedirector()
    {
        if($this->_headerRedirector === null) {
            $this->_headerRedirector = new HeaderRedirector();
        }
        
        return $this->_headerRedirector;
    }
    
    public function getCacheAdapter()
    {
        if($this->_cacheAdapter === null && $this->cacheEnable && $this->cacheID) {
            $cache = Yii::app()->getComponent($this->cacheID);
            $this->_cacheAdapter = new CacheAdapter($cache);
        }
        
        return $this->_cacheAdapter;
    }
    
    public function setLocale($locale)
    {
        $allowedLocales = array('en', 'es', 'fr', 'de', 'it', 'ja', 'th', 'ko', 'ru', 'pt', 'id');
        
        if(!in_array($locale, $allowedLocales)) {
            throw new AppException('Unknown locale name');
        }
        
        $this->getHttpClient()->setLocale($locale);
    }
    
    public function __call($name, $parameters)
    {
        if(!method_exists($this->getFactory(), $name)) {
            throw new AppException('Call undefined method');
        }
        
        return call_user_func_array(array($this->getFactory(), $name), $parameters);
    }
}