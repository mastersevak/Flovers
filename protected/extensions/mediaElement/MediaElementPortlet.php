<?php
/**
 * MediaElement
 * 
 * This ext allow you to add HTML5 audio and video player using mediaElement JS library to your Yii project. 
 * 
 * @version 1.0
 * @author Shiv Charan Panjeta <shiv@toxsl.com> <shivcharan.panjeta@outlook.com>
 */
/**

Usage:

	$this->widget ( 'ext.mediaElement.MediaElementPortlet',
	array ( 
	'url' => 'http://www.toxsl.com/test/bunny.mp4',
	//'model' => $model,
	//'attribute' => 'url'
	// 'mimeType' =>'audio/mp3',
	
	));
	
*/

Yii::import('zii.widgets.CPortlet');

class MediaElementPortlet extends CPortlet
{
	public $attribute = null;
	public $model = null;
	public $url = null;
	public $mimeType = null;
	public $mediaType = 'audio';
	public $autoplay = false;
	public $htmlOptions = array();
	public $scriptUrl = null;
	public $scriptFile = array('mediaelement-and-player.js');
	public $cssFile = array('mediaelementplayer.css','mejs-skins.css');

	protected function registerScriptFile($fileName,$position=CClientScript::POS_HEAD){
		Yii::app()->clientScript->registerScriptFile($this->scriptUrl.'/'.$fileName,$position);
	}
	protected function registerCssFile($fileName){
		Yii::app()->clientScript->registerCssFile($this->scriptUrl.'/'.$fileName);
	}
	protected function resolvePackagePath(){
		if($this->scriptUrl===null ){
			$basePath=__DIR__. '/assets';
			$baseUrl=Yii::app()->getAssetManager()->publish($basePath);
			if($this->scriptUrl===null)
			$this->scriptUrl=$baseUrl.'';
		}
	}

	protected function registerCoreScripts(){
		$cs=Yii::app()->getClientScript();
		if(is_string($this->cssFile))
		$this->registerCssFile($this->cssFile);
		else if(is_array($this->cssFile)){
			foreach($this->cssFile as $cssFile)
			$this->registerCssFile($cssFile);
		}

		$cs->registerCoreScript('jquery');

		if(is_string($this->scriptFile))
		$this->registerScriptFile($this->scriptFile);
		else if(is_array($this->scriptFile)){
			foreach($this->scriptFile as $scriptFile)
			$this->registerScriptFile($scriptFile);
		}
	}

	public function init() {
		parent::init();

		$model = $this->model;
		$att = $this->attribute;
		if ( $this->url == null ) $this->url = $model->$att;
		if ( $this->mimeType == null ) $this->mimeType = "audio/mp3";
		list ( $type, $codec ) = explode( '/', $this->mimeType);
		
		if ( $type != null ) {
			if($type == 'audio' || $type == 'video' ) $this->mediaType = $type;
		}
		if (!isset($this->htmlOptions['id']))
		$this->htmlOptions['id'] = $this->getId();

		$this->resolvePackagePath();
		$this->registerCoreScripts();

	}


	public function run() {
		parent::run();

		echo CHtml::tag($this->mediaType, [
				'id' => $this->id, 
				'src' => $this->url,
				'type' => $this->mimeType,
				'controls' => 'controls',
				'autoplay' => $this->autoplay
			],  '');


		cs()->registerScript('run_mediaElemen_portlet', "$('audio,video').mediaelementplayer();");

	}
}