<?php 

Yii::import('bootstrap.components.TbApi');
/**
* Bootstrap
*/
class Bootstrap extends TbApi
{
	private $_assetsUrl;
	
	public $forceCopyAssets = YII_DEBUG;

	/**
     * Registers the Bootstrap CSS.
     * @param string $url the URL to the CSS file to register.
     */
    public function registerCoreCss($url = null)
    {
        if ($url === null) {
            // $fileName = YII_DEBUG ? 'bootstrap.css' : 'bootstrap.min.css';
            $fileName = 'bootstrap.min.css';
            $url = $this->getAssetsUrl() . '/css/' . $fileName;
        }
        Yii::app()->clientScript->registerCssFile($url);
    }

    /**
     * Registers the responsive Bootstrap CSS.
     * @param string $url the URL to the CSS file to register.
     */
    public function registerResponsiveCss($url = null)
    {
        if ($url === null) {
            $fileName = YII_DEBUG ? 'bootstrap-theme.css' : 'bootstrap-theme.min.css';
            $url = $this->getAssetsUrl() . '/css/' . $fileName;
        }
        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerCssFile($url);
    }

    /**
     * Registers the Yiistrap CSS.
     * @param string $url the URL to the CSS file to register.
     */
    public function registerYiistrapCss($url = null)
    {
        if ($url === null) {
            $fileName = YII_DEBUG ? 'yiistrap.css' : 'yiistrap.min.css';
            $url = $this->getAssetsUrl() . '/css/' . $fileName;
        }
        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerCssFile($url);
    }

    /**
     * Registers all Bootstrap CSS files.
     */
    public function registerAllCss()
    {
        $this->registerCoreCss();
        $this->registerResponsiveCss();
        // $this->registerYiistrapCss();
    }

    /**
     * Registers jQuery and Bootstrap JavaScript.
     * @param string $url the URL to the JavaScript file to register.
     * @param int $position the position of the JavaScript code.
     */
    public function registerCoreScripts($url = null, $position = CClientScript::POS_END)
    {
        if ($url === null) {
            $fileName = YII_DEBUG ? 'bootstrap.js' : 'bootstrap.min.js';
            $url = $this->getAssetsUrl() . '/js/' . $fileName;
        }
        /** @var CClientScript $cs */
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $cs->registerScriptFile($url, $position);
    }

    /**
     * Registers all Bootstrap JavaScript files.
     */
    public function registerAllScripts()
    {
        $this->registerCoreScripts();
    }

    /**
     * Registers all assets.
     */
    public function register()
    {
        $this->registerAllCss();
        $this->registerAllScripts();
    }

    /**
     * Registers a specific Bootstrap plugin using the given selector and options.
     * @param string $name the plugin name.
     * @param string $selector the CSS selector.
     * @param array $options the JavaScript options for the plugin.
     * @param int $position the position of the JavaScript code.
     */
    public function registerPlugin($name, $selector, $options = array(), $position = CClientScript::POS_END)
    {
        $options = !empty($options) ? CJavaScript::encode($options) : '';
        $script = "jQuery('{$selector}').{$name}({$options});";
        $id = __CLASS__ . '#Plugin' . self::$counter++;
        Yii::app()->clientScript->registerScript($id, $script, $position);
    }

    /**
     * Registers events using the given selector.
     * @param string $selector the CSS selector.
     * @param string[] $events the JavaScript event configuration (name=>handler).
     * @param int $position the position of the JavaScript code.
     */
    public function registerEvents($selector, $events, $position = CClientScript::POS_END)
    {
        if (empty($events)) {
            return;
        }

        $script = '';
        foreach ($events as $name => $handler) {
            $handler = ($handler instanceof CJavaScriptExpression)
                ? $handler
                : new CJavaScriptExpression($handler);

            $script .= "jQuery('{$selector}').on('{$name}', {$handler});";
        }
        $id = __CLASS__ . '#Events' . self::$counter++;
        Yii::app()->clientScript->registerScript($id, $script, $position);
    }

    /**
     * Returns the url to the published assets folder.
     * @return string the url.
     */
    protected function getAssetsUrl()
    {
        if (isset($this->_assetsUrl)) {
            return $this->_assetsUrl;
        } else {
            $assetsPath = Yii::getPathOfAlias('app.assets.plugins.bootstrapv3');
            $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, $this->forceCopyAssets);
            return $this->_assetsUrl = $assetsUrl;
        }
    }
}