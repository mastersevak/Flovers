<?php

Yii::import('zii.widgets.CBreadcrumbs');

class SBreadcrumbs extends CBreadcrumbs
{

	public $tagName='ul';

	public $homeLink;

	public $activeLinkTemplate= '<li><a href="{url}">{label}</a></li>';
	
	public $inactiveLinkTemplate= '<li><a class="active">{label}</a></li>';

	public $separator = null;

	/**
	 * Renders the content of the portlet.
	 */
	public function run()
	{
		if(empty($this->links))
			return;

		echo CHtml::openTag($this->tagName, $this->htmlOptions)."\n";

		$links=array();
		if($this->homeLink===null)
			$links[]='<li>'.CHtml::link("ГЛАВНАЯ", [param('adminHome')]).'</li>';
		elseif($this->homeLink!==false)
			$links[]='<li>'.$this->homeLink.'</li>';
		
		foreach($this->links as $label=>$url)
		{
			if(is_string($label) || is_array($url))
				$links[]=strtr($this->activeLinkTemplate,array(
					'{url}'=>CHtml::normalizeUrl($url),
					'{label}'=>$this->encodeLabel ? CHtml::encode($label) : $label,
				));
			else
				$links[]=str_replace('{label}',$this->encodeLabel ? CHtml::encode($url) : $url,$this->inactiveLinkTemplate);
		}
		echo implode($this->separator,$links);
		echo CHtml::closeTag($this->tagName);
	}
}