<?php 


class AjaxSlugAction extends CAction{

	public function run(){
		$string = Yii::app()->request->getParam('string');
		$model = Yii::app()->request->getParam('model');

		if($model){
			$model = new $model;

			if(!isset($model->slugger)){
				$model->attachBehavior('slugger', [
					'class' => 'core.behaviors.SlugBehavior'
				]);
			}

			echo $model->slugger->makeSlug($string);

			Yii::app()->end();
			
		}
		
		echo '';
		
	}
}