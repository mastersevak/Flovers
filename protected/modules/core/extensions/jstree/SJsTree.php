<?php

class SJsTree extends CWidget
{
	/**
	 * @var string Id of elements
	 */
	public $id;

	/**
	 * @var array of nodes. Each node must contain next attributes:
	 *  id - If of node
	 *  name - Name of none
	 *  hasChildren - boolean has node children
	 *  children - get children array
	 */
	public $data = array();

	/**
	 * @var array jstree htmlOptions
	 */
	public $treeHtmlOptions = [];

	/**
	 * @var array jstree item htmlOptions
	 */
	public $itemHtmlOptions = [];

	/**
	 * @var array jstree options
	 */
	public $options = array();

	/**
	 * @var CClientScript
	 */
	protected $cs;

	public $multilang = false;

	/**
	 * Initialize widget
	 */
	public function init()
	{
		$assetsUrl = Yii::app()->getAssetManager()->publish(
			Yii::getPathOfAlias('core.extensions.jstree.assets'),
			false,
			-1,
			YII_DEBUG
		);

		Yii::app()->getClientScript()->registerPackage('cookie');

		$this->cs = Yii::app()->getClientScript();
		$this->cs->registerScriptFile($assetsUrl.'/jquery.jstree.js');
		$this->cs->registerScriptFile($assetsUrl.'/js/tree.js');
	}

	public function run()
	{
		echo CHtml::openTag('div', CMap::mergeArray([
			'id' => $this->id,
			'data-tree' => true
		], $this->treeHtmlOptions));
		echo CHtml::openTag('ul');
		
		call_user_func([$this, "create".($this->multilang ? 'Multilang' : '')."HtmlTree"], ($this->data));
		
		echo CHtml::closeTag('ul');
		echo CHtml::closeTag('div');

		$options = CJavaScript::encode($this->options);

		$this->cs->registerScript('JsTreeScript', "$('#{$this->id}').jstree({$options});");
	}

	/**
	 * Create ul html tree from data array
	 * @param string $data
	 */
	private function createHtmlTree($data)
	{
		if(!is_array($data)) $data = [$data];
		
		foreach($data as $node)
		{
			echo CHtml::openTag('li', CMap::mergeArray([
				'id' => $this->id.'Node_'.$node['id'],
				'data-id' => $node['id']
			], $this->itemHtmlOptions));

			echo CHtml::link(CHtml::encode($node->name));
			
			if ($node->children()->count() > 0)
			{
				echo CHtml::openTag('ul');
				$this->createHtmlTree($node->children()->findAll());
				echo CHtml::closeTag('ul');
			}
			echo CHtml::closeTag('li');
		}
	}


	/**
	 * Create ul html tree from data array
	 * @param string $data
	 */
	private function createMultilangHtmlTree($data)
	{
		if(!is_array($data)) $data = [$data];

		foreach($data as $node)
		{
			echo CHtml::openTag('li', CMap::mergeArray([
				'id' => $this->id.'Node_'.$node['id'],
				'data-id' => $node['id']
			], $this->itemHtmlOptions));
			
			$title = '';
			$langs = param('languages');
			$defLang = param('defaultLanguage');
			
			foreach($langs as $key => $lang){
				$title .= CHtml::tag('span', ['class' => 'multilang '.$key.($key != $defLang ? ' hidden' : '')], CHtml::encode($node->{'name_'.$key}));
			}

			echo CHtml::link($title);
			
			if ($node->children()->count() > 0)
			{
				echo CHtml::openTag('ul');
				$this->createMultilangHtmlTree($node->children()->multilang()->findAll());
				echo CHtml::closeTag('ul');
			}
			echo CHtml::closeTag('li');
		}
	}

}
