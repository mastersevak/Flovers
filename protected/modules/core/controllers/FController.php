<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
 

class FController extends BaseController
{
	public $baseUrl;

	/* SEO Vars */
    public $pageKeywords = '';
    public $pageRobotsIndex = true;

    public $pageOgTitle = '';
    public $pageOgDesc = '';
    public $pageOgImage = '';
    public $pageOgUrl = '';
    public $pageOgType = 'website';
    public $pageOgVideoType = '';
    public $pageOgVideo = '';

    public $fullUrl;

    public $layout = '//layouts/main';

    public $vars;

    public function actions()
	{
		return array(
			 'captcha'=>array(
				'class'=>'SCaptchaAction',
				'height'=>30,
				'backend'=> 'gd'
			),
		);
	}
	
	public function init(){
		parent::init();

		$this->isFront = true;

		if(!app()->theme) app()->theme = 'frontend';
        
      	$this->baseUrl = bu().lang();

      	user()->loginUrl = param('frontLoginUrl') ? url(param('frontLoginUrl')) : $this->baseUrl.'/login';

      	$this->setDefaultsForSeo();

      	// $this->registerStyles();
      	
      	$this->registerMeta();

      	$cs = Yii::app()->clientScript;     

      	// $this->registerScripts();
        // $cs->registerPackage('bootstrap'); //register bootstrap script and styles
      	$cs->registerPackage('frontend-globals'); //стили и скрипты для темы
      	$cs->registerPackage('fontawesome');
      	$cs->registerPackage('selectstyler-frontend');
      	$cs->registerPackage('project-specific-frontend');
      	$cs->registerPackage("messenger");
      	// app()->facebook->locale = param('locales/'.lang());
      	// setlocale(LC_ALL, $langs[lang()]);
    }

    public function registerScripts(){
    	$cs = Yii::app()->clientScript;

    	// HTML5 Shiv + detect touch events
    	// cs()->registerScriptFile($this->assetsUrl."/js/modernizr.custom.js", CClientScript::POS_HEAD);
    	
    	if(!YII_DEBUG){
    		//google analytics
	  		// cs()->registerScriptFile($this->assetsUrl."/js/googleAnalytics.js", CClientScript::POS_HEAD);
	    }
	    $cs->registerScriptFile($this->assetsUrl."/js/gridset-overlay.js", CClientScript::POS_END);

		//scripts
		// $cs->registerScriptFile($this->assetsUrl."/js/plugins/carousel/jquery.carousel.js", CClientScript::POS_END);
		// $cs->registerCssFile($this->assetsUrl."/js/plugins/carousel/jquery.carousel.css");

		// $cs->registerScriptFile($this->assetsUrl."/js/plugins/jquery.dotdotdot-1.5.7-packed.js", CClientScript::POS_END);
		
		// $cs->registerScriptFile($this->assetsUrl."/js/functions.js", CClientScript::POS_END);
        
        $cs->registerScriptFile($this->coreAssetsUrl .'/js/main/main-functions.js');
        $cs->registerScriptFile($this->assetsUrl .'/js/main.js');

        
    }

    private function registerStyles(){  
    	$cs = Yii::app()->clientScript;     
        
        //{* ЭТО НУЖНО ОБЪЯЗАТЕЛЪНО *}
        $cs->registerCssFile($this->rootAssetsUrl."/plugins/font-awesome/css/font-awesome.css");
        //мои стили
        $cs->registerCssFile($this->assetsUrl."/stylesheets/gridset.css");
        $cs->registerCssFile($this->assetsUrl."/stylesheets/custom-style.css");
        $cs->registerCssFile($this->assetsUrl."/stylesheets/slider.css");
        $cs->registerCssFile($this->assetsUrl."/stylesheets/main.css");
    }

    public function registerMeta(){
    	$cs = Yii::app()->clientScript;     

		$cs->registerMetaTag('width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no', 'viewport');
        $cs->registerMetaTag(lang(), 'language');
	}
    
    public function setDefaultsForSeo(){
		
		$this->fullUrl = request()->hostInfo.request()->url;
	    //if last symbol is / remove it
	    if (substr($this->fullUrl, -1, 1) == '/') { 
	    	$this->fullUrl = substr($this->fullUrl, 0, strlen($this->fullUrl)-1);
	    }

	    $this->pageTitle = strip_tags(Block::getBlock(($this->subdomain ? $this->subdomain."-" : "").'default-title'));

	    $this->pageDesc = strip_tags(Block::getBlock(($this->subdomain ? $this->subdomain."-" : "").'default-description'));
	    
	    $this->pageKeywords = strip_tags(Block::getBlock(($this->subdomain ? $this->subdomain."-" : "").'default-keywords'));

	    $this->pageOgImage = request()->hostInfo.$this->assetsUrl."/images/logo.png";
	}


