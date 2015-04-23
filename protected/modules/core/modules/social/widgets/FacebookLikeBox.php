<?php

/**
* FacebookLikeBox plugin
*/
class FacebookLikeBox extends CWidget
{
	public $icons = array();
	
	public function run(){
		echo CHtml::openTag('div', array('class'=>'mb30', 'style'=>'background:white'));
		
		$this->widget('ext.facebook.plugins.LikeBox', array(
		    'href' => 'https://www.facebook.com/yerevanrestoam', // if omitted Facebook will use the OG meta tag
		    'width' => 675,
		    'height' => 215 ,
		    'header' => true,
		    'show_faces' => true,
		));

		echo CHtml::closeTag('div');
	}
}