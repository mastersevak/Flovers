<?php
/**
 * EAjaxUpload class file.
 * This extension is a wrapper of http://valums.com/ajax-upload/
 *
 * @author Vladimir Papaev <kosenka@gmail.com>
 * @version 0.1
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * HOW to USE
 */
class Pirobox extends CWidget
{

    public $theme = 'black';
    public $id;
    public $version = '1.0';

    public $config = array();

    private $defaultOptions = array(
        'piro_speed' => 300,
        'bg_alpha' => 0.7,
        'piro_scroll' => true,
        'zoom_mode' => false,
        'move_mode' =>'mousemove',
        'share' => false,
    );
        
    public function run(){

        if(empty($this->id))
            throw new CException('ID parametr of Pirobox plugin cannot be empty.');

        echo '<div id="'.$this->id.'"><noscript><p>Please enable JavaScript to use file uploader.</p></noscript></div>';

        $assets = dirname(__FILE__).'/assets';
        $baseUrl = Yii::app()->assetManager->publish($assets);

        Yii::app()->clientScript->registerScriptFile($baseUrl . '/js/pirobox_extended_'.$this->version.'.js', CClientScript::POS_HEAD);

        Yii::app()->clientScript->registerCssFile($baseUrl.'/themes/'.$this->theme.'/style.css');

        $config = array_merge($this->defaultOptions, $this->config);

        if($config['zoom_mode'])
            Yii::app()->clientScript->registerCoreScript('jquery.ui');

        $config_encode = CJavaScript::encode($config);

        cs()->registerScript('Pirobox_'.$this->id, "$.pirobox_ext($config_encode)", CClientScript::POS_LOAD);

    }

}