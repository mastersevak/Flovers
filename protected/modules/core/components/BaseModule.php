<?php

/**
 * Base admin module 
 * 
 * @uses CWebModule
 */
class BaseModule extends CWebModule {

	public $_assetsUrl = null;

	/**
	 * Publish admin stylesheets,images,scripts,etc.. and return assets url
	 *
	 * @access public
	 * @return string Assets url
	 */
	public function getAssetsUrl()
	{
		if($this->_assetsUrl===null)
		{
			$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
			$this->_assetsUrl = assets($this->basePath.DS.'assets', false, -1, $recreate);
		}

		return $this->_assetsUrl;
	}

	/**
	 * Set assets url
	 *
	 * @param string $url
	 * @access public
	 * @return void
	 */
	public function setAssetsUrl($url)
	{
		$this->_assetsUrl = $url;
	}

}
 