	// Displays SEO-related Variables
	public function displaySeo()
	{
	    // STANDARD TAGS
	    // -------------------------
	    
	    echo "\t".''.PHP_EOL;

	    //Page title
	    if( !empty($this->pageTitle) )
	    	echo "\t".'<title>', 
	    		CHtml::encode($this->pageTitle), '</title>'.PHP_EOL;

	    // Page Desc
	    if( !empty($this->pageDesc) )
	    	echo "\t".'<meta name="Description" content="', 
	    		CHtml::encode($this->pageDesc),'">'.PHP_EOL;
	    
	    // Page Keywords
	    if( !empty($this->pageKeywords) )
	    	echo "\t".'<meta name="Keywords" content="', 
	    		CHtml::encode($this->pageKeywords), '">'.PHP_EOL;

	    // Option for NoIndex
	    if ( $this->pageRobotsIndex == false ) {
	        echo '<meta name="robots" content="noindex">'.PHP_EOL;
	    }

	    //Add canonical url
	    if(request()->hostInfo.request()->url != $this->fullUrl)
	    echo "\t".'<link rel="canonical" href="', $this->fullUrl, '">'.PHP_EOL;

	    // OPEN GRAPH(FACEBOOK) META
	    // -------------------------
	    //og:title
	    $ogTitle = actual($this->pageOgTitle, $this->pageTitle);
        echo "\t".'<meta property="og:title" content="', encode($ogTitle),'">'.PHP_EOL;
	    
	    //og:description
	    $ogDesc = actual($this->pageOgDesc, $this->pageDesc);
        echo "\t".'<meta property="og:description" content="', encode($ogDesc),'">'.PHP_EOL;

	    //og:image
	    $ogImage = $this->pageOgImage;
	    echo "\t".'<meta property="og:image" content="', encode($ogImage),'">'.PHP_EOL;
	    
	    //og:url
	    $ogUrl = actual($this->pageOgUrl, $this->fullUrl);
	    echo "\t".'<meta property="og:url" content="', encode($ogUrl),'">'.PHP_EOL;

	   	//og:type
	    echo "\t".'<meta property="og:type" content="', $this->pageOgType,'">'.PHP_EOL;
	
		
		if($this->pageOgType == 'video' && !empty($this->pageOgVideo)){
		    //og:video
		    echo "\t".'<meta property="og:video" content="http://www.youtube.com/v/'.$this->pageOgVideo.'">'.PHP_EOL;

		    //og:video:type
		    echo "\t".'<meta property="og:video:type" content="'.$this->pageOgVideoType.'">'.PHP_EOL;

		   	//og:video:width
			echo "\t".'<meta property="og:video:width" content="640">'.PHP_EOL;

		   	//og:video:height
			echo "\t".'<meta property="og:video:height" content="480">'.PHP_EOL;
		}

	   	//og:site_name
		echo "\t".'<meta property="og:site_name" content="'.app()->name.'">'.PHP_EOL;

	   	//fb:app_id
		echo "\t".'<meta property="fb:app_id" content="158066407721898">'.PHP_EOL;
		// echo "\t".'<meta property="fb:admins" content="158066407721898">'.PHP_EOL;

		//alternate languages
		$langs = param('languages');
		$defLang = param('defaultLanguage');
		if(count($langs) > 1){
			foreach($langs as $key => $lang){
				$r = request();
				$langSuffix = ($key == $defLang ? '' : '/'.$key);
				$uri = $langSuffix.(lang() != $defLang ? (substr($r->requestURI, strlen(lang()) + 1) ) : $r->requestURI) ;
				$href = $r->hostInfo.$uri;
				$hrefLang = $key == $defLang ? 'x-default' : $key;
				echo '<link rel="alternate" hreflang="'.$hrefLang.'" href="'.$href.'" />'.PHP_EOL;
			}
		}
	  	
	}

	protected function afterRender($view, &$output) {
		parent::afterRender($view,$output);
		//Yii::app()->facebook->addJsCallback($js); // use this if you are registering any $js code you want to run asyc
		// Yii::app()->facebook->initJs($output); // this initializes the Facebook JS SDK on all pages
		// Yii::app()->facebook->renderOGMetaTags(); // this renders the OG tags
		return true;
	}

}
