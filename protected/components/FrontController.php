<?php


/**
* FrontController
*/
class FrontController extends FController{

	public function behaviors(){
		return [
			//действия для связанные с корзиной
			'hends.widgets.cart.behaviors.CartActions',
		];
	}

	public function init(){
		parent::init();

		app()->theme = 'megatron';

		$this->registerStyles();
		$this->registerScripts();
	}

	private function registerStyles(){

	}

	public function registerScripts(){
		cs()->registerScriptFile($this->coreAssetsUrl .'/js/main/main-functions.js');
		cs()->registerScriptFile($this->coreAssetsUrl .'/js/main/small-plugins.js');
	}
}
